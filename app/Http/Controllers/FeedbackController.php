<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    /**
     * Show the feedback form
     */
    public function create()
    {
        return view('pages.client.feedback.create');
    }

    /**
     * Store feedback with reCAPTCHA verification
     */
    public function store(StoreFeedbackRequest $request)
    {
        // Verify reCAPTCHA
        $recaptchaVerified = $this->verifyRecaptcha($request->input('g-recaptcha-response'));

        if (!$recaptchaVerified['success']) {
            return back()
                ->withInput()
                ->with('error', 'reCAPTCHA verification failed. Please try again.');
        }

        // Check reCAPTCHA score (v3 only)
        $score = $recaptchaVerified['score'] ?? 1.0;
        $minScore = config('services.recaptcha.min_score', 0.5);

        if ($score < $minScore) {
            Log::warning('Low reCAPTCHA score', [
                'score' => $score,
                'ip' => $request->ip(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Suspicious activity detected. Please try again.');
        }

        // Store feedback
        try {
            Feedback::create([
                'message' => $request->input('message'),
                'rating' => $request->input('rating'),
                'feedback_type' => $request->input('feedback_type', 'general'),
                'page_url' => $request->input('page_url') ?? url()->previous(),
                'ip_hash' => hash('sha256', $request->ip()),
                'recaptcha_score' => $score,
                'status' => 'pending',
            ]);

            return redirect()
                ->back()
                ->with('success', 'Thank you for your feedback! We appreciate your input.');
        } catch (\Exception $e) {
            Log::error('Failed to store feedback', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Failed to submit feedback. Please try again.');
        }
    }

    /**
     * Verify reCAPTCHA token
     */
    private function verifyRecaptcha(string $token): array
    {
        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

            $result = $response->json();

            return [
                'success' => $result['success'] ?? false,
                'score' => $result['score'] ?? null, // Only in v3
                'action' => $result['action'] ?? null,
                'challenge_ts' => $result['challenge_ts'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification error', [
                'error' => $e->getMessage(),
            ]);

            return ['success' => false];
        }
    }

    /**
     * Admin: View all feedback with filters and analytics
     */
    public function index(Request $request)
    {
        $query = Feedback::query();

        // Search (compatible with search-sort component)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                    ->orWhere('page_url', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('feedback_type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by reCAPTCHA score range
        if ($request->filled('score_min')) {
            $query->where('recaptcha_score', '>=', $request->score_min);
        }
        if ($request->filled('score_max')) {
            $query->where('recaptcha_score', '<=', $request->score_max);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting (compatible with both sort/direction and sort_by/sort_order)
        $sortBy = $request->get('sort', $request->get('sort_by', 'created_at'));
        $sortOrder = $request->get('direction', $request->get('sort_order', 'desc'));
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->get('per_page', 20);
        $feedback = $query->paginate($perPage)->withQueryString();

        // Analytics (only for full page load, not AJAX)
        if (!$request->ajax() && !$request->wantsJson()) {
            $analytics = $this->getFeedbackAnalytics($request);
            return view('pages.admin.feedback.index', compact('feedback', 'analytics'));
        }

        // AJAX request - return only table HTML
        $html = view('pages.admin.feedback.partials.feedback-table', compact('feedback'))->render();
        return response()->json(['html' => $html]);
    }

    /**
     * Get feedback analytics
     */
    private function getFeedbackAnalytics(Request $request)
    {
        $query = Feedback::query();

        // Apply same filters as index
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return [
            'total_count' => $query->count(),
            'average_rating' => round($query->avg('rating'), 2),
            'average_score' => round($query->avg('recaptcha_score'), 2),
            'by_status' => $query->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status'),
            'by_type' => $query->select('feedback_type', DB::raw('count(*) as count'))
                ->groupBy('feedback_type')
                ->pluck('count', 'feedback_type'),
            'by_rating' => $query->select('rating', DB::raw('count(*) as count'))
                ->whereNotNull('rating')
                ->groupBy('rating')
                ->orderBy('rating')
                ->pluck('count', 'rating'),
            'recent_trend' => $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];
    }

    /**
     * Show single feedback detail
     */
    public function show(Feedback $feedback)
    {
        return view('pages.admin.feedback.show', compact('feedback'));
    }

    /**
     * Update feedback status
     */
    public function updateStatus(Request $request, Feedback $feedback)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,resolved,archived',
        ]);

        $feedback->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Feedback status updated successfully.');
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'feedback_ids' => 'required|array',
            'feedback_ids.*' => 'exists:feedback,id',
            'status' => 'required|in:pending,reviewed,resolved,archived',
        ]);

        Feedback::whereIn('id', $request->feedback_ids)
            ->update(['status' => $request->status]);

        return back()->with('success', 'Feedback items updated successfully.');
    }

    /**
     * Bulk delete feedback
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'feedback_ids' => 'required|array',
            'feedback_ids.*' => 'exists:feedback,id',
        ]);

        Feedback::whereIn('id', $request->feedback_ids)->delete();

        return back()->with('success', 'Feedback items deleted successfully.');
    }

    /**
     * Delete feedback
     */
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();
        return back()->with('success', 'Feedback deleted successfully.');
    }

    /**
     * Export feedback to CSV
     */
    public function export(Request $request)
    {
        $query = Feedback::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('message', 'like', "%{$search}%")
                ->orWhere('page_url', 'like', "%{$search}%");
        }
        if ($request->filled('type')) {
            $query->where('feedback_type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $feedback = $query->orderBy('created_at', 'desc')->get();

        $filename = 'feedback_export_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($feedback) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'ID',
                'Date',
                'Type',
                'Rating',
                'Message',
                'Page URL',
                'reCAPTCHA Score',
                'Status',
            ]);

            // Data rows
            foreach ($feedback as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->created_at->format('Y-m-d H:i:s'),
                    $item->feedback_type,
                    $item->rating ?? 'N/A',
                    $item->message,
                    $item->page_url,
                    $item->recaptcha_score,
                    $item->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
