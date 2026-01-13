<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

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
                    class="flex h-12 w-full rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-900 overflow-hidden transition-all duration-200 focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10 hover:border-gray-300 dark:hover:border-gray-600">
                    <div
                        class="flex items-center gap-2.5 px-4 bg-gray-50/50 dark:bg-gray-800/50 border-l border-gray-200 dark:border-gray-700">
                        <img :src="`https://flagcdn.com/w20/${selectedCountry.code.toLowerCase()}.png`" alt="Flag"
                            class="w-5 h-auto rounded-sm shadow-sm">
                        <span class="text-sm font-bold text-gray-600 dark:text-gray-400 font-outfit"
                            x-text="selectedCountry.dial_code"></span>
                    </div>

                    <input id="phone_display" type="tel" x-model="localPhoneNumber" placeholder="780236551" required
                        autofocus
                        class="flex-grow bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-0 border-none text-left font-medium placeholder:text-gray-400"
                        dir="ltr">
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

            <x-primary-button class="ms-3">
                {{ __('تسجيل الدخول') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>