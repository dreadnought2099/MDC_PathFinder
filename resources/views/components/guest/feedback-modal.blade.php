<!-- Floating Feedback Button -->
<button onclick="openFeedbackModal()"
    class="floating-btn fixed bottom-6 right-6 bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-full shadow-lg flex items-center gap-2 z-50 transition-all duration-300">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
    </svg>
    <span class="font-medium">Feedback</span>
</button>

<!-- Feedback Modal -->
<div id="feedbackModal"
    class="modal-overlay modal-hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div
        class="modal-content bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div
            class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-primary">We'd Love Your Feedback</h2>
            <button onclick="closeFeedbackModal()"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Error Message -->
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <form action="{{ route('feedback.store') }}" method="POST" id="feedbackForm">
                @csrf

                <!-- Hidden field for page URL -->
                <input type="hidden" name="page_url" value="{{ url()->current() }}">

                <!-- Feedback Type -->
                <div class="mb-5">
                    <label for="feedback_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Feedback Type
                    </label>
                    <select name="feedback_type" id="feedback_type" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white transition">
                        <option value="general">General Feedback</option>
                        <option value="bug">Bug Report</option>
                        <option value="feature">Feature Request</option>
                        <option value="navigation">Navigation Issue</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Improved Star Rating -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Rate Your Experience (Optional)
                    </label>
                    <div class="star-rating flex gap-2" id="starRating">
                        @for ($i = 1; $i <= 5; $i++)
                            <label
                                class="star-label cursor-pointer transition-transform hover:scale-125 active:scale-95"
                                data-rating="{{ $i }}">
                                <input type="radio" name="rating" value="{{ $i }}" class="hidden">
                                <svg class="star-icon w-10 h-10 text-gray-300 transition-all duration-200"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </label>
                        @endfor
                    </div>
                    <p class="text-xs text-gray-500 mt-2" id="ratingText">Click to rate</p>
                    @error('rating')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Feedback Message -->
                <div class="mb-5">
                    <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Your Feedback <span class="text-red-500">*</span>
                    </label>
                    <textarea name="message" id="message" rows="5" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none transition"
                        placeholder="Tell us what you think..." minlength="10" maxlength="1000">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">
                        <span id="charCount">0</span> / 1000 characters (minimum 10)
                    </p>
                </div>

                <!-- reCAPTCHA v3 (invisible) -->
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                <!-- Submit Button -->
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="closeFeedbackModal()"
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Cancel
                    </button>
                    <button type="submit" id="submitBtn"
                        class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Submit Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Load reCAPTCHA v3 Script -->
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>

<style>
    /* Modal animations */
    .modal-overlay {
        transition: opacity 0.3s ease;
    }

    .modal-content {
        transition: transform 0.3s ease, opacity 0.3s ease;
    }

    .modal-hidden {
        opacity: 0;
        pointer-events: none;
    }

    .modal-hidden .modal-content {
        transform: scale(0.95);
        opacity: 0;
    }

    /* Floating button pulse animation */
    @keyframes pulse-subtle {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }

    .floating-btn:hover {
        animation: pulse-subtle 1s infinite;
    }
</style>

<script>
    // Modal functions
    function openFeedbackModal() {
        const modal = document.getElementById('feedbackModal');
        modal.classList.remove('modal-hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeFeedbackModal() {
        const modal = document.getElementById('feedbackModal');
        modal.classList.add('modal-hidden');
        document.body.style.overflow = '';
    }

    // Close modal on outside click
    document.getElementById('feedbackModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeFeedbackModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('feedbackModal').classList.contains(
            'modal-hidden')) {
            closeFeedbackModal();
        }
    });

    // Auto-open modal if there's a success/error message
    @if (session('success') || session('error'))
        document.addEventListener('DOMContentLoaded', function() {
            openFeedbackModal();
            @if (session('success'))
                setTimeout(() => {
                    closeFeedbackModal();
                }, 3000);
            @endif
        });
    @endif

    // Star rating functionality
    const starRating = document.getElementById('starRating');
    const ratingText = document.getElementById('ratingText');
    const ratingLabels = ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
    let selectedRating = 0;

    starRating.addEventListener('click', function(e) {
        const label = e.target.closest('.star-label');
        if (label) {
            const rating = parseInt(label.dataset.rating);
            selectedRating = rating;
            label.querySelector('input').checked = true;
            updateStarDisplay(rating);
            ratingText.textContent = ratingLabels[rating - 1];
        }
    });

    // Hover effect for stars
    starRating.addEventListener('mouseover', function(e) {
        const label = e.target.closest('.star-label');
        if (label) {
            const rating = parseInt(label.dataset.rating);
            updateStarDisplay(rating, true);
        }
    });

    starRating.addEventListener('mouseout', function() {
        updateStarDisplay(selectedRating);
    });

    function updateStarDisplay(rating, isHover = false) {
        const stars = starRating.querySelectorAll('.star-icon');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.style.color = isHover ? '#FDE047' : '#FBBF24'; // yellow-300 or yellow-400
            } else {
                star.style.color = '#D1D5DB'; // gray-300
            }
        });

        if (rating === 0) {
            ratingText.textContent = 'Click to rate';
        }
    }

    // Character counter
    const messageInput = document.getElementById('message');
    const charCount = document.getElementById('charCount');

    messageInput.addEventListener('input', updateCharCount);

    function updateCharCount() {
        const count = messageInput.value.length;
        charCount.textContent = count;

        if (count < 10) {
            charCount.classList.add('text-red-500');
            charCount.classList.remove('text-gray-500', 'text-green-500');
        } else if (count >= 1000) {
            charCount.classList.add('text-red-500');
            charCount.classList.remove('text-gray-500', 'text-green-500');
        } else {
            charCount.classList.add('text-green-500');
            charCount.classList.remove('text-gray-500', 'text-red-500');
        }
    }

    // Initialize character counter
    updateCharCount();

    // reCAPTCHA integration
    document.getElementById('feedbackForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';

        grecaptcha.ready(function() {
            grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {
                    action: 'feedback'
                })
                .then(function(token) {
                    document.getElementById('g-recaptcha-response').value = token;
                    document.getElementById('feedbackForm').submit();
                })
                .catch(function(error) {
                    console.error('reCAPTCHA error:', error);
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Feedback';
                });
        });
    });
</script>