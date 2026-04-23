@extends('mobile.layouts.app')

@section('content')
    <div class="px-4 py-10 min-h-screen font-sans bg-slate-50" dir="rtl">
        
        <div class="mb-10 text-center">
            <span class="inline-block px-4 py-1.5 mb-4 text-xs font-bold tracking-wide text-blue-700 rounded-full bg-blue-100/80">
                🚀 خطط الشحن الذكية
            </span>
            <h1 class="mb-3 text-3xl font-extrabold leading-tight text-transparent bg-clip-text bg-gradient-to-r from-slate-900 to-slate-600">
                اختر الباقة المناسبة
            </h1>
            <p class="px-2 text-sm text-slate-500">
                ارتقِ بأعمالك مع باقاتنا المرنة والمصممة لتلائم حجم شركتك.
            </p>
        </div>

        @if(session('error'))
            <div class="flex gap-3 items-center p-4 mb-6 bg-red-50 rounded-2xl border border-red-100 shadow-sm animate-pulse">
                <div class="flex justify-center items-center w-8 h-8 text-white bg-red-500 rounded-full shadow-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <div class="text-sm font-medium text-red-800">{{ session('error') }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="flex gap-3 items-center p-4 mb-6 bg-blue-50 rounded-2xl border border-blue-100 shadow-sm">
                <div class="flex justify-center items-center w-8 h-8 text-white bg-blue-500 rounded-full shadow-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="text-sm font-medium text-blue-800">{{ session('info') }}</div>
            </div>
        @endif

        <div class="flex flex-col gap-6">
            @foreach($packages as $package)
                @php
                    $isPendingThis = $pendingSubscription && $pendingSubscription->package_id == $package->id;
                    $adminPhone = "967781152674";
                    $appName = auth()->user()->App->name ?? 'شركتي';
                    $waMessage = "مرحباً إدارة النظام، قمت بطلب تفعيل باقة ({$package->name}) لشركة ({$appName}). أرجو تفعيل الحساب.";
                    $waLink = "https://wa.me/{$adminPhone}?text=" . urlencode($waMessage);
                @endphp

                <div class="relative bg-white rounded-[2rem] p-6 border shadow-[0_4px_20px_rgb(0,0,0,0.03)] transition-transform duration-200 active:scale-[0.98] {{ $isPendingThis ? 'border-green-400 ring-2 ring-green-50' : 'border-slate-100' }}">
                    
                    @if($isPendingThis)
                        <div class="absolute top-0 right-0 bg-gradient-to-l from-green-500 to-green-400 text-white text-[11px] font-bold px-4 py-1.5 rounded-bl-[1.5rem] rounded-tr-[2rem] shadow-sm">
                            طلبك الحالي
                        </div>
                    @endif

                    <div class="pb-5 mb-5 text-center border-b border-slate-100/80">
                        <h2 class="mt-2 mb-2 text-lg font-bold text-slate-700">{{ $package->name }}</h2>
                        <div class="flex gap-1 justify-center items-center">
                            <span class="text-4xl font-black tracking-tight text-slate-900">
                                {{ $package->price == 0 ? 'مجاناً' : '$' . $package->price }}
                            </span>
                        </div>
                        <div class="inline-block px-3 py-1 mt-3 text-xs font-semibold rounded-lg border bg-slate-50 text-slate-500 border-slate-100">
                            ⏳ صالحة لمدة {{ $package->duration_in_days }} يوماً
                        </div>
                    </div>

                    <ul class="mb-6 space-y-4 text-sm font-medium text-slate-600">
                        <li class="flex gap-3 items-center">
                            <div class="flex justify-center items-center w-6 h-6 text-blue-500 bg-blue-50 rounded-full shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span>{{ $package->max_branches ?: 'عدد غير محدود من' }} <strong class="font-bold text-slate-800">الفروع</strong></span>
                        </li>
                        <li class="flex gap-3 items-center">
                            <div class="flex justify-center items-center w-6 h-6 text-blue-500 bg-blue-50 rounded-full shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span>{{ $package->max_drivers ?: 'عدد غير محدود من' }} <strong class="font-bold text-slate-800">السائقين</strong></span>
                        </li>
                        <li class="flex gap-3 items-center">
                            <div class="flex justify-center items-center w-6 h-6 text-blue-500 bg-blue-50 rounded-full shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span>{{ $package->max_shipments ?: 'غير محدود' }} <strong class="font-bold text-slate-800">طرد / شهرياً</strong></span>
                        </li>
                        <li class="flex gap-3 items-center">
                            <div class="flex justify-center items-center w-6 h-6 text-blue-500 bg-blue-50 rounded-full shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span>{{ $package->max_packages ?: 'غير محدود' }} <strong class="font-bold text-slate-800">رحلة مجمعة</strong></span>
                        </li>
                    </ul>

                    <div>
                        @if($isPendingThis)
                            <a href="{{ $waLink }}" target="_blank"
                                class="flex items-center justify-center gap-2 w-full bg-gradient-to-r from-[#25D366] to-[#128C7E] text-white text-sm font-bold py-3.5 rounded-xl shadow-md active:scale-95 transition-transform duration-200">
                                تأكيد عبر الواتساب
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.031 0C5.385 0 0 5.388 0 12.036c0 2.124.553 4.195 1.602 6.012L.15 24l6.108-1.602a11.96 11.96 0 0 0 5.772 1.488h.005c6.645 0 12.03-5.388 12.03-12.036C24.065 5.388 18.676 0 12.031 0Zm0 21.896c-1.802 0-3.567-.485-5.115-1.403l-.366-.217-3.8.997.997-3.8-.238-.378a9.96 9.96 0 0 1-1.523-5.267c0-5.503 4.478-9.98 9.98-9.98s9.98 4.477 9.98 9.98-4.478 9.98-9.98 9.98Zm5.474-7.48c-.3-.15-1.776-.877-2.052-.977-.276-.1-.477-.15-.677.15-.2.3-.776.977-.952 1.177-.176.2-.352.226-.652.076-1.32-.656-2.39-1.332-3.328-2.614-.24-.326.242-.303.824-1.468.075-.15.037-.282-.038-.433-.075-.15-.677-1.632-.927-2.235-.245-.588-.495-.508-.677-.518-.175-.008-.376-.01-.576-.01-.2 0-.526.075-.802.375-.276.3-.105 1.152.927 2.53 0 0 1.77 2.7 4.28 3.755.57.24 1.01.385 1.354.492.57.18 1.09.155 1.5.093.46-.07 1.406-.575 1.603-1.13.197-.556.197-1.033.137-1.13-.06-.098-.21-.156-.51-.306Z" />
                                </svg>
                            </a>
                        @else
                            <form action="{{ route('subscription.request') }}" method="POST">
                                @csrf
                                <input type="hidden" name="package_id" value="{{ $package->id }}">
                                <button type="submit"
                                    class="flex gap-2 justify-center items-center py-3.5 w-full text-sm font-bold text-white rounded-xl shadow-md transition-transform duration-200 bg-slate-900 active:scale-95">
                                    @if($pendingSubscription)
                                        تغيير الطلب لهذه الباقة
                                    @else
                                        اطلب الاشتراك الآن
                                        <svg class="w-4 h-4 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    @endif
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection