<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/flowbite@4.0.1/dist/flowbite.min.js"></script>

    <title>باقات الاشتراك | FLogistics</title>

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #0b1121; /* لون خلفية داكن جداً */
            -webkit-tap-highlight-color: transparent;
        }

        /* تأثير الخلفية المخططة المخصص للموبايل */
        .bg-lines-mobile {
            background-image: repeating-linear-gradient(
                45deg,
                rgba(255, 255, 255, 0.02) 0px,
                rgba(255, 255, 255, 0.02) 2px,
                transparent 2px,
                transparent 10px
            );
        }
        
        /* أشرطة الباقات (Ribbons) مصغرة لتناسب الموبايل */
        .ribbon-mobile {
            position: absolute;
            top: 20px;
            left: -35px;
            color: #fff;
            padding: 4px 40px;
            transform: rotate(-45deg);
            font-weight: 800;
            font-size: 0.7rem;
            letter-spacing: 1px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            z-index: 20;
        }
        .ribbon-vip { background: #059669; } /* أخضر زمردي */
        .ribbon-popular { background: #2563eb; } /* أزرق */
    </style>
</head>
<body class="overflow-x-hidden relative min-h-screen antialiased text-gray-300">

    <div class="fixed inset-0 z-0 pointer-events-none bg-lines-mobile"></div>
    <div class="fixed top-0 right-0 w-72 h-72 bg-blue-900/20 rounded-full mix-blend-screen filter blur-[80px] pointer-events-none z-0"></div>
    <div class="fixed bottom-20 left-0 w-72 h-72 bg-emerald-900/10 rounded-full mix-blend-screen filter blur-[80px] pointer-events-none z-0"></div>

    <div class="relative z-10 px-4 py-10 mx-auto w-full max-w-md">
        
        <div class="flex flex-col gap-4 justify-center items-center mb-10 text-center">
            <img src="{{ asset('assets/image/icon_without_bg.png') }}" alt="Logo" class="w-24 h-24 drop-shadow-[0_0_15px_rgba(255,255,255,0.1)]">
            <h1 class="text-2xl font-bold leading-relaxed text-white">
                ارتق بأعمال الشحن الخاصة بك مع باقاتنا المرنة والمصممة خصيصاً لتلبية احتياجاتك المتطورة.
            </h1>
        </div>

        @if(session('error'))
            <div class="flex gap-3 items-center p-4 mb-6 rounded-2xl border shadow-lg backdrop-blur-md animate-pulse bg-red-900/30 border-red-500/30">
                <div class="flex justify-center items-center w-8 h-8 text-white rounded-full shadow-sm bg-red-500/80 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <div class="text-sm font-medium text-red-200">{{ session('error') }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="flex gap-3 items-center p-4 mb-6 rounded-2xl border shadow-lg backdrop-blur-md bg-blue-900/30 border-blue-500/30">
                <div class="flex justify-center items-center w-8 h-8 text-white rounded-full shadow-sm bg-blue-500/80 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="text-sm font-medium text-blue-200">{{ session('info') }}</div>
            </div>
        @endif

        <div class="flex flex-col gap-6 pb-6">
            @foreach($packages as $package)
                @php
                    $isFree = $package->price == 0;
                    $isVip = str_contains(strtoupper($package->name), 'VIP') || str_contains($package->name, 'كبرى') || $package->price > 200;
                    $checkBgClass = $isVip ? 'bg-emerald-500' : ($isFree ? 'bg-gray-500' : 'bg-blue-600');

                    $isActiveThis = isset($activeSubscription) && $activeSubscription && $activeSubscription->package_id == $package->id;
                    $isPendingThis = isset($pendingSubscription) && $pendingSubscription && $pendingSubscription->package_id == $package->id;
                    
                    $adminPhone = "967781152674";
                    $appName = auth()->user()->App->name ?? 'شركتي';
                    $waMessage = "مرحباً إدارة النظام، قمت بطلب تفعيل باقة ({$package->name}) لشركة ({$appName}). أرجو تفعيل الحساب.";
                    $waLink = "https://wa.me/{$adminPhone}?text=" . urlencode($waMessage);
                @endphp

                <div class="bg-[#1e293b]/60 backdrop-blur-xl rounded-[2rem] p-7 border shadow-2xl relative overflow-hidden transition-all 
                    @if($isActiveThis) border-blue-500/60 shadow-[0_0_20px_rgba(59,130,246,0.2)]
                    @elseif($isPendingThis) border-emerald-500/60 shadow-[0_0_20px_rgba(16,185,129,0.2)]
                    @else border-white/10 @endif">

                    @if($isVip && !$isActiveThis && !$isPendingThis)
                        <div class="ribbon-mobile ribbon-vip">VIP</div>
                    @elseif(!$isFree && !$isActiveThis && !$isPendingThis)
                        <div class="ribbon-mobile ribbon-popular">POPULAR</div>
                    @endif

                    @if($isActiveThis)
                        <div class="absolute top-0 right-0 px-4 py-1.5 text-xs font-bold text-white bg-gradient-to-l from-blue-600 to-blue-500 rounded-bl-2xl shadow-lg">
                            باقتك الحالية
                        </div>
                    @elseif($isPendingThis)
                        <div class="absolute top-0 right-0 px-4 py-1.5 text-xs font-bold text-white bg-gradient-to-l from-emerald-600 to-emerald-500 rounded-bl-2xl shadow-lg">
                            قيد الانتظار
                        </div>
                    @endif

                    <div class="pb-6 mt-3 mb-6 text-center border-b border-white/10">
                        <h2 class="mb-3 text-lg font-bold text-gray-100">{{ $package->name }}</h2>
                        
                        <div class="flex gap-2 justify-center items-center mb-4">
                            @if($isFree)
                                <div class="flex justify-center items-center w-6 h-6 bg-yellow-500 rounded-full shadow-[0_0_10px_rgba(234,179,8,0.5)]">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <span class="text-3xl font-black tracking-tight text-white">مجاناً</span>
                            @else
                                <span class="text-4xl font-black tracking-tight text-white">${{ number_format($package->price, 2) }}</span>
                            @endif
                        </div>
                        
                        <div class="inline-block px-4 py-1.5 text-xs font-semibold text-gray-300 rounded-full border bg-white/5 border-white/10">
                            صالحة لمدة {{ $package->duration_in_days }} يوماً
                        </div>
                    </div>

                    <ul class="mb-8 space-y-4 text-sm font-medium text-gray-300">
                        <li class="flex justify-between items-center">
                            <div class="flex gap-3 items-center">
                                <svg class="w-5 h-5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span>{{ $package->max_branches ?: 'عدد غير محدود من' }} <strong class="font-bold text-white">الفروع</strong></span>
                            </div>
                            <div class="flex justify-center items-center w-4 h-4 rounded-full {{ $checkBgClass }}">
                                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </li>
                        <li class="flex justify-between items-center">
                            <div class="flex gap-3 items-center">
                                <svg class="w-5 h-5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span>{{ $package->max_drivers ?: 'عدد غير محدود من' }} <strong class="font-bold text-white">السائقين</strong></span>
                            </div>
                            <div class="flex justify-center items-center w-4 h-4 rounded-full {{ $checkBgClass }}">
                                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </li>
                        <li class="flex justify-between items-center">
                            <div class="flex gap-3 items-center">
                                <svg class="w-5 h-5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                <span>{{ $package->max_shipments ?: 'غير محدود' }} <strong class="font-bold text-white">طرد / شهرياً</strong></span>
                            </div>
                            <div class="flex justify-center items-center w-4 h-4 rounded-full {{ $checkBgClass }}">
                                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </li>
                        <li class="flex justify-between items-center">
                            <div class="flex gap-3 items-center">
                                <svg class="w-5 h-5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                <span>{{ $package->max_packages ?: 'غير محدود' }} <strong class="font-bold text-white">رحلة مجمعة</strong></span>
                            </div>
                            <div class="flex justify-center items-center w-4 h-4 rounded-full {{ $checkBgClass }}">
                                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </li>
                    </ul>

                    <div class="mt-auto">
                        @if($isActiveThis)
                            <button disabled class="py-3.5 w-full text-[15px] font-bold text-gray-400 rounded-xl border backdrop-blur-sm cursor-not-allowed bg-white/5 border-white/10">
                                أنت مشترك في هذه الباقة حالياً
                            </button>
                        @elseif($isPendingThis || $isVip)
                            <a href="{{ $waLink }}" target="_blank"
                                class="flex justify-center items-center py-3.5 w-full text-[15px] font-bold text-emerald-50 bg-[#064e3b] hover:bg-[#022c22] border border-[#059669]/50 rounded-xl transition-all shadow-lg shadow-emerald-900/20 gap-2">
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
                                    class="py-3.5 w-full text-[15px] font-bold rounded-xl transition-all shadow-lg border
                                    {{ $isFree ? 'bg-white/10 hover:bg-white/20 text-gray-200 border-white/20' : 'bg-[#1e3a8a] hover:bg-[#172554] text-blue-50 border-blue-600/50 shadow-blue-900/30' }}">
                                    @if($pendingSubscription)
                                        تغيير الطلب لهذه الباقة
                                    @elseif($isFree)
                                        ابدأ تجربتك المجانية
                                    @else
                                        اطلب الاشتراك الآن
                                    @endif
                                </button>
                            </form>
                        @endif
                    </div>
                    
                </div>
            @endforeach
        </div>
    </div>
    
</body>
</html>