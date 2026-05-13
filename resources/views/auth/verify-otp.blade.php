<x-guest-layout>
    <x-slot name="title">التحقق من رقم الهاتف</x-slot>

    @php
        $currentPhone = $phone ?? session('verify_phone');

        $lockoutKey = 'verify-otp:' . $currentPhone;
        $resendKey = 'resend-otp:' . $currentPhone;

        $lockSeconds = \Illuminate\Support\Facades\RateLimiter::availableIn($lockoutKey);
        $resendSeconds = \Illuminate\Support\Facades\RateLimiter::availableIn($resendKey);

        $isLocked = \Illuminate\Support\Facades\RateLimiter::tooManyAttempts($lockoutKey, 3);

        // 💡 الاعتماد الكلي على السيرفر (بدون أرقام افتراضية)
        $initialSeconds = max($lockSeconds, $resendSeconds);
    @endphp

    <div class="flex flex-col justify-center items-center px-4 min-h-screen bg-slate-50 font-body" dir="rtl">

        <div
            class="w-full max-w-md bg-white rounded-[2rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] border border-slate-100 p-6 sm:p-8 relative overflow-hidden">

            <div
                class="absolute -top-12 -right-12 w-32 h-32 rounded-full blur-3xl pointer-events-none {{ $isLocked ? 'bg-rose-500/10' : 'bg-primary/5' }}">
            </div>

            <div class="relative z-10 mb-8 text-center">
                <div
                    class="flex justify-center items-center mx-auto mb-5 w-16 h-16 rounded-2xl shadow-sm {{ $isLocked ? 'bg-rose-50 text-rose-500' : 'bg-emerald-50 text-emerald-500' }}">
                    <span
                        class="material-symbols-outlined text-[32px]">{{ $isLocked ? 'lock' : 'phonelink_ring' }}</span>
                </div>
                <h1 class="mb-3 text-2xl font-black text-slate-800 font-headline">كود التحقق</h1>
                <p class="text-sm font-bold leading-relaxed text-slate-500">
                    أدخل الكود المكون من 6 أرقام المرسل إلى الواتساب الخاص بالرقم
                    <br>
                <div class="flex justify-center w-full mt-2">
                    <span
                        class="inline-flex gap-1.5 items-center px-3 py-1.5 font-mono rounded-lg text-primary bg-primary/5 dir-ltr">
                        <span class="material-symbols-outlined text-[16px]">phone</span>
                        <x-phone-number :value="$currentPhone" />
                    </span>
                </div>
                </p>
            </div>

            @if ($errors->any() || session('error'))
                <div class="flex gap-3 items-start p-4 mb-6 bg-rose-50 rounded-2xl border border-rose-100">
                    <span class="text-rose-500 material-symbols-outlined">error</span>
                    <p class="mt-0.5 text-sm font-bold text-rose-600">{{ session('error') ?? $errors->first() }}</p>
                </div>
            @endif

            @if (session('success'))
                <div class="flex gap-3 items-start p-4 mb-6 bg-emerald-50 rounded-2xl border border-emerald-100">
                    <span class="text-emerald-500 material-symbols-outlined">check_circle</span>
                    <p class="mt-0.5 text-sm font-bold text-emerald-600">{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('otp.verify') }}" method="POST" id="otp-form" class="relative z-10">
                @csrf
                <input type="hidden" name="phone" value="{{ $currentPhone }}">
                <input type="hidden" name="otp" id="final-otp">

                <div x-data="otpComponent({{ $isLocked ? 'true' : 'false' }})" class="mb-8">
                    <div class="flex gap-1.5 justify-center sm:gap-2 md:gap-3 dir-ltr"
                        :class="isLocked ? 'opacity-50' : ''">
                        <template x-for="(digit, index) in length" :key="index">
                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1"
                                :id="`otp-input-${index}`" :value="values[index]" :disabled="isLocked"
                                @input="handleInput($event, index)" @keydown="handleKeydown($event, index)"
                                @paste="handlePaste($event)" @focus="$event.target.select()"
                                class="w-10 h-12 font-mono text-xl font-black text-center rounded-xl border shadow-sm transition-all outline-none sm:w-12 sm:h-14 md:w-14 md:h-16 sm:text-2xl sm:rounded-2xl text-slate-800 bg-slate-50 border-slate-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 disabled:bg-slate-100 disabled:cursor-not-allowed">
                        </template>
                    </div>

                    <div x-show="isSubmitting && !isLocked" x-cloak
                        class="flex gap-2 justify-center items-center mt-6 text-primary">
                        <span class="material-symbols-outlined animate-spin text-[24px]">progress_activity</span>
                        <span class="text-sm font-bold">جاري التحقق...</span>
                    </div>
                </div>
            </form>

            <div x-data="timerComponent({{ $initialSeconds }}, {{ $isLocked ? 'true' : 'false' }})" class="relative z-10 pt-6 mt-6 text-center border-t border-slate-100">

                <p class="mb-3 text-xs font-bold text-slate-500">لم يصلك الكود؟</p>

                <form action="{{ route('otp.resend') }}" method="POST">
                    @csrf
                    <input type="hidden" name="phone" value="{{ $currentPhone }}">

                    <button type="submit" x-show="seconds === 0 && !isLockedUser" x-cloak
                        class="text-sm font-black underline transition-colors text-primary hover:text-primary-hover underline-offset-4 active:scale-95">
                        إعادة إرسال الكود
                    </button>

                    <div x-show="seconds > 0 || isLockedUser" x-cloak
                        class="inline-flex gap-2 items-center px-4 py-2 rounded-xl border bg-slate-50 border-slate-100"
                        :class="isLockedUser ? 'border-rose-100 bg-rose-50 text-rose-500' : 'text-slate-500'">
                        <span class="material-symbols-outlined text-[18px] animate-pulse"
                            :class="isLockedUser ? 'text-rose-500' : 'text-slate-400'">
                            <span x-text="isLockedUser ? 'lock_clock' : 'schedule'"></span>
                        </span>
                        <span class="text-xs font-bold">
                            <span x-text="isLockedUser ? 'مقفول! يرجى الانتظار' : 'يمكنك طلب كود جديد بعد'"></span>
                            <span x-text="formattedTime"
                                class="inline-block mx-1 w-10 font-mono font-black text-center dir-ltr"
                                :class="isLockedUser ? 'text-rose-600' : 'text-primary'"></span>
                        </span>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('otpComponent', (isLockedInitial) => ({
                length: 6,
                values: ['', '', '', '', '', ''],
                isSubmitting: false,
                isLocked: isLockedInitial,

                focusField(index) {
                    if (this.isLocked) return;
                    const field = document.getElementById(`otp-input-${index}`);
                    if (field) field.focus();
                },

                init() {
                    if (!this.isLocked) {
                        setTimeout(() => {
                            this.focusField(0);
                        }, 300);
                    }
                },

                handleInput(e, index) {
                    if (this.isLocked) return;
                    let val = e.target.value.replace(/\D/g, '');

                    if (val === '') {
                        this.values[index] = '';
                        return;
                    }

                    val = val.substring(val.length - 1);
                    this.values[index] = val;
                    e.target.value = val;

                    if (index < this.length - 1) {
                        this.focusField(index + 1);
                    } else {
                        this.checkAndSubmit();
                    }
                },

                handleKeydown(e, index) {
                    if (this.isLocked) return;
                    if (e.key === 'Backspace') {
                        if (this.values[index] === '' && index > 0) {
                            this.values[index - 1] = '';
                            const prevField = document.getElementById(`otp-input-${index - 1}`);
                            if (prevField) {
                                prevField.value = '';
                                prevField.focus();
                            }
                        } else {
                            this.values[index] = '';
                        }
                    } else if (e.key === 'ArrowLeft' && index > 0) {
                        this.focusField(index - 1);
                    } else if (e.key === 'ArrowRight' && index < this.length - 1) {
                        this.focusField(index + 1);
                    }
                },

                handlePaste(e) {
                    if (this.isLocked) return;
                    e.preventDefault();
                    let pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, this
                        .length);

                    if (pastedData) {
                        for (let i = 0; i < pastedData.length; i++) {
                            this.values[i] = pastedData[i];
                            const field = document.getElementById(`otp-input-${i}`);
                            if (field) field.value = pastedData[i];
                        }

                        if (pastedData.length === this.length) {
                            this.checkAndSubmit();
                        } else {
                            this.focusField(pastedData.length);
                        }
                    }
                },

                checkAndSubmit() {
                    if (this.isLocked) return;
                    const otpString = this.values.join('');
                    if (otpString.length === this.length) {
                        this.isSubmitting = true;
                        document.getElementById('final-otp').value = otpString;
                        setTimeout(() => {
                            document.getElementById('otp-form').submit();
                        }, 400);
                    }
                }
            }));

            Alpine.data('timerComponent', (initialSeconds, isLockedUser) => ({
                seconds: initialSeconds,
                interval: null,
                isLockedUser: isLockedUser,

                get formattedTime() {
                    let m = Math.floor(this.seconds / 60);
                    let s = this.seconds % 60;
                    return m + ':' + (s < 10 ? '0' : '') + s;
                },

                init() {
                    if (this.seconds > 0) {
                        this.interval = setInterval(() => {
                            this.seconds--;
                            if (this.seconds <= 0) {
                                clearInterval(this.interval);
                                // إعادة التحميل لإظهار الزر أو فك القفل
                                window.location.reload();
                            }
                        }, 1000);
                    }
                }
            }));
        });
    </script>
</x-guest-layout>
