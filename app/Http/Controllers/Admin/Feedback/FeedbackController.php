<?php

namespace App\Http\Controllers\Admin\Feedback;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    public function __construct()
    {
        // Public access for sending feedbacks in landing page
        $this->middleware('guest')->only(['create', 'store']);

        // Require authentication for admin methods (policy will check roles)
        $this->middleware('auth')->except(['create', 'store']);
    }


    /**
     * Admin: View all feedback with filters and analytics
     */
    public function index(Request $request)
    {
        // Policy authorization
        $this->authorize('viewAny', Feedback::class);

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

        // Paginate - added 10 as an option
        $perPage = $request->get('per_page', 10);
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
        // Policy authorization
        $this->authorize('view', $feedback);

        return view('pages.admin.feedback.show', compact('feedback'));
    }

    /**
     * Update feedback status
     */
    public function updateStatus(Request $request, Feedback $feedback)
    {
        // Policy authorization
        $this->authorize('update', $feedback);

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
        // Policy authorization - check if user can update ANY feedback
        $this->authorize('viewAny', Feedback::class);

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
        // Policy authorization - check if user can delete ANY feedback
        $this->authorize('viewAny', Feedback::class);

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
        // Policy authorization
        $this->authorize('delete', $feedback);

        $feedback->delete();
        return back()->with('success', 'Feedback deleted successfully.');
    }

    /**
     * Restore a soft-deleted feedback
     */
    public function restore($id)
    {
        $feedback = Feedback::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', Feedback::class);

        $feedback->restore();

        $tab = request()->input('tab', 'feedback');

        return redirect()->route('recycle-bin', ['tab' => $tab])
            ->with('success', 'Feedback restored successfully.');
    }

    /**
     * Permanently delete a feedback
     */
    public function forceDelete($id)
    {
        $feedback = Feedback::onlyTrashed()->findOrFail($id);

        $this->authorize('forceDelete', Feedback::class);

        $feedback->forceDelete();

        $tab = request()->input('tab', 'feedback');

        return redirect()->route('recycle-bin', ['tab' => $tab])
            ->with('success', 'Feedback permanently deleted.');
    }
    
    /**
     * Export feedback to CSV
     */
    public function export(Request $request)
    {
        ob_end_clean(); // Prevent stray output

        // Policy authorization
        $this->authorize('viewAny', Feedback::class);

        $query = Feedback::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                    ->orWhere('page_url', 'like', "%{$search}%");
            });
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
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($feedback) {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

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

        if ($feedback->isEmpty()) {
            return back()->with('error', 'No feedback data found to export.');
        }

        return response()->stream($callback, 200, $headers);
    }
}
