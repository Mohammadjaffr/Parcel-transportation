<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-6 text-center space-y-1">
            <h2 class="text-2xl sm:text-3xl  tracking-tight text-gray-900 font-bold dark:text-white">
                {{ __('تسجيل الدخول إلى حسابك') }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                أدخل رقم الجوال وكلمة المرور للمتابعة
            </p>
        </div>

        <!-- Phone Address -->
        <div x-data="{
            selectedCountry: { name: 'Yemen', code: 'YE', dial_code: '+967' },
            localPhoneNumber: '{{ old('phone') }}'.startsWith('967') ? '{{ old('phone') }}'.substring(3) : '{{ old('phone') }}'
        }" class="group">
            <x-input-label for="phone_display" :value="__('رقم الجوال')"
                class="group-focus-within:text-brand-500 transition-colors duration-200" />

            <div class="relative mt-1.5">
                <input type="hidden" name="phone"
                    :value="selectedCountry.dial_code.replace('+', '') + localPhoneNumber">

                <div
                    class="flex h-12 w-full rounded-xl border border-gray-200 dark:border-gray-700
           bg-white dark:bg-gray-900 shadow-sm overflow-hidden
           transition focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10">

                    <!-- Phone input -->
                    <input id="phone_display" type="tel" x-model="localPhoneNumber" placeholder="780236551" required
                        autofocus dir="ltr" inputmode="numeric"
                        class="flex-1 bg-transparent px-4
               text-sm font-medium text-gray-900 dark:text-white
               placeholder:text-gray-400
               focus:outline-none border-0">
                    <!-- Country / Dial code -->
                    <div
                        class="flex items-center gap-2 px-4
               bg-gray-50 dark:bg-gray-800
               border-r border-gray-200 dark:border-gray-700">

                        <img :src="`https://flagcdn.com/w20/${selectedCountry.code.toLowerCase()}.png`" alt="Flag"
                            class="w-5 h-auto rounded-sm shadow-sm">

                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300 font-outfit"
                            x-text="selectedCountry.dial_code"></span>
                    </div>

                </div>

            </div>
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('كلمة المرور')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-orange-400 text-orange-600 shadow-sm orange-focus orange-checkbox"
                    name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('تذكرني') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            {{-- @if (Route::has('password.request'))
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('password.request') }}">
                {{ __('Forgot your password?') }}
            </a>
            @endif --}}

          <x-primary-button
    class="w-full h-12 ms-0
           flex items-center justify-center
           rounded-2xl
           bg-brand-600 hover:bg-brand-700
           text-white text-base font-black
           shadow-lg shadow-brand-600/25
           transition
           focus:outline-none focus:ring-4 focus:ring-brand-500/30">
    {{ __('تسجيل الدخول') }}
</x-primary-button>

        </div>
    </form>
</x-guest-layout>
