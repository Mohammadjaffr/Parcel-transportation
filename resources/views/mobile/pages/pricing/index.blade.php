<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/flowbite@4.0.1/dist/flowbite.min.js"></script>

    <title>باقات الاشتراك | FLogistics</title>

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #0b1121;
            -webkit-tap-highlight-color: transparent;
        }

        .bg-lines-mobile {
            background-image: repeating-linear-gradient(45deg,
                    rgba(255, 255, 255, 0.015) 0px,
                    rgba(255, 255, 255, 0.015) 1px,
                    transparent 1px,
                    transparent 12px);
        }

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
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            z-index: 20;
        }

        .ribbon-vip {
            background: #059669;
        }

        .ribbon-popular {
            background: #2563eb;
        }
    </style>
</head>

<body class="overflow-x-hidden relative pb-10 min-h-screen antialiased text-gray-300">

    <!-- الخلفيات المؤثرة للموبايل -->
    <div class="fixed inset-0 z-0 pointer-events-none bg-lines-mobile"></div>
    <div
        class="fixed top-0 right-0 w-72 h-72 bg-blue-900/20 rounded-full mix-blend-screen filter blur-[80px] pointer-events-none z-0">
    </div>
    <div
        class="fixed bottom-20 left-0 w-72 h-72 bg-emerald-900/10 rounded-full mix-blend-screen filter blur-[80px] pointer-events-none z-0">
    </div>

    <div class="relative z-10 px-4 py-8 mx-auto w-full max-w-md">

        <!-- الهيدر -->
        <div class="flex flex-col gap-3 justify-center items-center mb-8 text-center">
            <img src="{{ asset('assets/image/icon_without_bg.png') }}" alt="Logo" class="w-20 h-20 drop-shadow-xl">
            <h1 class="text-2xl font-black tracking-tight leading-tight text-white">
                ارتقِ بأعمال الشحن الخاصة بك
            </h1>
            <p class="px-2 text-sm text-gray-400">باقات مرنة ومصممة خصيصاً لتلبية احتياجاتك المتطورة.</p>
        </div>

        <!-- التنبيهات -->
        @if (session('error') || session('info'))
            <div
                class="flex gap-3 items-center p-3.5 mb-6 rounded-2xl border shadow-lg backdrop-blur-md 
                {{ session('error') ? 'bg-red-900/40 border-red-500/40' : 'bg-teal-900/40 border-teal-500/40' }}">
                <div
                    class="flex justify-center items-center w-8 h-8 text-white rounded-full shadow-sm shrink-0 
                    {{ session('error') ? 'bg-red-500/80' : 'bg-teal-500/80' }}">
                    @if (session('error'))
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    @endif
                </div>
                <div class="text-sm font-medium {{ session('error') ? 'text-red-200' : 'text-teal-200' }}">
                    {{ session('error') ?? session('info') }}
                </div>
            </div>
        @endif

        <!-- حاوية الباقات -->
        <div class="flex flex-col gap-6">
            @foreach ($packages as $package)
                @php
                    $isFree = $package->price == 0;
                    $isVip =
                        str_contains(strtoupper($package->name), 'VIP') ||
                        str_contains($package->name, 'كبرى') ||
                        $package->price > 200;
                    $isPopular = !$isFree && !$isVip;

                    $isActiveThis =
                        isset($activeSubscription) &&
                        $activeSubscription &&
                        $activeSubscription->package_id == $package->id;
                    $isPendingThis =
                        isset($pendingSubscription) &&
                        $pendingSubscription &&
                        $pendingSubscription->package_id == $package->id;

                    $checkBgClass = $isVip ? 'bg-emerald-500' : ($isFree ? 'bg-gray-500' : 'bg-blue-600');

                    $adminPhone = '967781152674';
                    $appName = auth()->user()->App->name ?? 'شركتي';
                    $waMessage = "مرحباً، قمت بطلب تفعيل باقة ({$package->name}) لشركة ({$appName}). أرجو تفعيل الحساب.";
                    $waLink = "https://wa.me/{$adminPhone}?text=" . urlencode($waMessage);
                @endphp

                <div
                    class="bg-[#1e293b]/80 backdrop-blur-xl rounded-[2rem] p-7 border relative overflow-hidden transition-all
                    {{ $isPopular ? 'border-blue-500/50 shadow-[0_0_30px_-10px_rgba(37,99,235,0.4)] z-10' : 'border-white/10 shadow-xl' }}
                    {{ $isActiveThis ? 'ring-2 ring-emerald-500/50' : '' }}
                    {{ $isPendingThis ? 'ring-2 ring-yellow-500/50' : '' }}">

                    @if ($isVip)
                        <div class="ribbon-mobile ribbon-vip">VIP</div>
                    @elseif($isPopular)
                        <div class="ribbon-mobile ribbon-popular">الاكثر طلب</div>
                    @endif

                    <!-- علامات الحالة الجانبية (للموبايل فقط) -->
                    @if ($isActiveThis)
                        <div
                            class="absolute top-0 right-0 px-3 py-1 text-[11px] font-bold text-white bg-gradient-to-l from-emerald-600 to-emerald-500 rounded-bl-xl shadow-md">
                            نشط حالياً
                        </div>
                    @elseif($isPendingThis)
                        <div
                            class="absolute top-0 right-0 px-3 py-1 text-[11px] font-bold text-white bg-gradient-to-l from-yellow-600 to-yellow-500 rounded-bl-xl shadow-md">
                            قيد المراجعة
                        </div>
                    @endif

                    <!-- رأس الباقة -->
                    <div class="pb-6 mt-4 mb-6 text-center border-b border-white/10">
                        <h2 class="mb-2 text-lg font-bold text-gray-200">{{ $package->name }}</h2>

                        <div class="flex gap-1 justify-center items-end mb-4 h-12">
                            @if ($isFree)
                                <span class="text-4xl font-black tracking-tight text-white">مجاناً</span>
                            @else
                                <span
                                    class="text-4xl font-black tracking-tight text-white">{{ number_format($package->price) }}</span>
                                <span class="mb-1 text-lg font-bold text-gray-400">ر.س</span>
                            @endif
                        </div>

                        <div
                            class="inline-block px-4 py-1 text-xs font-semibold text-gray-300 rounded-full border bg-white/5 border-white/10">
                            صالحة لمدة {{ $package->duration_in_days }} يوماً
                        </div>
                    </div>

                    <!-- المميزات -->
                    <ul class="mb-8 space-y-4 text-sm font-medium text-gray-300">
                        <li class="flex justify-between items-center">
                            <span><strong class="text-white">{{ $package->max_branches ?: 'عدد غير محدود' }}</strong>
                                فروع</span>
                            <div class="flex justify-center items-center w-5 h-5 rounded-full {{ $checkBgClass }}">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </li>
                        <li class="flex justify-between items-center">
                            <span><strong class="text-white">{{ $package->max_drivers ?: 'عدد غير محدود' }}</strong>
                                سائقين</span>
                            <div class="flex justify-center items-center w-5 h-5 rounded-full {{ $checkBgClass }}">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </li>
                        <li class="flex justify-between items-center">
                            <span><strong class="text-white">{{ $package->max_shipments ?: 'غير محدود' }}</strong> طرد
                                / شهرياً</span>
                            <div class="flex justify-center items-center w-5 h-5 rounded-full {{ $checkBgClass }}">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </li>
                        <li class="flex justify-between items-center">
                            <span><strong class="text-white">{{ $package->max_packages ?: 'غير محدود' }}</strong> رحلة
                                مجمعة</span>
                            <div class="flex justify-center items-center w-5 h-5 rounded-full {{ $checkBgClass }}">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </li>
                    </ul>

                    <!-- الأزرار السفلية -->
                    <div class="mt-auto">
                        @if ($isActiveThis)
                            <button disabled
                                class="py-3.5 w-full text-[15px] font-bold rounded-xl bg-emerald-900/30 text-emerald-400 border border-emerald-500/30 cursor-not-allowed">
                                باقـتـك الحالية
                            </button>
                        @elseif($isPendingThis)
                            <div class="flex flex-col gap-2">
                                <button disabled
                                    class="py-3.5 w-full text-[15px] font-bold rounded-xl bg-yellow-900/30 text-yellow-400 border border-yellow-500/30 cursor-not-allowed">
                                    قيد المراجعة...
                                </button>
                                <a href="{{ $waLink }}" target="_blank"
                                    class="text-xs text-center text-emerald-400 underline hover:text-emerald-300 underline-offset-4">
                                    تواصل عبر الواتساب للتسريع
                                </a>
                            </div>
                        @else
                            <form action="{{ route('subscription.request') }}" method="POST">
                                @csrf
                                <input type="hidden" name="package_id" value="{{ $package->id }}">
                                <button type="submit"
                                    class="py-3.5 w-full text-[15px] font-bold rounded-xl transition-all shadow-lg border
                                    {{ $isPopular ? 'bg-[#2563eb] active:bg-[#1d4ed8] text-white border-blue-500 shadow-blue-900/40' : 'bg-white/10 active:bg-white/20 text-white border-white/20' }}">
                                    {{ $isFree ? 'ابدأ باقتك المجانية' : ($activeSubscription ? 'ترقية إلى هذه الباقة' : 'اشترك الآن') }}
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
