@extends('mobile.layouts.app')

@section('title', 'تفاصيل الراكب')

@section('content')

    @php
        $passengerDate = $passenger->date ? \Carbon\Carbon::parse($passenger->date) : null;
        $dayName = $passengerDate ? $passengerDate->translatedFormat('l') : 'غير محدد';

        $driverName = $passenger->driver->name ?? 'لم يحدد بعد';
        $branchName = $passenger->branch->name ?? 'غير محدد';
        $driverInitial = $passenger->driver ? mb_substr($driverName, 0, 1, 'UTF-8') : 'س';
        $brokerName = $passenger->broker?->name ?? 'وسيط غير محدد';
        $brokerInitial = mb_substr($brokerName, 0, 1, 'UTF-8');

        // تحديد الحالة الديناميكية
        $rawStatus = strtolower($passenger->status ?? 'pending');
        if ($rawStatus == 'completed' || $rawStatus == 'مكتمل') {
            $statusLabel = 'مكتمل';
            $statusClass =
                'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20';
            $statusIcon = 'task_alt';
        } elseif ($rawStatus == 'cancel' || $rawStatus == 'ملغي') {
            $statusLabel = 'ملغي';
            $statusClass =
                'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20';
            $statusIcon = 'cancel';
        } else {
            $statusLabel = 'قيد التجهيز';
            $statusClass =
                'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20';
            $statusIcon = 'schedule';
        }
    @endphp

    <div class="flex flex-col pt-4 pb-24 min-h-screen bg-slate-50/50 dark:bg-black font-headline" x-data="{ showStatusModal: false, showRebookModal: false, showDeleteModal: false, isSubmitting: false }" dir="rtl">

        {{-- ================= الهيدر وزر الرجوع ================= --}}
        <div class="flex justify-between items-center px-4 mb-5">
            <div class="flex gap-3 items-center">
                <a href="{{ route('passengers.index') }}"
                    class="flex justify-center items-center w-10 h-10 bg-white dark:bg-boxdark rounded-full border shadow-sm transition-all border-slate-100 dark:border-boxdark-2 text-slate-500 dark:text-gray-400 hover:text-primary active:scale-90">
                    <span class="material-symbols-outlined text-[20px] mr-1">arrow_forward_ios</span>
                </a>
                <h1 class="text-lg font-black text-slate-800 dark:text-white">تفاصيل الراكب</h1>
            </div>

        </div>

        <div class="px-4 space-y-4 w-full">

            {{-- ================= 1️⃣ البطاقة الرئيسية (رقم السند والهاتف) ================= --}}
            <div
                class="p-5 bg-white rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden dark:bg-boxdark dark:border-boxdark-2">
                <div class="flex justify-between items-start mb-4">
                    <div
                        class="inline-flex gap-1.5 items-center px-3 py-1.5 text-[10px] font-black rounded-xl border shadow-sm {{ $statusClass }}">
                        <span class="material-symbols-outlined text-[15px]">{{ $statusIcon }}</span>
                        {{ $statusLabel }}
                    </div>
                    <span
                        class="text-[10px] font-black font-mono text-slate-400 bg-slate-50 px-3 py-1.5 rounded-xl dark:bg-boxdark-2 dark:text-slate-500">
                        #{{ $passenger->id }}
                    </span>
                </div>

                <div class="flex gap-4 items-center">
                    <div
                        class="flex justify-center items-center w-14 h-14 text-2xl font-black bg-primary/10 rounded-[1.25rem] border shadow-inner text-primary border-primary/5 shrink-0 dark:bg-primary/20">
                        {{ mb_substr($passenger->passenger_name ?: 'ر', 0, 1, 'UTF-8') }}
                    </div>
                    <div class="min-w-0">
                        @if ($passenger->passenger_name)
                            <h2 class="text-base font-black text-slate-800 dark:text-white truncate mb-0.5">
                                {{ $passenger->passenger_name }}
                            </h2>
                        @endif
                        <span class="block mb-0.5 text-[10px] font-bold text-slate-400 dark:text-gray-500">رقم هاتف
                            الراكب</span>
                        <div class="inline-block px-3 py-1 bg-slate-50 dark:bg-boxdark-2 rounded-xl">
                            <x-phone-number :value="$passenger->passenger_number"
                                class="text-sm font-black font-mono text-slate-700 dir-ltr inline-block dark:text-white" />
                        </div>
                    </div>
                </div>
            </div>

            @if ($rawStatus == 'pending')
                @php
                    $whatsappUrl = \App\Services\WhatsApp\WhatsAppLinkService::generate($passenger, 'passengerBooking');
                @endphp
                {{-- ================= بطاقة الإجراءات السريعة للموبايل ================= --}}
                <div class="p-5 bg-white rounded-[2rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 space-y-3">
                    <h3 class="flex items-center gap-2 text-xs font-black text-slate-400 dark:text-gray-500 uppercase tracking-wider">
                        <span class="material-symbols-outlined text-[18px]">bolt</span> الإجراءات السريعة
                    </h3>
                    
                    <div class="flex flex-col gap-2.5">
                        @if ($whatsappUrl)
                            <button type="button"
                                onclick="sendPassengerBookingDirect({{ $passenger->id }}, this, '{{ $whatsappUrl }}')"
                                class="flex gap-3 justify-center items-center py-3.5 bg-emerald-50 border border-emerald-100 rounded-2xl text-xs font-black text-emerald-600 shadow-sm transition-all hover:bg-emerald-100/50 active:scale-95">
                                <span class="material-symbols-outlined text-[16px]">send</span>
                                إرسال تفاصيل الحجز (واتساب)
                            </button>
                        @endif

                        <div class="grid grid-cols-2 gap-2.5">
                            <button type="button" @click="showStatusModal = true"
                                class="flex gap-2 justify-center items-center py-3.5 bg-amber-50 border border-amber-100 rounded-2xl text-xs font-black text-amber-600 shadow-sm transition-all hover:bg-amber-100/50 active:scale-95">
                                <span class="material-symbols-outlined text-[16px]">block</span>
                                إلغاء الراكب
                            </button>

                            <button type="button" @click="showDeleteModal = true"
                                class="flex gap-2 justify-center items-center py-3.5 bg-rose-50 border border-rose-100 rounded-2xl text-xs font-black text-rose-600 shadow-sm transition-all hover:bg-rose-100/50 active:scale-95">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                                حذف الراكب
                            </button>
                        </div>
                    </div>
            @elseif ($rawStatus == 'cancel')
                @php
                    $whatsappCancelUrl = \App\Services\WhatsApp\WhatsAppLinkService::generate($passenger, 'passengerCancel');
                    $whatsappRebookUrl = \App\Services\WhatsApp\WhatsAppLinkService::generate($passenger, 'passengerRebook');
                @endphp
                {{-- ================= بطاقة الإجراءات السريعة للملغي ================= --}}
                <div class="p-5 bg-white rounded-[2rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 space-y-3">
                    <h3 class="flex items-center gap-2 text-xs font-black text-slate-400 dark:text-gray-500 uppercase tracking-wider">
                        <span class="material-symbols-outlined text-[18px]">bolt</span> الإجراءات المتاحة
                    </h3>
                    
                    <div class="flex flex-col gap-2.5">
                        {{-- زر إعادة الحجز --}}
                        <button type="button" @click="showRebookModal = true"
                            class="flex gap-2.5 justify-center items-center py-3.5 bg-emerald-600 text-white rounded-2xl text-xs font-black shadow-lg shadow-emerald-600/25 transition-all hover:bg-emerald-700 active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">history</span>
                            إعادة الحجز (تأكيد وإشعار الوسيط)
                        </button>

                        @if ($whatsappRebookUrl)
                            <button type="button"
                                onclick="sendPassengerRebookDirect({{ $passenger->id }}, this, '{{ $whatsappRebookUrl }}')"
                                class="flex gap-3 justify-center items-center py-3 bg-emerald-50 border border-emerald-100 rounded-2xl text-xs font-black text-emerald-600 shadow-sm transition-all hover:bg-emerald-100/50 active:scale-95">
                                <span class="material-symbols-outlined text-[16px]">send</span>
                                إرسال رسالة إعادة الحجز للوسيط (واتساب)
                            </button>
                        @endif

                        @if ($whatsappCancelUrl)
                            <button type="button"
                                onclick="sendPassengerCancelDirect({{ $passenger->id }}, this, '{{ $whatsappCancelUrl }}')"
                                class="flex gap-3 justify-center items-center py-3 bg-rose-50 border border-rose-100 rounded-2xl text-xs font-black text-rose-600 shadow-sm transition-all hover:bg-rose-100/50 active:scale-95">
                                <span class="material-symbols-outlined text-[16px]">send</span>
                                إرسال الإلغاء للوسيط (واتساب)
                            </button>
                        @endif

                        <button type="button" @click="showDeleteModal = true"
                            class="flex gap-2 justify-center items-center py-3 bg-rose-50 border border-rose-100 rounded-2xl text-xs font-black text-rose-600 shadow-sm transition-all hover:bg-rose-100/50 active:scale-95">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                            حذف الراكب
                        </button>
                    </div>
                </div>
            @endif

            {{-- ================= 2️⃣ خط السير والمسار (Timeline ستايل) ================= --}}
            @if (!empty($passenger->destination) || !empty($passenger->pickup_location))
                <div
                    class="p-5 bg-white rounded-[2rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                    <h3
                        class="flex items-center gap-2 text-xs font-black text-slate-400 mb-4 dark:text-gray-500 uppercase tracking-wider">
                        <span class="material-symbols-outlined text-[18px]">alt_route</span> خط سير الرحلة
                    </h3>

                    <div class="relative pr-6 border-r-2 border-dashed border-slate-200 dark:border-slate-800 space-y-4 my-2">
                        <div class="relative">
                            <span
                                class="absolute right-[-29px] top-1 w-2.5 h-2.5 rounded-full bg-slate-300 border-2 border-white ring-4 ring-slate-100 dark:border-boxdark dark:ring-slate-900"></span>
                            <div class="text-xs">
                                <span class="text-slate-400 font-bold block text-[10px] mb-0.5 dark:text-gray-500">مكان
                                    الصعود / الانطلاق:</span>
                                <span
                                    class="text-sm font-black text-slate-700 dark:text-white">{{ $passenger->pickup_location ?? ($passenger->location ?? 'غير محدد') }}</span>
                            </div>
                        </div>

                        <div class="relative">
                            <span
                                class="absolute right-[-29px] top-1 w-2.5 h-2.5 rounded-full bg-amber-500 border-2 border-white ring-4 ring-amber-100 dark:border-boxdark dark:ring-amber-500/10"></span>
                            <div class="text-xs">
                                <span class="text-amber-500 font-bold block text-[10px] mb-0.5">الوجهة المقصودة:</span>
                                <span
                                    class="text-sm font-black text-amber-600 dark:text-amber-400">{{ $passenger->destination ?? 'غير محدد' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ================= 3️⃣ معلومات الوقت والعدد والفرع ================= --}}
            <div class="grid grid-cols-3 gap-2.5">
                {{-- التاريخ اليومي --}}
                <div
                    class="p-4 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center items-center text-center dark:bg-boxdark dark:border-boxdark-2">
                    <div
                        class="flex justify-center items-center mb-2 w-9 h-9 text-blue-500 bg-blue-50 rounded-xl dark:bg-blue-500/10">
                        <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-gray-500 mb-0.5">تاريخ الرحلة</span>
                    <span
                        class="text-xs font-black text-slate-800 dark:text-white font-mono">{{ $passengerDate ? $passengerDate->format('Y-m-d') : 'غير محدد' }}</span>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-gray-500 mt-0.5">{{ $dayName }}</span>
                </div>

                {{-- نوع المركبة --}}
                <div
                    class="p-4 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center items-center text-center dark:bg-boxdark dark:border-boxdark-2">
                    <div
                        class="flex justify-center items-center mb-2 w-9 h-9 text-indigo-500 bg-indigo-50 rounded-xl dark:bg-indigo-500/10">
                        <span class="material-symbols-outlined text-[18px]">directions_car</span>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-gray-500 mb-0.5">نوع المركبة</span>
                    <span
                        class="text-xs font-black text-slate-800 dark:text-white truncate max-w-full">{{ $passenger->vehicle_type ?: 'غير محدد' }}</span>
                </div>

                {{-- عدد الركاب --}}
                <div
                    class="p-4 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center items-center text-center dark:bg-boxdark dark:border-boxdark-2">
                    <div
                        class="flex justify-center items-center mb-2 w-9 h-9 text-purple-500 bg-purple-50 rounded-xl dark:bg-purple-500/10">
                        <span class="material-symbols-outlined text-[18px]">groups</span>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-gray-500 mb-0.5">إجمالي الركاب</span>
                    <span
                        class="text-sm font-black text-slate-800 dark:text-white">{{ number_format($passenger->count ?? 1, 0) }}
                        ركاب</span>
                </div>
            </div>

            {{-- ================= 4️⃣ القسم المالي (العمولات وعمولة المكتب) ================= --}}
            <div
                class="p-5 bg-white rounded-[2rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 relative overflow-hidden">
                <h3
                    class="flex items-center gap-2 text-xs font-black text-slate-400 mb-4 dark:text-gray-500 uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[18px]">payments</span> البيانات المالية للطلب
                </h3>

                <div class="grid grid-cols-2 gap-3">
                    <div
                        class="p-3 rounded-2xl bg-emerald-50/60 border border-emerald-100 dark:bg-emerald-500/5 dark:border-emerald-500/10">
                        <span class="block text-[10px] font-bold text-slate-400 dark:text-gray-500 mb-1">عمولة المكتب</span>
                        <span class="text-base font-black text-emerald-600 dark:text-emerald-400">
                            {{ number_format((float) ($passenger->office_commission ?? 0), 0) }} <span
                                class="text-[10px] font-bold">ر.ي</span>
                        </span>
                    </div>

                    <div
                        class="p-3 rounded-2xl bg-amber-50/60 border border-amber-100 dark:bg-amber-500/5 dark:border-amber-500/10">
                        <span class="block text-[10px] font-bold text-slate-400 dark:text-gray-500 mb-1">عمولة مكاتب
                            أخرى</span>
                        <span class="text-base font-black text-amber-500 dark:text-amber-400">
                            {{ number_format((float) ($passenger->other_office_commission ?? 0), 0) }} <span
                                class="text-[10px] font-bold">ر.ي</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- 1. تجهيز بيانات السائق والوسيط ذكياً بناءً على وجود رحلة أم لا --}}
            @php
                // التحقق أولاً من وجود سائق للرحلة، وإلا نعتمد سائق السند المباشر
                $activeDriver = $passenger->trip->driver ?? $passenger->driver;

                $driverName = $activeDriver->name ?? 'غير محدد';
                $driverPhone = $activeDriver->phone ?? null;
                $driverInitial = $activeDriver ? mb_substr($driverName, 0, 1, 'UTF-8') : '-';
                $isTripDriver = isset($passenger->trip->driver); // متغير لمعرفة هل هو سائق رحلة أم لا

                $brokerName = $passenger->broker->name ?? 'غير محدد';
                $brokerInitial = $passenger->broker ? mb_substr($brokerName, 0, 1, 'UTF-8') : '-';
            @endphp

            {{-- ================= 5️⃣ جهات التوصيل (السائق والوسيط بالتوازي في صف واحد) ================= --}}
            <div class="grid grid-cols-2 gap-3">

                {{-- ----------- كرت السائق (الكابتن) ----------- --}}
                <div
                    class="p-4 bg-white rounded-[1.75rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-2.5">
                            <span
                                class="block text-[9px] font-bold text-slate-400 dark:text-gray-500 uppercase tracking-wider">
                                الكابتن المسؤول
                            </span>
                            {{-- شارة ذكية تظهر فقط إذا كان السائق مأخوذاً من الرحلة المربوطة --}}
                            @if($isTripDriver)
                                <span
                                    class="text-[8px] bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 px-1.5 py-0.5 rounded-md font-bold">
                                    سائق الرحلة
                                </span>
                            @endif
                        </div>

                        <div class="flex gap-2 items-center min-w-0">
                            <div
                                class="flex justify-center items-center w-8 h-8 text-[11px] font-black text-white rounded-xl shadow-sm bg-primary shrink-0">
                                {{ $driverInitial }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-black truncate text-slate-800 dark:text-white"
                                    title="{{ $driverName }}">
                                    {{ $driverName }}
                                </h4>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-3 pt-2 border-t border-slate-50 dark:border-boxdark-2 flex items-center justify-between gap-1 min-w-0">
                        <div class="min-w-0 flex-1">
                            @if ($driverPhone)
                                <x-phone-number :value="$driverPhone"
                                    class="text-[10px] font-bold text-slate-400 font-mono dir-ltr inline-block truncate w-full dark:text-bodydark" />
                            @else
                                <span class="text-[9px] font-bold text-slate-300 dark:text-gray-600 truncate block">لا يوجد
                                    رقم</span>
                            @endif
                        </div>
                        @if ($driverPhone)
                            <a href="https://wa.me/{{ $driverPhone }}" target="_blank"
                                class="flex justify-center items-center w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 shrink-0">
                                <span class="material-symbols-outlined text-[13px]">chat</span>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- ----------- كرت الوسيط المعتمد (منفصل تماماً وفي نفس الصف) ----------- --}}
                <div
                    class="p-4 bg-white rounded-[1.75rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 flex flex-col justify-between">
                    <div>
                        <span
                            class="block text-[9px] font-bold text-slate-400 dark:text-gray-500 mb-2.5 uppercase tracking-wider">
                            الوسيط المعتمد
                        </span>
                        <div class="flex gap-2 items-center min-w-0">
                            <div
                                class="flex justify-center items-center w-8 h-8 text-[11px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400 rounded-xl shrink-0">
                                {{ $brokerInitial }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-black truncate text-slate-800 dark:text-white"
                                    title="{{ $brokerName }}">
                                    {{ $brokerName }}
                                </h4>
                                <span class="block text-[9px] font-bold text-slate-300 dark:text-gray-600 mt-0.5">موجّه
                                    السند</span>
                            </div>
                        </div>
                    @php $brokerPhone = $passenger->broker?->phone; @endphp
                    <div class="mt-3 pt-2 border-t border-slate-50 dark:border-boxdark-2 flex items-center justify-between gap-1 min-w-0">
                        <div class="min-w-0 flex-1">
                            @if ($brokerPhone)
                                <x-phone-number :value="$brokerPhone"
                                    class="text-[10px] font-bold text-slate-400 font-mono dir-ltr inline-block truncate w-full dark:text-bodydark" />
                            @else
                                <span class="text-[9px] font-bold text-slate-300 dark:text-gray-600 truncate block">لا يوجد رقم</span>
                            @endif
                        </div>
                        @if ($brokerPhone)
                            <a href="https://wa.me/{{ $brokerPhone }}" target="_blank"
                                class="flex justify-center items-center w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 shrink-0">
                                <span class="material-symbols-outlined text-[13px]">chat</span>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- ================= 6️⃣ الملاحظات السند المكتوبة (تأخذ العرض كاملاً col-span-2 تحت الكرتين)
                ================= --}}
                @if ($passenger->note)
                    <div
                        class="col-span-2 p-5 bg-amber-50/40 rounded-[2rem] border border-amber-100/60 shadow-sm dark:bg-amber-500/5 dark:border-amber-500/10">
                        <h3 class="flex items-center gap-1.5 text-xs font-black text-amber-600 mb-2.5">
                            <span class="material-symbols-outlined text-[18px]">description</span>
                            ملاحظات السند الحالية
                        </h3>
                        <p class="text-xs font-medium leading-relaxed text-slate-600 dark:text-gray-300 pr-1">
                            {{ $passenger->note }}
                        </p>
                    </div>
                @endif

                @if ($passenger->status === 'cancel' && !empty($passenger->cancel_reason))
                    <div
                        class="col-span-2 p-5 bg-rose-50/40 rounded-[2rem] border border-rose-100/60 shadow-sm dark:bg-rose-500/5 dark:border-rose-500/10">
                        <h3 class="flex items-center gap-1.5 text-xs font-black text-rose-600 mb-2.5">
                            <span class="material-symbols-outlined text-[18px]">block</span>
                            سبب الإلغاء
                        </h3>
                        <p class="text-xs font-medium leading-relaxed text-slate-700 dark:text-gray-300 pr-1">
                            {{ $passenger->cancel_reason }}
                        </p>
                    </div>
                @endif

            </div>
        </div>

        {{-- ====================== Status Modal (Cancel Only) ====================== --}}
        <template x-teleport="body">
            <div x-show="showStatusModal" x-cloak
                class="fixed inset-0 z-[99999] flex justify-center items-center p-4 sm:p-0 font-body"
                dir="rtl">

                {{-- الخلفية المظللة --}}
                <div x-show="showStatusModal" x-transition.opacity.duration.300ms
                    class="absolute inset-0 backdrop-blur-sm bg-gray-900/60 dark:bg-black/70"
                    @click="showStatusModal = false"></div>

                {{-- المودال نفسه --}}
                <div x-show="showStatusModal"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative bg-white dark:bg-boxdark w-full max-w-md rounded-[2rem] shadow-2xl p-8 text-center border border-gray-100 dark:border-boxdark-2">

                    {{-- الأيقونة --}}
                    <div
                        class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-amber-50 dark:bg-amber-500/10 text-amber-500 rounded-[1.5rem] border border-amber-100 dark:border-amber-500/20">
                        <span class="text-4xl material-symbols-outlined">block</span>
                    </div>

                    <h3 class="mb-3 text-2xl font-black font-headline text-on-surface dark:text-white">
                        تأكيد إلغاء الراكب</h3>

                    <p class="mb-8 text-sm font-semibold leading-relaxed text-gray-500 dark:text-gray-400">
                        هل أنت متأكد من إلغاء الراكب
                        @if ($passenger->passenger_name)
                            <span class="block font-black text-on-surface dark:text-white text-base mt-1">
                                {{ $passenger->passenger_name }}
                            </span>
                        @endif
                        <span class="text-base font-black text-on-surface dark:text-white font-headline dir-ltr inline-block mt-1">
                            {{ $passenger->passenger_number }}
                        </span>؟<br>
                        <span class="text-amber-500/80">سيتم تغيير حالة الراكب إلى ملغي.</span>
                    </p>

                    {{-- فورم الإرسال --}}
                    <form action="{{ route('passengers.updateStatus', $passenger->id) }}" method="POST" @submit="isSubmitting = true"
                        class="space-y-4">
                        @csrf
                        <input type="hidden" name="status" value="cancel">

                        @if ($passenger->broker)
                            <div class="p-3 bg-gray-50 dark:bg-boxdark-2 rounded-2xl border border-gray-100 dark:border-boxdark flex items-center justify-between text-right">
                                <span class="text-xs font-bold text-gray-400">الوسيط المعتمد:</span>
                                <span class="text-xs font-black text-gray-700 dark:text-white">{{ $passenger->broker->name }}</span>
                            </div>
                        @endif

                        <div class="text-right">
                            <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-300">
                                <span>واتساب الوسيط لإرسال الإلغاء</span>
                                <span class="text-xs font-normal text-gray-400">(اختياري لتعديل أو تعيين الرقم)</span>
                            </label>
                            <input type="text" name="broker_phone"
                                value="{{ $passenger->broker?->phone }}"
                                placeholder="مثال: 967770000000"
                                dir="ltr"
                                class="p-3 w-full text-xs font-bold text-gray-700 bg-gray-50 rounded-2xl border border-gray-200 outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white focus:ring-2 focus:ring-amber-500/20 font-body placeholder:text-gray-400">
                        </div>

                        <div class="text-right">
                            <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-300">
                                <span>سبب الإلغاء</span>
                                <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                            </label>
                            <textarea name="cancel_reason" rows="3"
                                placeholder="اكتب سبب إلغاء الحجز..."
                                class="p-3 w-full text-xs font-bold text-gray-700 bg-gray-50 rounded-2xl border border-gray-200 outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white focus:ring-2 focus:ring-amber-500/20 font-body placeholder:text-gray-400"></textarea>
                        </div>

                        <div class="flex items-center gap-2 pt-1 text-right">
                            <input type="checkbox" name="send_whatsapp" id="send_whatsapp_mobile_show" value="1" checked
                                class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 dark:bg-boxdark-2 dark:border-boxdark">
                            <label for="send_whatsapp_mobile_show" class="text-xs font-bold text-gray-700 dark:text-gray-300 cursor-pointer">
                                إرسال إشعار الإلغاء للوسيط عبر واتساب تلقائياً
                            </label>
                        </div>

                        <div class="flex gap-3">
                            <button type="button" @click="showStatusModal = false"
                                class="flex-1 py-3.5 text-sm font-black rounded-xl transition-all text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-boxdark-2 dark:text-gray-300 dark:hover:bg-gray-700 active:scale-95 font-headline">
                                تراجع
                            </button>

                            <button type="submit" :disabled="isSubmitting"
                                class="flex flex-1 gap-2 justify-center items-center py-3.5 text-sm font-black text-white bg-amber-500 rounded-xl shadow-lg transition-all hover:bg-amber-600 shadow-amber-500/30 active:scale-95 font-headline disabled:opacity-70 disabled:cursor-not-allowed">
                                <span x-show="!isSubmitting">نعم، إلغاء الراكب</span>
                                <span x-show="isSubmitting"
                                    class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- ====================== Rebook Modal (Restore / Confirm Booking) ====================== --}}
        <template x-teleport="body">
            <div x-show="showRebookModal" x-cloak
                class="fixed inset-0 z-[99999] flex justify-center items-center p-4 sm:p-0 font-body"
                dir="rtl">

                {{-- الخلفية المظللة --}}
                <div x-show="showRebookModal" x-transition.opacity.duration.300ms
                    class="absolute inset-0 backdrop-blur-sm bg-gray-900/60 dark:bg-black/70"
                    @click="showRebookModal = false"></div>

                {{-- المودال نفسه --}}
                <div x-show="showRebookModal"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative bg-white dark:bg-boxdark w-full max-w-md rounded-[2rem] shadow-2xl p-7 text-center border border-gray-100 dark:border-boxdark-2">

                    {{-- الأيقونة --}}
                    <div
                        class="flex justify-center items-center mx-auto mb-5 w-16 h-16 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-[1.25rem] border border-emerald-100 dark:border-emerald-500/20 shadow-sm">
                        <span class="text-3xl material-symbols-outlined">history</span>
                    </div>

                    <h3 class="mb-2 text-xl font-black font-headline text-slate-800 dark:text-white">
                        تأكيد إعادة الحجز
                    </h3>

                    <p class="mb-4 text-xs font-semibold leading-relaxed text-gray-500 dark:text-gray-400">
                        هل أنت متأكد من إعادة حجز الراكب
                        @if ($passenger->passenger_name)
                            <span class="block font-black text-slate-800 dark:text-white text-sm mt-1">
                                {{ $passenger->passenger_name }}
                            </span>
                        @endif
                        <span class="text-sm font-black text-slate-800 dark:text-white font-headline dir-ltr inline-block mt-0.5">
                            {{ $passenger->passenger_number }}
                        </span>؟<br>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">سيتم تغيير حالة الراكب إلى قيد الانتظار وإلغاء الإلغاء.</span>
                    </p>

                    @if ($passenger->broker)
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 mb-4 text-[11px] font-bold text-emerald-700 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400 rounded-full border border-emerald-200 dark:border-emerald-500/20">
                            <span class="material-symbols-outlined text-[15px]">handshake</span>
                            <span>الوسيط:</span>
                            <span>{{ $passenger->broker->name }}</span>
                        </div>
                    @endif

                    {{-- فورم الإرسال --}}
                    <form action="{{ route('passengers.updateStatus', $passenger->id) }}" method="POST" @submit="isSubmitting = true"
                        class="space-y-3.5 text-right">
                        @csrf
                        <input type="hidden" name="status" value="pending">
                        <input type="hidden" name="is_rebook" value="1">

                        <div>
                            <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-300">
                                <span>رقم هاتف الوسيط (واتساب)</span>
                                <span class="text-[10px] font-normal text-gray-400">(لإرسال إشعار إعادة الحجز)</span>
                            </label>
                            <input type="text" name="broker_phone"
                                value="{{ $passenger->broker?->phone }}"
                                placeholder="مثال: 967770000000"
                                dir="ltr"
                                class="p-3 w-full text-xs font-bold text-gray-700 bg-gray-50 rounded-2xl border border-gray-200 outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white focus:ring-2 focus:ring-emerald-500/20 font-body placeholder:text-gray-400 text-right">
                        </div>

                        <div>
                            <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-300">
                                <span>ملاحظات</span>
                                <span class="text-[10px] font-normal text-gray-400">(اختياري)</span>
                            </label>
                            <textarea name="note" rows="2"
                                placeholder="أي ملاحظات حول إعادة الحجز..."
                                class="p-3 w-full text-xs font-bold text-gray-700 bg-gray-50 rounded-2xl border border-gray-200 outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white focus:ring-2 focus:ring-emerald-500/20 font-body placeholder:text-gray-400">{{ $passenger->note }}</textarea>
                        </div>

                        <div class="flex items-center gap-2 p-2.5 text-right bg-emerald-50/70 dark:bg-emerald-500/10 rounded-2xl border border-emerald-100 dark:border-emerald-500/20">
                            <input type="checkbox" name="send_whatsapp" id="send_whatsapp_rebook_mobile_show" value="1" checked
                                class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 dark:bg-boxdark-2 dark:border-boxdark cursor-pointer">
                            <label for="send_whatsapp_rebook_mobile_show" class="text-xs font-bold text-gray-700 dark:text-gray-200 cursor-pointer flex-1">
                                إرسال إشعار تأكيد الحجز للوسيط عبر واتساب 💬
                            </label>
                        </div>

                        <div class="flex gap-2.5 pt-2">
                            <button type="button" @click="showRebookModal = false"
                                class="flex-1 py-3 text-xs font-black rounded-xl transition-all text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-boxdark-2 dark:text-gray-300 dark:hover:bg-gray-700 active:scale-95 font-headline">
                                تراجع
                            </button>

                            <button type="submit" :disabled="isSubmitting"
                                class="flex flex-1 gap-1.5 justify-center items-center py-3 text-xs font-black text-white bg-emerald-600 rounded-xl shadow-lg transition-all hover:bg-emerald-700 shadow-emerald-600/30 active:scale-95 font-headline disabled:opacity-70 disabled:cursor-not-allowed">
                                <span x-show="!isSubmitting">تأكيد إعادة الحجز</span>
                                <span x-show="isSubmitting"
                                    class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- ====================== Delete Modal ====================== --}}
        <template x-teleport="body">
            <div x-show="showDeleteModal" x-cloak
                class="fixed inset-0 z-[99999] flex justify-center items-center p-4 sm:p-0 font-body"
                dir="rtl">

                {{-- الخلفية المظللة --}}
                <div x-show="showDeleteModal" x-transition.opacity.duration.300ms
                    class="absolute inset-0 backdrop-blur-sm bg-gray-900/60 dark:bg-black/70"
                    @click="showDeleteModal = false"></div>

                {{-- المودال نفسه --}}
                <div x-show="showDeleteModal"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative bg-white dark:bg-boxdark w-full max-w-md rounded-[2rem] shadow-2xl p-8 text-center border border-gray-100 dark:border-boxdark-2">

                    {{-- الأيقونة --}}
                    <div
                        class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-rose-50 dark:bg-rose-500/10 text-rose-500 rounded-[1.5rem] border border-rose-100 dark:border-rose-500/20">
                        <span class="text-4xl material-symbols-outlined">delete_forever</span>
                    </div>

                    <h3 class="mb-3 text-2xl font-black font-headline text-on-surface dark:text-white">
                        تأكيد الحذف</h3>

                    <p class="mb-8 text-sm font-semibold leading-relaxed text-gray-500 dark:text-gray-400">
                        هل أنت متأكد من حذف الراكب
                        @if ($passenger->passenger_name)
                            <span class="block font-black text-on-surface dark:text-white text-base mt-1">
                                {{ $passenger->passenger_name }}
                            </span>
                        @endif
                        <span class="text-base font-black text-on-surface dark:text-white font-headline dir-ltr inline-block mt-1">
                            {{ $passenger->passenger_number }}
                        </span>؟<br>
                        <span class="text-rose-500/80">لا يمكن التراجع عن هذا الإجراء.</span>
                    </p>

                    {{-- فورم الإرسال --}}
                    <form action="{{ route('passengers.destroy', $passenger->id) }}" method="POST" @submit="isSubmitting = true"
                        class="flex gap-3">
                        @csrf
                        @method('DELETE')

                        <button type="button" @click="showDeleteModal = false"
                            class="flex-1 py-3.5 text-sm font-black rounded-xl transition-all text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-boxdark-2 dark:text-gray-300 dark:hover:bg-gray-700 active:scale-95 font-headline">
                            تراجع
                        </button>

                        <button type="submit" :disabled="isSubmitting"
                            class="flex flex-1 gap-2 justify-center items-center py-3.5 text-sm font-black text-white bg-rose-500 rounded-xl shadow-lg transition-all hover:bg-rose-600 shadow-rose-500/30 active:scale-95 font-headline disabled:opacity-70 disabled:cursor-not-allowed">
                            <span x-show="!isSubmitting">نعم، احذف</span>
                            <span x-show="isSubmitting"
                                class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                        </button>
                    </form>
                </div>
            </div>
        </template>
            </div>
        </div>

        <style>
            @media print {
                body {
                    background: white !important;
                }

                a,
                button,
                alert,
                nav,
                header {
                    display: none !important;
                }

                .bg-white,
                .dark\:bg-boxdark,
                .bg-slate-50\/50 {
                    background: white !important;
                    border-color: #f1f5f9 !important;
                }

                .text-slate-800,
                .dark\:text-white {
                    color: #1e293b !important;
                }

                .shadow-sm {
                    box-shadow: none !important;
                }
            }
        </style>
    </div>

    <script>
        async function sendPassengerBookingDirect(passengerId, btn, fallbackLink = '') {
            if (!passengerId || btn.disabled) return;

            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = `
                <span class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                <span>جاري الإرسال...</span>
            `;

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const url = "{{ route('whatsapp.passengerBooking', ':id') }}".replace(':id', passengerId);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                        <span class="text-emerald-600">تم الإرسال بنجاح ✅</span>
                    `;
                    showNotificationToast(data.message || 'تم إرسال تفاصيل الحجز للراكب بنجاح ✅', 'success');
                } else if (data.fallback && data.whatsapp_link) {
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[16px] text-amber-500">open_in_new</span>
                        <span class="text-amber-500">جاري فتح الواتساب...</span>
                    `;
                    showNotificationToast(data.message || 'خدمة الواتساب غير متصلة، جاري فتح الواتساب كرسالة...', 'warning');
                    window.open(data.whatsapp_link, '_blank');
                } else {
                    if (fallbackLink) {
                        window.open(fallbackLink, '_blank');
                    }
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[16px] text-amber-500">open_in_new</span>
                        <span class="text-amber-500">تم التحويل للواتساب</span>
                    `;
                    showNotificationToast(data.message || 'تم فتح الواتساب للإرسال اليدوي', 'warning');
                }
            } catch (error) {
                console.error('WhatsApp direct booking send error:', error);
                if (fallbackLink) {
                    window.open(fallbackLink, '_blank');
                    showNotificationToast('تعذر الاتصال بالسيرفر، تم فتح الواتساب للإرسال كرسالة.', 'warning');
                } else {
                    showNotificationToast('حدث خطأ في الاتصال بالخادم. يرجى المحاولة لاحقاً.', 'error');
                }
                btn.innerHTML = `
                    <span class="material-symbols-outlined text-[16px] text-amber-500">open_in_new</span>
                    <span class="text-amber-500">تم التحويل للواتساب</span>
                `;
            } finally {
                setTimeout(() => {
                    btn.disabled = false;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                    btn.innerHTML = originalHtml;
                }, 3000);
            }
        }

        async function sendPassengerCancelDirect(passengerId, btn, fallbackLink = '') {
            if (!passengerId || btn.disabled) return;

            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = `
                <span class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                <span>جاري الإرسال...</span>
            `;

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const url = "{{ route('whatsapp.passengerCancel', ':id') }}".replace(':id', passengerId);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                        <span class="text-emerald-600">تم الإرسال بنجاح ✅</span>
                    `;
                    showNotificationToast(data.message || 'تم إرسال إشعار الإلغاء للوسيط بنجاح ✅', 'success');
                } else if (data.fallback && data.whatsapp_link) {
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[16px] text-amber-500">open_in_new</span>
                        <span class="text-amber-500">جاري فتح الواتساب...</span>
                    `;
                    showNotificationToast(data.message || 'خدمة الواتساب غير متصلة، جاري فتح الواتساب كرسالة...', 'warning');
                    window.open(data.whatsapp_link, '_blank');
                } else {
                    if (fallbackLink) {
                        window.open(fallbackLink, '_blank');
                    }
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[16px] text-amber-500">open_in_new</span>
                        <span class="text-amber-500">تم التحويل للواتساب</span>
                    `;
                    showNotificationToast(data.message || 'تم فتح الواتساب للإرسال اليدوي', 'warning');
                }
            } catch (error) {
                console.error('WhatsApp direct cancel send error:', error);
                if (fallbackLink) {
                    window.open(fallbackLink, '_blank');
                    showNotificationToast('تعذر الاتصال بالسيرفر، تم فتح الواتساب للإرسال كرسالة.', 'warning');
                } else {
                    showNotificationToast('حدث خطأ في الاتصال بالخادم. يرجى المحاولة لاحقاً.', 'error');
                }
                btn.innerHTML = `
                    <span class="material-symbols-outlined text-[16px] text-amber-500">open_in_new</span>
                    <span class="text-amber-500">تم التحويل للواتساب</span>
                `;
            } finally {
                setTimeout(() => {
                    btn.disabled = false;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                    btn.innerHTML = originalHtml;
                }, 3000);
            }
        }

        async function sendPassengerRebookDirect(passengerId, btn, fallbackLink = '') {
            if (!passengerId || btn.disabled) return;

            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = `
                <span class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                <span>جاري الإرسال...</span>
            `;

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const url = "{{ route('whatsapp.passengerRebook', ':id') }}".replace(':id', passengerId);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                        <span class="text-emerald-600">تم الإرسال بنجاح ✅</span>
                    `;
                    showNotificationToast(data.message || 'تم إرسال إشعار إعادة الحجز للوسيط بنجاح ✅', 'success');
                } else if (data.fallback && data.whatsapp_link) {
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[16px] text-amber-500">open_in_new</span>
                        <span class="text-amber-500">جاري فتح الواتساب...</span>
                    `;
                    showNotificationToast(data.message || 'خدمة الواتساب غير متصلة، جاري فتح الواتساب كرسالة...', 'warning');
                    window.open(data.whatsapp_link, '_blank');
                } else {
                    if (fallbackLink) {
                        window.open(fallbackLink, '_blank');
                    }
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[16px] text-amber-500">open_in_new</span>
                        <span class="text-amber-500">تم التحويل للواتساب</span>
                    `;
                    showNotificationToast(data.message || 'تم فتح الواتساب للإرسال اليدوي', 'warning');
                }
            } catch (error) {
                console.error('WhatsApp direct rebook send error:', error);
                if (fallbackLink) {
                    window.open(fallbackLink, '_blank');
                    showNotificationToast('تعذر الاتصال بالسيرفر، تم فتح الواتساب للإرسال كرسالة.', 'warning');
                } else {
                    showNotificationToast('حدث خطأ في الاتصال بالخادم. يرجى المحاولة لاحقاً.', 'error');
                }
                btn.innerHTML = `
                    <span class="material-symbols-outlined text-[16px] text-amber-500">open_in_new</span>
                    <span class="text-amber-500">تم التحويل للواتساب</span>
                `;
            } finally {
                setTimeout(() => {
                    btn.disabled = false;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                    btn.innerHTML = originalHtml;
                }, 3000);
            }
        }

        function showNotificationToast(message, type = 'success') {
            const existingToast = document.getElementById('direct-whatsapp-toast');
            if (existingToast) existingToast.remove();

            const toast = document.createElement('div');
            toast.id = 'direct-whatsapp-toast';
            let bgClass = 'bg-emerald-600 shadow-emerald-600/25';
            let icon = 'check_circle';

            if (type === 'error') {
                bgClass = 'bg-red-600 shadow-red-600/25';
                icon = 'error';
            } else if (type === 'warning') {
                bgClass = 'bg-amber-600 shadow-amber-600/25';
                icon = 'open_in_new';
            }

            toast.className = `fixed bottom-24 left-4 right-4 md:left-6 md:right-auto z-[9999] flex items-center justify-center gap-3 px-5 py-3.5 rounded-2xl shadow-xl text-white font-bold text-xs sm:text-sm transition-all duration-300 transform translate-y-0 ${bgClass}`;

            toast.innerHTML = `
                <span class="material-symbols-outlined text-[20px]">${icon}</span>
                <span>${message}</span>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 400);
            }, 3500);
        }
    </script>

    @if (session('whatsapp_redirect_url'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const url = @js(session('whatsapp_redirect_url'));
                if (url) {
                    window.open(url, '_blank');
                }
            });
        </script>
    @endif
@endsection