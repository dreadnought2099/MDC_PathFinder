<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Feedback;
use Illuminate\Http\Request;
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
     * Admin: View all feedback (optional)
     */
    public function index()
    {
        // Add authentication middleware to this route
        $feedback = Feedback::recent()->paginate(20);
        return view('pages.admin.feedback.index', compact('feedback'));
    }
}
