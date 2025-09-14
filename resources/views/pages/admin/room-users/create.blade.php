@extends('layouts.app')

@section('content')
    <x-floating-actions />

    <div class="container max-w-lg mx-auto mt-10">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-6">Create <span class="text-primary">Office
                User</span></h1>

        <form action="{{ route('room-user.store') }}" method="POST" id="roomUserForm"
            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-gray-700 dark:text-gray-200 mb-2">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    class="w-full font-sofia border-2 border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-gray-100"
                    required>
                <p id="nameFeedback" class="text-sm mt-1"></p>
            </div>

            <!-- Username -->
            <div class="mb-4">
                <label for="username" class="block text-gray-700 dark:text-gray-200 mb-2">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}"
                    class="w-full font-sofia border-2 border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-gray-100"
                    required>
                <p id="usernameFeedback" class="text-sm mt-1"></p>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-gray-700 dark:text-gray-200 mb-2">Password</label>
                <input type="password" name="password" id="password"
                    class="w-full font-sofia border-2 border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-gray-100"
                    required>
                <div id="passwordStrength" class="h-2 mt-2 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                <p id="passwordFeedback" class="text-sm mt-1"></p>
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700 dark:text-gray-200 mb-2">Confirm
                    Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full font-sofia border-2 border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-gray-100"
                    required>
                <p id="confirmFeedback" class="text-sm mt-1"></p>
            </div>

            <!-- Room Selection -->
            <div class="mb-6">
                <label for="room_id" class="block text-gray-700 dark:text-gray-200 mb-2">Assign Room</label>
                <select name="room_id" id="room_id"
                    class="w-full border-2 border-gray-300 dark:border-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-gray-100"
                    required>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                class="bg-primary text-white px-6 py-3 rounded hover:bg-white hover:text-primary border-2 border-primary transition-all duration-300">
                Create User
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const usernameInput = document.getElementById('username');
            const usernameFeedback = document.getElementById('usernameFeedback');
            const nameFeedback = document.getElementById('nameFeedback');

            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            const passwordStrength = document.getElementById('passwordStrength');
            const passwordFeedback = document.getElementById('passwordFeedback');
            const confirmFeedback = document.getElementById('confirmFeedback');

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

            // Optional: Live username check via AJAX (requires route)
            // usernameInput.addEventListener('blur', () => {
            //     fetch(`/admin/users/check-username?username=${usernameInput.value}`)
            //         .then(res => res.json())
            //         .then(data => {
            //             if (data.exists) {
            //                 usernameFeedback.textContent = 'Username already taken';
            //                 usernameFeedback.className = 'text-red-500 text-sm mt-1';
            //             } else {
            //                 usernameFeedback.textContent = 'Username available';
            //                 usernameFeedback.className = 'text-green-500 text-sm mt-1';
            //             }
            //         });
            // });
        });
    </script>
@endpush
