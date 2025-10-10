 @extends('layouts.app')

 @section('content')
     <div x-data="{
         showOtpModal: {{ session('show_2fa_modal') ? 'true' : 'false' }},
         showRecoveryModal: {{ session('show_recovery_modal') ? 'true' : 'false' }}
     }" x-cloak>

         <!-- OTP Modal -->
         <div x-show="showOtpModal"
             class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-2xl z-[60]">

             <div class="bg-white dark:bg-gray-800 p-6 rounded-md w-full max-w-md mx-4 shadow-xl">
                 <h2 class="text-lg font-bold text-primary text-center mb-4">Two-Factor Authentication</h2>

                 <form method="POST" action="{{ route('admin.2fa.verifyOTP') }}" class="space-y-4">
                     @csrf
                     <div>
                         <input type="text" name="otp" maxlength="6" inputmode="numeric" pattern="[0-9]*"
                             class="w-full py-4 text-center text-lg rounded-md dark:text-gray-300 text-gray-700 ring-1 px-4 ring-gray-400 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-colors duration-200"
                             placeholder="Enter 6-digit code" required autofocus x-ref="otpInput">
                     </div>

                     <button type="submit"
                         class="w-full bg-primary text-white px-6 py-3 rounded-md hover:bg-white hover:text-primary dark:hover:bg-gray-800 border-2 border-primary transition-all duration-300 cursor-pointer shadow-primary-hover">
                         Verify Code
                     </button>

                     <div class="text-center">
                         <button type="button" @click="showOtpModal = false; showRecoveryModal = true"
                             class="text-primary hover-underline text-sm cursor-pointer">
                             Use a recovery code instead
                         </button>
                     </div>
                 </form>
             </div>
         </div>

         <!-- Recovery Code Modal -->
         <div x-show="showRecoveryModal"
             class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-2xl z-[60]">

             <div class="bg-white dark:bg-gray-800 p-6 rounded-md w-full max-w-md mx-4 shadow-xl">
                 <h2 class="text-lg font-bold text-primary text-center mb-4">Use Recovery Code</h2>

                 <form method="POST" action="{{ route('admin.2fa.recovery.verify') }}" class="space-y-4">
                     @csrf
                     <div>
                         <input type="text" name="recovery_code"
                             class="w-full py-4 text-center text-lg rounded-md dark:text-gray-300 text-gray-700 ring-1 px-4 ring-gray-400 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-colors duration-200"
                             placeholder="Enter recovery code" required x-ref="recoveryInput">
                     </div>

                     <button type="submit"
                         class="w-full bg-primary text-white px-6 py-3 rounded-md hover:bg-white hover:text-primary dark:hover:bg-gray-800 border-2 border-primary transition-all duration-300 cursor-pointer shadow-primary-hover">
                         Verify Recovery Code
                     </button>

                     <div class="text-center">
                         <button type="button" @click="showRecoveryModal = false; showOtpModal = true"
                             class="text-primary hover-underline text-sm cursor-pointer">
                             Back to OTP
                         </button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 @endsection