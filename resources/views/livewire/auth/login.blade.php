<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-100 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-10 left-10 w-32 h-32 rounded-full bg-blue-300 animate-pulse"></div>
        <div class="absolute top-32 right-20 w-24 h-24 rounded-full bg-indigo-300 animate-pulse delay-1000"></div>
        <div class="absolute bottom-20 left-1/4 w-28 h-28 rounded-full bg-cyan-300 animate-pulse delay-500"></div>
        <div class="absolute bottom-32 right-1/3 w-20 h-20 rounded-full bg-blue-400 animate-pulse delay-1500"></div>
    </div>

    <!-- Subtle Washing Machine Icons -->
    <div class="absolute inset-0 opacity-3">
        <svg class="absolute top-20 left-1/4 w-16 h-16 text-blue-200" fill="currentColor" viewBox="0 0 24 24">
            <path
                d="M18,2.01L6,2C4.89,2 4,2.89 4,4V20C4,21.11 4.89,22 6,22H18C19.11,22 20,21.11 20,20V4C20,2.89 19.11,2.01 18,2.01M18,20H6V16H18V20M6.5,3.5H7.5C7.78,3.5 8,3.72 8,4S7.78,4.5 7.5,4.5H6.5C6.22,4.5 6,4.28 6,4S6.22,3.5 6.5,3.5M9.5,3.5H10.5C10.78,3.5 11,3.72 11,4S10.78,4.5 10.5,4.5H9.5C9.22,4.5 9,4.28 9,4S9.22,3.5 9.5,3.5M12,6A6,6 0 0,1 18,12A6,6 0 0,1 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6M12,8A4,4 0 0,0 8,12A4,4 0 0,0 12,16A4,4 0 0,0 16,12A4,4 0 0,0 12,8Z" />
        </svg>
        <svg class="absolute bottom-40 right-1/4 w-14 h-14 text-indigo-200" fill="currentColor" viewBox="0 0 24 24">
            <path
                d="M12,2A3,3 0 0,1 15,5V11A3,3 0 0,1 12,14A3,3 0 0,1 9,11V5A3,3 0 0,1 12,2M19,11C19,14.53 16.39,17.44 13,17.93V21H11V17.93C7.61,17.44 5,14.53 5,11H7A5,5 0 0,0 12,16A5,5 0 0,0 17,11H19Z" />
        </svg>
    </div>

    <div class="relative z-20 flex min-h-screen items-center justify-center p-4 sm:p-6 lg:p-8">
        <!-- Wide Login Card with Reduced Height -->
        <div
            class="w-full max-w-md sm:max-w-lg md:max-w-2xl lg:max-w-4xl xl:max-w-5xl bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">

            <!-- Main Content Container - Reduced Padding -->
            <div class="px-6 py-4 sm:px-8 sm:py-6 lg:px-12 lg:py-8">

                <!-- Header Section - Reduced Margins (as per previous request) -->
                <div class="text-center mb-3 lg:mb-4">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full mb-2 shadow-md">
                        <svg class="w-8 h-8 lg:w-10 lg:h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M18,2.01L6,2C4.89,2 4,2.89 4,4V20C4,21.11 4.89,22 6,22H18C19.11,22 20,21.11 20,20V4C20,2.89 19.11,2.01 18,2.01M18,20H6V16H18V20M6.5,3.5H7.5C7.78,3.5 8,3.72 8,4S7.78,4.5 7.5,4.5H6.5C6.22,4.5 6,4.28 6,4S6.22,3.5 6.5,3.5M9.5,3.5H10.5C10.78,3.5 11,3.72 11,4S10.78,4.5 10.5,4.5H9.5C9.22,4.5 9,4.28 9,4S9.22,3.5 9.5,3.5M12,6A6,6 0 0,1 18,12A6,6 0 0,1 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6M12,8A4,4 0 0,0 8,12A4,4 0 0,0 12,16A4,4 0 0,0 16,12A4,4 0 0,0 12,8Z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800 mb-1">Florina Laundry Shop</h1>
                    <p class="text-base sm:text-md lg:text-lg text-gray-600">Management System</p>
                </div>

                <!-- Login Form Container -->
                <div class="max-w-md md:max-w-lg lg:max-w-xl xl:max-w-2xl mx-auto">
                    <!-- Status Messages -->
                    @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded-lg text-center">
                        {{ session('status') }}
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700">
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Login Form - Reduced Spacing -->
                    <form wire:submit="login" class="space-y-4">
                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email
                                address') }}</label>
                            <input wire:model.lazy="email" id="email" name="email" type="email" required autofocus
                                autocomplete="email" placeholder="you@example.com"
                                class="appearance-none block w-full px-4 py-3 text-base sm:text-lg border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Password')
                                }}</label>
                            <input wire:model.lazy="password" id="password" name="password" type="password" required
                                autocomplete="current-password" placeholder="{{ __('Password') }}"
                                class="appearance-none block w-full px-4 py-3 text-base sm:text-lg border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200">
                        </div>

                        <!-- Remember Me Checkbox -->
                        <div class="flex items-center">
                            <input wire:model.lazy="remember" id="remember_me" name="remember" type="checkbox"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="remember_me" class="ml-3 block text-sm text-gray-700">{{ __('Remember me')
                                }}</label>
                        </div>

                        <!-- Login Button - Reduced Padding -->
                        <div class="pt-2">
                            <button type="submit"
                                class="w-full flex justify-center items-center py-3 px-6 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200 ease-in-out transform hover:scale-[1.02]">
                                <span wire:loading.remove wire:target="login">
                                    {{ __('Log in') }}
                                </span>
                                <!-- MODIFIED SPAN FOR LOADING STATE -->
                                <span wire:loading wire:target="login" class="flex items-center justify-center">
                                    <svg class="animate-spin h-5 w-5 text-white" <!-- REMOVED -ml-1 mr-2 -->
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 100 101">
                                        <path
                                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                            fill="currentColor" class="opacity-25" />
                                        <path
                                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0492C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                            fill="currentColor" class="opacity-75" />
                                    </svg>
                                    <!-- "Processing..." text REMOVED -->
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Decorative Elements -->
    <div
        class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-blue-100/30 to-transparent pointer-events-none">
    </div>
    <div
        class="absolute top-0 right-0 w-64 h-64 bg-gradient-radial from-indigo-200/20 to-transparent rounded-full -translate-y-32 translate-x-32 pointer-events-none">
    </div>
</div>