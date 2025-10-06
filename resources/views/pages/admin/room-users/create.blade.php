@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <div class="container max-w-lg mx-auto mt-10 border-2 border-primary rounded-md dark:bg-gray-800">
        <h1 class="text-3xl font-bold text-center p-6 text-gray-800 dark:text-gray-300 mb-6">Create <span
                class="text-primary">Office
                User</span></h1>

        <form action="{{ route('room-user.store') }}" method="POST" id="roomUserForm"
            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
            @csrf


            @php
                $inputClasses =
                    'peer py-3 w-full placeholder-transparent font-sofia rounded-md text-gray-700 dark:text-gray-300 ring-1 px-4 ring-gray-500 dark:ring-gray-500 focus:ring-2 focus:ring-primary focus:border-primary outline-none bg-white dark:bg-gray-800';

                $labelClasses =
                    'absolute cursor-text left-0 -top-3 text-sm font-sofia text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 mx-1 px-1 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 dark:peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-2 peer-focus:-top-3 peer-focus:text-primary peer-focus:text-sm peer-focus:bg-white dark:peer-focus:bg-gray-800 peer-focus:px-2 peer-focus:rounded-md';
            @endphp

            <!-- Room Selection -->
            <div class="relative mb-6">
                <label for="room_id" class="block font-sofia text-gray-400 dark:text-gray-300 mb-2">Assign Office</label>
                <select name="room_id" id="room_id"
                    class="w-full font-sofia border border-primary rounded-lg px-4 py-3 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                    required>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}"
                            {{ $selectedRoom && $selectedRoom->id == $room->id ? 'selected' : '' }}>
                            {{ $room->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Name -->
            <div class="relative mb-4">
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Name"
                    class="{{ $inputClasses }}">
                <p id="nameFeedback" class="text-sm mt-1"></p>
                <label for="name" class="{{ $labelClasses }}">Name</label>
            </div>

            <!-- Username -->
            <div class="relative mb-4">
                <input type="text" name="username" id="username" value="{{ old('username') }}" placeholder="Username"
                    class="{{ $inputClasses }}" required>
                <p id="usernameFeedback" class="text-sm mt-1"></p>
                <label for="username" class="{{ $labelClasses }}">Username</label>
            </div>

            <!-- Password -->
            <div class="relative mb-4">
                <input type="password" name="password" id="password" class="{{ $inputClasses }}" placeholder="Password"
                    required>
                <label for="password" class="{{ $labelClasses }}">Password</label>
                
                <button type="button" onclick="togglePassword('password')"
                    class="absolute right-3 top-3 text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-gray-200">
                    <svg id="password-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="password-eye-slash" class="w-5 h-5 hidden" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
                <div id="passwordStrength" class="h-2 mt-2 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                <p id="passwordFeedback" class="text-sm mt-1"></p>
            </div>

            <!-- Confirm Password -->
            <div class="relative mb-4">
                <input type="password" name="password_confirmation" id="password_confirmation"
                    placeholder="Confirm Password" class="{{ $inputClasses }}" required>
                <label for="password_confirmation" class="{{ $labelClasses }}">Confirm Password</label>

                <button type="button" onclick="togglePassword('password_confirmation')"
                    class="absolute right-3 top-3 text-gray-500 hover:text-primary dark:text-gray-400 dark:hover:text-gray-200">
                    <svg id="password_confirmation-eye" class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="password_confirmation-eye-slash" class="w-5 h-5 hidden" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
                <p id="confirmFeedback" class="text-sm mt-1"></p>
            </div>

            <button type="submit"
                class="w-full bg-primary text-white px-6 py-3 rounded-md hover:bg-white hover:text-primary dark:hover:bg-gray-800 border-2 border-primary transition-all duration-300 cursor-pointer shadow-primary-hover">
                Create User
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(`${fieldId}-eye`);
            const eyeSlashIcon = document.getElementById(`${fieldId}-eye-slash`);

            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const usernameInput = document.getElementById('username');
            const usernameFeedback = document.getElementById('usernameFeedback');
            const nameFeedback = document.getElementById('nameFeedback');

            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            const passwordStrength = document.getElementById('passwordStrength');
            const passwordFeedback = document.getElementById('passwordFeedback');
            const confirmFeedback = document.getElementById('confirmFeedback');
            const roomSelect = document.getElementById('room_id');

            roomSelect.addEventListener('change', () => {
                const selectedId = roomSelect.value;
                const url = new URL(window.location);
                url.searchParams.set('roomId', selectedId);
                window.history.replaceState({}, '', url);
            });

            function checkPasswordStrength(password) {
                let score = 0;
                if (password.length >= 6) score++;
                if (/[A-Z]/.test(password)) score++;
                if (/[0-9]/.test(password)) score++;
                if (/[\W]/.test(password)) score++;
                return score;
            }

            function updateStrengthBar(score) {
                const colors = ['bg-red-500', 'bg-yellow-400', 'bg-yellow-500', 'bg-green-500'];
                passwordStrength.className = 'h-2 mt-2 rounded-full ' + (colors[score - 1] || 'bg-gray-200');
            }

            passwordInput.addEventListener('input', () => {
                const score = checkPasswordStrength(passwordInput.value);
                updateStrengthBar(score);

                if (score < 2) {
                    passwordFeedback.textContent = 'Weak password';
                    passwordFeedback.className = 'text-red-500 text-sm mt-1';
                } else if (score < 4) {
                    passwordFeedback.textContent = 'Medium strength';
                    passwordFeedback.className = 'text-yellow-500 text-sm mt-1';
                } else {
                    passwordFeedback.textContent = 'Strong password';
                    passwordFeedback.className = 'text-green-500 text-sm mt-1';
                }
            });

            confirmInput.addEventListener('input', () => {
                if (confirmInput.value !== passwordInput.value) {
                    confirmFeedback.textContent = 'Passwords do not match';
                    confirmFeedback.className = 'text-red-500 text-sm mt-1';
                } else {
                    confirmFeedback.textContent = 'Passwords match';
                    confirmFeedback.className = 'text-green-500 text-sm mt-1';
                }
            });
        });
    </script>
@endpush
