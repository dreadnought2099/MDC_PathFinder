<div x-data="{
    open: false,
    clearFeedbackMessage() {
        document.getElementById('flash-message')?.remove();
        document.getElementById('temp-message')?.remove();
    }
    }" @keydown.escape.window="open = false" class="relative group"
        @close-modal.window="open = false; clearFeedbackMessage()">

    <!-- Trigger Button -->
    <button @click="open = true"
        class="flex items-center justify-center w-10 h-10 rounded-full transition-all duration-300 cursor-pointer">
        <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/feedback.png"
            alt="Feedback Icon" class="w-8 h-8 sm:w-10 sm:h-10 hover:scale-110 transition-transform duration-300">
    </button>

    <!-- Tooltip -->
    <div
        class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium 
               text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible
               group-hover:opacity-100 group-hover:visible transition-all duration-300 
               whitespace-nowrap dark:bg-gray-700 pointer-events-none font-sofia">
        Send Feedback
        <div
            class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 
                   border-l-4 border-l-gray-900 dark:border-l-gray-700
                   border-t-4 border-t-transparent 
                   border-b-4 border-b-transparent">
        </div>
    </div>

    <!-- Modal Overlay -->
    <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click="open = false; $nextTick(() => { clearFeedbackMessage() })" style="display: none;">

        <!-- Modal Content -->
        <div @click.stop x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-90"
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto relative border-2 border-primary">

            <!-- Header -->
            <div
                class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-primary dark:!text-gray-300 text-center flex-1">We'd Love Your Feedback</h2>
                <button @click="open = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6">
                <!-- Form -->
                <form action="{{ route('feedback.store') }}" method="POST" id="feedbackForm" x-data="feedbackForm()"
                    @submit.prevent="submit">
                    @csrf
                    <input type="hidden" name="page_url" value="{{ url()->current() }}">
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                    <input type="hidden" name="rating" :value="rating">

                    <!-- Flash Message -->
                    <template x-if="flashMessage">
                        <div x-show="flashMessage" x-transition.opacity.duration.500 class="mt-4 p-4 rounded-lg"
                            :class="flashClass" x-text="flashMessage" x-init="setTimeout(() => { flashMessage = '' }, 4000)">
                        </div>
                    </template>

                    <!-- Feedback Type -->
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Feedback Type
                        </label>
                        <select name="feedback_type" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="general">General Feedback</option>
                            <option value="bug">Bug Report</option>
                            <option value="feature">Feature Request</option>
                            <option value="navigation">Navigation Issue</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Rating -->
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Rate Your Experience (Optional)
                        </label>
                        <div class="flex gap-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="cursor-pointer" @mouseover="hover = {{ $i }}"
                                    @mouseleave="hover = null" @click="rating = {{ $i }}">
                                    <svg class="w-8 h-8 transition-colors" fill="currentColor" viewBox="0 0 20 20"
                                        :class="(hover >= {{ $i }} || rating >= {{ $i }}) ?
                                        'text-yellow-400' : 'text-gray-300'">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588
                                            1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755
                                            1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8
                                            2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1
                                            1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1
                                            1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </span>
                            @endfor
                        </div>
                        <p class="text-xs text-gray-500 mt-2"
                            x-text="rating ? 'You rated: ' + rating + ' star' + (rating > 1 ? 's' : '') : 'Click to rate'">
                        </p>
                    </div>

                    <!-- Message -->
                    <div class="mb-5">
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Your Feedback <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message" id="message" rows="5" x-model="message"
                            x-on:input="canSubmit = message.trim().length >= 10" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none"
                            placeholder="Tell us what you think..." minlength="10" maxlength="1000"></textarea>
                        <p class="text-xs mt-1">
                            <span :class="message.trim().length >= 10 ? 'text-green-500 font-bold' : 'text-red-500'">
                                <span x-text="message.length"></span> / 1000 characters
                                <span x-show="message.trim().length < 10 && message.length > 0" class="ml-2">
                                    (minimum 10 characters)
                                </span>
                            </span>
                        </p>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="$dispatch('close-modal')"
                            class="w-full sm:w-auto px-4 py-2 text-sm font-medium border-2 border-gray-400 text-white bg-gray-400 hover:text-gray-500 hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-gray-300 shadow-cancel-hover">
                            Cancel
                        </button>
                        <button type="submit" :disabled="!canSubmit || isSubmitting"
                            class="bg-primary text-white text-sm font-medium px-4 py-2 rounded-md hover:text-primary border-2 border-primary hover:bg-white transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 shadow-primary-hover disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isSubmitting">Submit Feedback</span>
                            <span x-show="isSubmitting">Submitting...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function feedbackForm() {
        return {
            message: '{{ old('message') }}',
            rating: null,
            hover: null,
            canSubmit: false,
            isSubmitting: false,
            flashMessage: '',
            flashClass: '',
            lastSubmit: 0,

            async submit(event) {
                const now = Date.now();
                if (now - this.lastSubmit < 5000) {
                    alert('Please wait a few seconds before submitting again.');
                    return;
                }
                if (!this.canSubmit) {
                    alert('Please enter at least 10 characters in your feedback.');
                    return;
                }

                this.isSubmitting = true;
                this.lastSubmit = now;

                try {
                    let token = '';
                    if (typeof grecaptcha !== 'undefined') {
                        token = await grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {
                            action: 'feedback'
                        });
                        document.getElementById('g-recaptcha-response').value = token;
                    }

                    const formData = new FormData(event.target);

                    const response = await fetch('{{ route('feedback.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });

                    const text = await response.text();

                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        console.error('Server returned non-JSON:', text);
                        this.flashClass = 'bg-red-100 text-red-700 border border-red-500';
                        this.flashMessage = 'Unexpected server response. Please try again.';
                        this.isSubmitting = false;
                        return;
                    }

                    if (data.errors) {
                        const firstError = Object.values(data.errors)[0][0];
                        this.flashClass = 'bg-red-100 text-red-700 border border-red-500';
                        this.flashMessage = firstError;
                        this.isSubmitting = false;
                        return;
                    }

                    if (data.success) {
                        this.flashClass = 'bg-green-100 text-green-700 border border-green-500';
                        this.message = '';
                        this.rating = null;
                        this.launchConfetti(); // Trigger confetti
                    } else {
                        this.flashClass = 'bg-red-100 text-red-700 border border-red-500';
                    }

                    this.flashMessage = data.message || 'An error occurred.';
                    setTimeout(() => {
                        this.flashMessage = ''
                    }, 4000);

                } catch (err) {
                    console.error(err);
                    this.flashClass = 'bg-red-100 text-red-700 border border-red-500';
                    this.flashMessage = 'Submission failed. Please try again.';
                    setTimeout(() => {
                        this.flashMessage = ''
                    }, 4000);
                } finally {
                    this.isSubmitting = false;
                }
            },

            launchConfetti() {
                confetti({
                    particleCount: 50,
                    spread: 70,
                    origin: {
                        y: 0.6
                    },
                    colors: ['#4ade80', '#22d3ee', '#facc15', '#f472b6'],
                });
            }
        }
    }
</script>
