@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold mt-2 text-gray-800 dark:text-gray-300 text-center">Feedback
                <span class="text-primary">
                    Details
                </span>
            </h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 border-2 border-primary shadow-lg rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4 dark:text-white">Message</h2>
                    <div class="prose dark:prose-invert max-w-none">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $feedback->message }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 border-2 border-primary">
                    <h2 class="text-xl font-semibold mb-4 dark:text-white">Timeline</h2>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Feedback Submitted</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $feedback->created_at->format('M d, Y \a\t H:i') }}
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        ({{ $feedback->created_at->diffForHumans() }})
                                    </span>
                                </p>
                            </div>
                        </div>

                        @if ($feedback->updated_at != $feedback->created_at)
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Last Updated</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $feedback->updated_at->format('M d, Y \a\t H:i') }}
                                        <span class="text-xs text-gray-400 dark:text-gray-500">
                                            ({{ $feedback->updated_at->diffForHumans() }})
                                        </span>
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 border-2 border-primary">
                    <h2 class="text-lg font-semibold mb-4 dark:text-white">Status</h2>
                    <form method="POST" action="{{ route('feedback.updateStatus', $feedback) }}">
                        @csrf
                        @method('PATCH')
                        <select name="status" onchange="this.form.submit()"
                            class="w-full px-3 py-2 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white cursor-pointer focus:ring-2 focus:ring-primary">
                            <option value="pending" {{ $feedback->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="reviewed" {{ $feedback->status == 'reviewed' ? 'selected' : '' }}>Reviewed
                            </option>
                            <option value="resolved" {{ $feedback->status == 'resolved' ? 'selected' : '' }}>Resolved
                            </option>
                            <option value="archived" {{ $feedback->status == 'archived' ? 'selected' : '' }}>Archived
                            </option>
                        </select>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 border-2 border-primary">
                    <h2 class="text-lg font-semibold mb-4 dark:text-white">Details</h2>

                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">ID</p>
                            <p class="font-medium dark:text-white">#{{ $feedback->id }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
                            <span
                                class="inline-block px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded text-sm font-medium">
                                {{ ucfirst($feedback->feedback_type) }}
                            </span>
                        </div>

                        @if ($feedback->rating)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Rating</p>
                                <div class="flex items-center gap-1 mt-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                    <span class="ml-2 text-sm font-medium dark:text-white">{{ $feedback->rating }}/5</span>
                                </div>
                            </div>
                        @endif

                        {{-- reCAPTCHA Score --}}
                        @if ($feedback->recaptcha_score)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">reCAPTCHA Score</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span
                                        class="inline-block px-2 py-1 rounded text-sm font-medium
                                    {{ $feedback->recaptcha_score >= 0.7 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                    {{ $feedback->recaptcha_score >= 0.5 && $feedback->recaptcha_score < 0.7 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                    {{ $feedback->recaptcha_score < 0.5 ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                        {{ number_format($feedback->recaptcha_score, 2) }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        @if ($feedback->recaptcha_score >= 0.7)
                                            (High Trust)
                                        @elseif($feedback->recaptcha_score >= 0.5)
                                            (Medium Trust)
                                        @else
                                            (Low Trust)
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
