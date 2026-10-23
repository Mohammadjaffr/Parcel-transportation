@extends('mobile.layouts.app')

@section('title', 'الركاب')

@section('content')
    <div x-data="passengerMobileRegistry()"
        class="flex relative flex-col gap-6 pb-24 min-h-screen font-body bg-slate-50/50">

        <div class="flex justify-between items-center px-4 mt-4">
            <div>
                <h1 class="text-2xl font-black tracking-tight font-headline text-slate-800">الركاب</h1>
                <p class="mt-0.5 text-xs font-semibold text-slate-400">
                    إجمالي <span class="text-primary">{{ $passengers->total() ?? 0 }}</span> راكب مسجل
                </p>
            </div>
            <div class="flex gap-2">
                <a :href="'{{ route('receipt.generate', ['type' => 'all_passenger', 'id' => '__ID__']) }}'.replace('__ID__', getPrintUrl())" target="_blank"
                    class="flex justify-center items-center h-12 px-4 gap-1 text-primary bg-primary/10 hover:bg-primary hover:text-white rounded-[1rem] shadow-sm transition-all active:scale-95 border border-primary/20">
                    <span class="text-xl material-symbols-outlined">print</span>
                    <span class="text-sm font-bold">طباعة الكشف</span>
                </a>
                <a href="{{ route('passengers.create') }}">
                    <button type="button"
                        class="flex justify-center items-center w-12 h-12 text-white rounded-[1rem] shadow-lg transition-all bg-primary shadow-primary/30 active:scale-95">
                        <span class="text-2xl material-symbols-outlined"
                            style="font-variation-settings: 'FILL' 1;">person_add</span>
                    </button>
                </a>
            </div>
        </div>


        <div class="px-4">
            <div class="relative group">
                <span
                    class="absolute right-4 top-1/2 transition-colors -translate-y-1/2 material-symbols-outlined text-slate-400 group-focus-within:text-primary">search</span>
                <input type="text" x-model="searchQuery" placeholder="ابحث برقم الراكب، العميل، السائق أو المكان..."
                    class="w-full h-14 pr-12 pl-12 rounded-[1.25rem] border-none bg-white shadow-sm ring-1 ring-slate-200/60 focus:ring-2 focus:ring-primary/40 transition-all font-headline text-sm text-slate-700 outline-none">

                <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''" style="display: none;"
                    class="flex absolute left-4 top-1/2 justify-center items-center w-8 h-8 rounded-xl transition-transform -translate-y-1/2 bg-slate-50 text-slate-400 active:scale-95">
                    <span class="text-lg material-symbols-outlined">close</span>
                </button>
            </div>
        </div>

        <div class="px-4 -mt-2">
            <form method="GET" action="{{ route('passengers.index') }}" class="flex gap-2 items-center">
                <input type="hidden" name="isMobile" value="1">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <div class="relative flex-1">
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-lg pointer-events-none">calendar_today</span>
                    <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
                        onclick="try { this.showPicker(); } catch(e) {}"
                        class="w-full h-11 pr-10 pl-3 rounded-2xl border-none bg-white shadow-sm ring-1 ring-slate-200/60 font-headline text-xs text-slate-700 outline-none cursor-pointer">
                </div>
                <button type="button" onclick="const f=this.form; f.date.value='{{ now()->format('Y-m-d') }}'; f.submit();"
                    class="h-11 px-3.5 text-xs font-bold rounded-2xl whitespace-nowrap transition-all {{ request('date') === now()->format('Y-m-d') || request('date') === 'today' ? 'bg-primary text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200/60' }}">
                    اليوم
                </button>
                @if(request('date'))
                    <a href="{{ route('passengers.index', array_filter(request()->except('date', 'page'))) }}"
                        class="h-11 px-3 flex items-center justify-center text-xs font-bold rounded-2xl bg-white text-rose-500 ring-1 ring-rose-200 hover:bg-rose-50 transition-all"
                        title="إلغاء فلتر التاريخ">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </a>
                @endif
            </form>
        </div>

        <div class="pr-4 pl-4 -mt-2">
            <div class="flex overflow-x-auto gap-2 pb-2 custom-scrollbar"
                style="scrollbar-width: none; -ms-overflow-style: none;">
                <style>
                    .custom-scrollbar::-webkit-scrollbar {
                        display: none;
                    }
                </style>

                <button type="button" @click="selectedStatus = ''" :class="selectedStatus === '' ? 'bg-slate-800 text-white shadow-md' :
                                'bg-white text-slate-500 border border-slate-200'"
                    class="px-4 py-2.5 text-xs font-bold rounded-[1rem] whitespace-nowrap transition-all font-headline active:scale-95">
                    الكل
                </button>
                <button type="button" @click="selectedStatus = 'pending'" :class="selectedStatus === 'pending' ? 'bg-slate-500 text-white shadow-md' :
                                'bg-white text-slate-500 border border-slate-200'"
                    class="px-4 py-2.5 text-xs font-bold rounded-[1rem] whitespace-nowrap transition-all font-headline active:scale-95">
                    قيد الانتظار
                </button>
                <button type="button" @click="selectedStatus = 'completed'" :class="selectedStatus === 'completed' ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' :
                                'bg-white text-slate-500 border border-slate-200'"
                    class="px-4 py-2.5 text-xs font-bold rounded-[1rem] whitespace-nowrap transition-all font-headline active:scale-95">
                    مكتمل
                </button>
                <button type="button" @click="selectedStatus = 'cancel'" :class="selectedStatus === 'cancel' ? 'bg-rose-500 text-white shadow-md shadow-rose-500/20' :
                                'bg-white text-slate-500 border border-slate-200'"
                    class="px-4 py-2.5 text-xs font-bold rounded-[1rem] whitespace-nowrap transition-all font-headline active:scale-95">
                    ملغي
                </button>
            </div>
        </div>

        <div class="px-4 space-y-4">
            @forelse($passengers as $passenger)
                @php
                    $rawStatus = strtolower($passenger->status ?? 'pending');
                    if ($rawStatus == 'completed' || $rawStatus == 'مكتمل') {
                        $statusLabel = 'مكتمل';
                        $statusKey = 'completed';
                        $statusClass = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                    } elseif ($rawStatus == 'cancel' || $rawStatus == 'ملغي') {
                        $statusLabel = 'ملغي';
                        $statusKey = 'cancel';
                        $statusClass = 'bg-rose-50 text-rose-600 border-rose-100';
                    } else {
                        $statusLabel = 'قيد الانتظار';
                        $statusKey = 'pending';
                        $statusClass = 'bg-amber-50 text-amber-600 border-amber-100';
                    }
                @endphp

                <div x-data Dessert="{ expanded: false }"
                    x-show="matchSearch({{ json_encode($passenger->passenger_number) }}, {{ json_encode($passenger->destination ?? $passenger->location) }}, {{ json_encode($passenger->broker?->name ?? '') }}, '', {{ json_encode($passenger->driver->name ?? '') }}, {{ json_encode($passenger->driver->phone ?? '') }}, {{ json_encode($statusKey) }}, {{ json_encode($passenger->passenger_name ?? '') }})"
                    class="bg-white rounded-[1.75rem] p-5 shadow-sm border border-slate-100 relative transition-all duration-300 passenger-card cursor-pointer hover:border-slate-200"
                    :class="expanded ? 'shadow-md ring-1 ring-slate-100/50' : ''" @click="expanded = !expanded">

                    <div class="flex justify-between items-center mb-4">
                        <div class="flex gap-2 items-center">
                            <div
                                class="flex justify-center items-center w-8 h-8 rounded-lg border shadow-inner bg-slate-50 text-slate-700 border-slate-100">
                                <span class="text-base material-symbols-outlined">layers</span>
                            </div>
                            <h4 class="font-mono text-base font-black text-slate-800">#{{ $passenger->id }}</h4>
                        </div>

                        <div class="flex gap-2 items-center" @click.stop>
                            <div class="flex flex-col items-end">
                                <span
                                    class="px-3 py-1.5 text-[10px] font-bold rounded-xl border shadow-sm font-headline {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                                @if ($statusKey == 'cancel' && !empty($passenger->cancel_reason))
                                    <span class="text-[10px] text-rose-500 font-bold mt-1 max-w-[120px] truncate" title="سبب الإلغاء: {{ $passenger->cancel_reason }}">
                                        السبب: {{ $passenger->cancel_reason }}
                                    </span>
                                @endif
                            </div>

                            @if ($statusKey == 'cancel')
                                <button type="button"
                                    @click="openRebookModal({
                                        id: {{ $passenger->id }},
                                        passenger_name: @js($passenger->passenger_name),
                                        passenger_number: @js($passenger->passenger_number),
                                        broker_name: @js($passenger->broker?->name ?? 'غير محدد'),
                                        broker_phone: @js($passenger->broker?->phone ?? ''),
                                        status: 'pending',
                                        note: @js($passenger->note)
                                    })"
                                    title="إعادة الحجز"
                                    class="flex justify-center items-center w-8 h-8 rounded-full border bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border-emerald-100 shadow-sm active:scale-95">
                                    <span class="material-symbols-outlined text-[18px]">history</span>
                                </button>
                            @endif

                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" type="button"
                                    class="flex justify-center items-center w-8 h-8 rounded-full border bg-slate-50 text-slate-500 hover:bg-slate-100 border-slate-100">
                                    <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute left-0 top-full mt-1 z-[60] w-44 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden py-1">

                                    <a href="{{ route('passengers.show', $passenger->id) }}"
                                        class="flex gap-2 items-center px-3 py-2 w-full text-xs font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600 font-headline">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span> عرض التفاصيل
                                    </a>

                                    <a href="{{ route('passengers.edit', $passenger->id) }}"
                                        class="flex gap-2 items-center px-3 py-2 w-full text-xs font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-600 font-headline">
                                        <span class="material-symbols-outlined text-[16px]">edit</span> تعديل
</a>
                                    @if ($statusKey == 'pending')
                                        @php
                                            $whatsappUrl = \App\Services\WhatsApp\WhatsAppLinkService::generate($passenger, 'passengerBooking');
                                        @endphp
                                        @if ($whatsappUrl)
                                            <button type="button"
                                                onclick="sendPassengerBookingDirect({{ $passenger->id }}, this, '{{ $whatsappUrl }}')"
                                                class="flex gap-2 items-center px-3 py-2 w-full text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 font-headline">
                                                <span class="material-symbols-outlined text-[16px]">send</span>
                                                إرسال الحجز
                                            </button>
                                        @endif
                                    @endif
                                    @if ($statusKey == 'cancel')
                                        <button type="button"
                                            @click="openRebookModal({
                                                id: {{ $passenger->id }},
                                                passenger_name: @js($passenger->passenger_name),
                                                passenger_number: @js($passenger->passenger_number),
                                                broker_name: @js($passenger->broker?->name ?? 'غير محدد'),
                                                broker_phone: @js($passenger->broker?->phone ?? ''),
                                                status: 'pending',
                                                note: @js($passenger->note)
                                            }); open = false"
                                            class="flex gap-2 items-center px-3 py-2 w-full text-xs font-bold text-emerald-700 hover:bg-emerald-50 font-headline">
                                            <span class="material-symbols-outlined text-[16px]">history</span>
                                            إعادة الحجز
                                        </button>
                                        @php
                                            $whatsappCancelUrl = \App\Services\WhatsApp\WhatsAppLinkService::generate($passenger, 'passengerCancel');
                                        @endphp
                                        @if ($whatsappCancelUrl)
                                            <button type="button"
                                                onclick="sendPassengerCancelDirect({{ $passenger->id }}, this, '{{ $whatsappCancelUrl }}')"
                                                class="flex gap-2 items-center px-3 py-2 w-full text-xs font-bold text-rose-700 hover:bg-rose-50 font-headline">
                                                <span class="material-symbols-outlined text-[16px]">send</span>
                                                إرسال الإلغاء للوسيط
                                            </button>
                                        @endif
                                    @endif
                                    @if ($statusKey != 'cancel' && $statusKey != 'completed')
                                        <button type="button"
                                            @click="openStatusModal({
                                                id: {{ $passenger->id }},
                                                passenger_name: @js($passenger->passenger_name),
                                                passenger_number: @js($passenger->passenger_number),
                                                broker_name: @js($passenger->broker?->name ?? 'غير محدد'),
                                                broker_phone: @js($passenger->broker?->phone ?? ''),
                                                status: 'cancel',
                                                cancel_reason: @js($passenger->cancel_reason ?? ''),
                                                note: @js($passenger->note)
                                            }); open = false"
                                            class="flex gap-2 items-center px-3 py-2 w-full text-xs font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-600 font-headline">
                                            <span class="material-symbols-outlined text-[16px]">block</span> إلغاء الراكب
                                        </button>
                                    @endif
                                     @if ($statusKey != 'completed')
                                                <button type="button"
                                                    @click="openDeleteModal({{ $passenger->id }}, @js($passenger->passenger_number), @js($passenger->passenger_name)); open = false"
                                                    class="flex gap-2 items-center px-3 py-2 w-full text-xs font-bold text-slate-700 hover:bg-rose-50 hover:text-rose-600 font-headline">
                                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                                    حذف الراكب
                                                </button>
                                            @endif
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex justify-between items-center p-3 mb-4 rounded-2xl border bg-slate-50/50 border-slate-100/60">
                        <div class="flex gap-3 items-center min-w-0">
                            <div
                                class="flex justify-center items-center w-10 h-10 text-sm font-black text-emerald-600 bg-emerald-50 rounded-xl border border-emerald-100/70 shrink-0">
                                {{ mb_substr($passenger->broker?->name ?? 'و', 0, 1, 'UTF-8') }}
                            </div>
                            <div class="min-w-0">
                                <span class="text-[9px] font-bold text-slate-400 block">الوسيط </span>
                                <h3 class="text-xs font-black truncate text-slate-800 font-headline">
                                    {{ $passenger->broker?->name ?? 'وسيط غير محدد' }}
                                </h3>
                            </div>
                        </div>

                        <div class="text-left" @click.stop>
                            @if ($passenger->passenger_name)
                                <span class="text-[11px] font-black text-slate-800 block truncate max-w-[130px] mb-0.5" title="{{ $passenger->passenger_name }}">
                                    {{ $passenger->passenger_name }}
                                </span>
                            @else
                                <span class="text-[9px] font-bold text-slate-400 block mb-0.5">رقم هاتف الراكب</span>
                            @endif
                            <span class="px-2.5 py-1 font-mono text-xs font-black rounded-lg text-primary dir-ltr bg-primary/5 inline-block">
                                <x-phone-number :value="$passenger->passenger_number" class="text-primary" />
                            </span>
                            @if ($passenger->note)
                                <span class="text-[10px] text-slate-500 block mt-0.5 truncate max-w-[120px]" title="{{ $passenger->note }}">
                                    {{ $passenger->note }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if(!empty($passenger->destination))
                        <div class="relative pr-6 my-4 space-y-3 border-r-2 border-dashed border-slate-200">
                            <div class="relative">
                                <span
                                    class="absolute right-[-29px] top-0.5 w-2.5 h-2.5 rounded-full bg-slate-300 border-2 border-white ring-4 ring-slate-100"></span>
                                <div class="text-xs">
                                    <span class="text-slate-400 font-bold block text-[9px]">مكان الصعود:</span>
                                    <span class="font-black text-slate-700">{{ $passenger->pickup_location ?? 'غير محدد' }}</span>
                                </div>
                            </div>

                            <div class="relative">
                                <span
                                    class="absolute right-[-29px] top-0.5 w-2.5 h-2.5 rounded-full bg-amber-500 border-2 border-white ring-4 ring-amber-100"></span>
                                <div class="text-xs">
                                    <span class="text-amber-500 font-bold block text-[9px]">الوجهة:</span>
                                    <span class="font-black text-amber-600">{{ $passenger->destination }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3 pt-3 mt-3 text-xs border-t border-slate-100">
                        <div>
                            <span class="text-slate-400 font-bold block text-[9px] mb-0.5">تاريخ الرحلة:</span>
                            <div class="flex gap-1 items-center font-bold text-slate-700">
                                <span class="material-symbols-outlined text-[14px] text-slate-400">calendar_today</span>
                                <span>{{ \Carbon\Carbon::parse($passenger->date)->format('Y/m/d') }}</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold block text-[9px] mb-0.5">عدد الركاب:</span>
                            <div class="flex gap-1.5 items-center font-black text-slate-700">
                                <span class="material-symbols-outlined text-[14px] text-slate-400">group</span>
                                <span>{{ $passenger->count }} راكب</span>
                                @if($passenger->vehicle_type)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 text-[10px] font-bold">
                                        {{ $passenger->vehicle_type }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            @empty
                <div
                    class="py-20 flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-200 shadow-sm">
                    <div class="flex justify-center items-center mb-6 w-24 h-24 rounded-full bg-slate-50 text-slate-300">
                        <span class="text-6xl material-symbols-outlined">group_off</span>
                    </div>
                    <p class="text-lg font-bold font-headline text-slate-400">لم نعثر على أي ركاب</p>
                </div>
            @endforelse
        </div>

        <div class="px-4 mt-4" x-show="searchQuery === '' && selectedStatus === ''">
            {{ $passengers->links('vendor.pagination.mobile') }}
        </div>

        {{-- ================= Status Modal - Cancel Only (Bottom Sheet) ================= --}}
        <div x-show="showStatusModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60" @click="closeModals()"></div>

            <div
                class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-8 text-center pointer-events-auto">
                <div @click="closeModals()"
                    class="mx-auto mb-6 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
                </div>

                <div
                    class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-amber-50 text-amber-500 rounded-[1.5rem] shadow-sm border border-amber-100">
                    <span class="text-4xl material-symbols-outlined">block</span>
                </div>
                <h3 class="mb-3 text-2xl font-black font-headline text-slate-800">تأكيد إلغاء الراكب</h3>
                <p class="mb-8 text-sm font-semibold leading-relaxed text-slate-500">
                    هل أنت متأكد من إلغاء الراكب:
                    <template x-if="statusPassengerData.passenger_name">
                        <span class="block font-black text-slate-800 text-base mt-1" x-text="statusPassengerData.passenger_name"></span>
                    </template>
                    <span class="inline-block font-mono text-base font-bold text-slate-800 dir-ltr mt-1"
                        x-text="statusPassengerData.passenger_number"></span>؟<br>
                    <span class="inline-block mt-2 text-amber-500/80">سيتم تغيير حالة الراكب إلى ملغي.</span>
                </p>

                <form :action="statusPassengerData.url" method="POST" @submit="isSubmitting = true" class="space-y-4 w-full">
                    @csrf
                    <input type="hidden" name="status" value="cancel">

                    {{-- معلومات الوسيط --}}
                    <template x-if="statusPassengerData.broker_name">
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-right">
                            <span class="text-xs font-bold text-slate-400">الوسيط المعتمد:</span>
                            <span class="text-xs font-black text-slate-700" x-text="statusPassengerData.broker_name"></span>
                        </div>
                    </template>

                    {{-- هاتف / واتساب الوسيط --}}
                    <div class="text-right">
                        <label class="block mb-1.5 text-xs font-bold text-slate-700">
                            واتساب الوسيط لإرسال الإلغاء <span class="text-slate-400 font-normal">(اختياري)</span>
                        </label>
                        <input type="text" name="broker_phone" x-model="statusPassengerData.broker_phone"
                            placeholder="مثال: 967770000000"
                            dir="ltr"
                            class="w-full p-3 text-xs font-bold text-slate-700 bg-slate-50 rounded-2xl border border-slate-200 outline-none focus:ring-2 focus:ring-amber-500/20">
                    </div>

                    <div class="text-right">
                        <label class="block mb-1.5 text-xs font-bold text-slate-700">
                            سبب الإلغاء <span class="text-slate-400 font-normal">(اختياري)</span>
                        </label>
                        <textarea name="cancel_reason" x-model="statusPassengerData.cancel_reason" rows="3"
                            placeholder="اكتب سبب إلغاء الحجز هنا..."
                            class="w-full p-3 text-xs font-bold text-slate-700 bg-slate-50 rounded-2xl border border-slate-200 outline-none focus:ring-2 focus:ring-amber-500/20"></textarea>
                    </div>

                    {{-- إرسال الإلغاء عبر واتساب --}}
                    <div class="flex items-center gap-2 pt-1 text-right">
                        <input type="checkbox" name="send_whatsapp" id="send_whatsapp_mobile_index" value="1"
                            x-model="statusPassengerData.send_whatsapp"
                            class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                        <label for="send_whatsapp_mobile_index" class="text-xs font-bold text-slate-700 cursor-pointer">
                            إرسال إشعار الإلغاء للوسيط عبر واتساب تلقائياً
                        </label>
                    </div>

                    <div class="flex gap-3 w-full">
                        <button type="button" @click="closeModals()"
                            class="flex-1 h-14 text-sm font-bold text-slate-600 rounded-[1rem] transition-all bg-slate-50 hover:bg-slate-100 active:scale-95 font-headline">تراجع</button>
                        <button type="submit" :disabled="isSubmitting"
                            class="flex flex-1 gap-2 justify-center items-center h-14 text-sm font-bold text-white rounded-[1rem] shadow-lg transition-all bg-amber-500 hover:bg-amber-600 shadow-amber-500/30 active:scale-95 font-headline disabled:opacity-70 disabled:cursor-not-allowed">
                            <span x-show="!isSubmitting">نعم، إلغاء الراكب</span>
                            <span x-show="isSubmitting"
                                class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= Rebook Modal (Bottom Sheet) ================= --}}
        <div x-show="showRebookModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60" @click="closeModals()"></div>

            <div
                class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-8 text-center pointer-events-auto">
                <div @click="closeModals()"
                    class="mx-auto mb-6 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
                </div>

                <div
                    class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-emerald-50 text-emerald-600 rounded-[1.5rem] shadow-sm border border-emerald-100">
                    <span class="text-4xl material-symbols-outlined">history</span>
                </div>
                <h3 class="mb-3 text-2xl font-black font-headline text-slate-800">تأكيد إعادة الحجز</h3>
                <p class="mb-6 text-sm font-semibold leading-relaxed text-slate-500">
                    هل أنت متأكد من إعادة حجز الراكب:
                    <template x-if="rebookPassengerData.passenger_name">
                        <span class="block font-black text-slate-800 text-base mt-1" x-text="rebookPassengerData.passenger_name"></span>
                    </template>
                    <span class="inline-block font-mono text-base font-bold text-slate-800 dir-ltr mt-1"
                        x-text="rebookPassengerData.passenger_number"></span>؟<br>
                    <span class="inline-block mt-2 text-emerald-600 font-bold">سيتم تغيير حالة الراكب إلى قيد الانتظار وإلغاء الإلغاء.</span>
                </p>

                <form :action="rebookPassengerData.url" method="POST" @submit="isSubmitting = true" class="space-y-4 w-full">
                    @csrf
                    <input type="hidden" name="status" value="pending">
                    <input type="hidden" name="is_rebook" value="1">

                    {{-- معلومات الوسيط --}}
                    <template x-if="rebookPassengerData.broker_name">
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-right">
                            <span class="text-xs font-bold text-slate-400">الوسيط المعتمد:</span>
                            <span class="text-xs font-black text-slate-700" x-text="rebookPassengerData.broker_name"></span>
                        </div>
                    </template>

                    {{-- هاتف / واتساب الوسيط --}}
                    <div class="text-right">
                        <label class="block mb-1.5 text-xs font-bold text-slate-700">
                            واتساب الوسيط لإرسال إشعار الحجز <span class="text-slate-400 font-normal">(اختياري)</span>
                        </label>
                        <input type="text" name="broker_phone" x-model="rebookPassengerData.broker_phone"
                            placeholder="مثال: 967770000000"
                            dir="ltr"
                            class="w-full p-3 text-xs font-bold text-slate-700 bg-slate-50 rounded-2xl border border-slate-200 outline-none focus:ring-2 focus:ring-emerald-500/20 text-right">
                    </div>

                    <div class="text-right">
                        <label class="block mb-1.5 text-xs font-bold text-slate-700">
                            ملاحظات <span class="text-slate-400 font-normal">(اختياري)</span>
                        </label>
                        <textarea name="note" x-model="rebookPassengerData.note" rows="2"
                            placeholder="أي ملاحظات حول إعادة الحجز..."
                            class="w-full p-3 text-xs font-bold text-slate-700 bg-slate-50 rounded-2xl border border-slate-200 outline-none focus:ring-2 focus:ring-emerald-500/20"></textarea>
                    </div>

                    {{-- إرسال الإشعار عبر واتساب --}}
                    <div class="flex items-center gap-2 pt-1 text-right">
                        <input type="checkbox" name="send_whatsapp" id="send_whatsapp_mobile_rebook" value="1"
                            x-model="rebookPassengerData.send_whatsapp"
                            class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                        <label for="send_whatsapp_mobile_rebook" class="text-xs font-bold text-slate-700 cursor-pointer">
                            إرسال إشعار تأكيد الحجز للوسيط عبر واتساب تلقائياً 💬
                        </label>
                    </div>

                    <div class="flex gap-3 w-full">
                        <button type="button" @click="closeModals()"
                            class="flex-1 h-14 text-sm font-bold text-slate-600 rounded-[1rem] transition-all bg-slate-50 hover:bg-slate-100 active:scale-95 font-headline">تراجع</button>
                        <button type="submit" :disabled="isSubmitting"
                            class="flex flex-1 gap-2 justify-center items-center h-14 text-sm font-bold text-white rounded-[1rem] shadow-lg transition-all bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/30 active:scale-95 font-headline disabled:opacity-70 disabled:cursor-not-allowed">
                            <span x-show="!isSubmitting">نعم، تأكيد إعادة الحجز</span>
                            <span x-show="isSubmitting"
                                class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= Delete Modal (Bottom Sheet) ================= --}}
        <div x-show="showDeleteModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60" @click="closeModals()"></div>

            <div
                class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-8 text-center pointer-events-auto">
                <div @click="closeModals()"
                    class="mx-auto mb-6 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
                </div>

                <div
                    class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-rose-50 text-rose-500 rounded-[1.5rem] shadow-sm">
                    <span class="text-4xl material-symbols-outlined">delete_forever</span>
                </div>
                <h3 class="mb-3 text-2xl font-black font-headline text-slate-800">تأكيد الحذف</h3>
                <p class="mb-8 text-sm font-semibold leading-relaxed text-slate-500">
                    هل أنت متأكد من حذف الراكب:
                    <template x-if="deletePassengerData.passenger_name">
                        <span class="block font-black text-slate-800 text-base mt-1" x-text="deletePassengerData.passenger_name"></span>
                    </template>
                    <span class="inline-block font-mono text-base font-bold text-slate-800 dir-ltr mt-1"
                        x-text="deletePassengerData.passenger_number"></span>؟<br>
                    <span class="inline-block mt-2 text-rose-500/80">لا يمكن التراجع عن هذا الإجراء.</span>
                </p>

                <form :action="deletePassengerData.url" method="POST" class="flex gap-3 w-full">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="closeModals()"
                        class="flex-1 h-14 text-sm font-bold text-slate-600 rounded-[1rem] transition-all bg-slate-50 hover:bg-slate-100 active:scale-95 font-headline">تراجع</button>
                    <button type="submit"
                        class="flex-1 h-14 text-sm font-bold text-white rounded-[1rem] shadow-lg transition-all bg-rose-500 hover:bg-rose-600 shadow-rose-500/30 active:scale-95 font-headline">نعم،
                        احذف</button>
                </form>
            </div>
        </div>
    </div>
    </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {

            Alpine.data('passengerMobileRegistry', () => ({
                searchQuery: '',
                selectedStatus: '',
                showCreateModal: false,
                showEditModal: false,
                showDeleteModal: false,
                showStatusModal: false,
                showRebookModal: false,
                isSubmitting: false,
                deletePassengerData: {
                    id: null,
                    passenger_name: '',
                    passenger_number: '',
                    url: ''
                },
                statusPassengerData: {
                    id: '',
                    passenger_name: '',
                    status: 'pending',
                    url: '',
                    date: '',
                    passenger_number: '',
                    location: '',
                    count: 1,
                    total_commission: 0,
                    broker_name: '',
                    broker_phone: '',
                    send_whatsapp: true,
                    customer_phone: '',
                    customer_name: '',
                    driver_phone: '',
                    driver_name: '',
                    note: ''
                },
                rebookPassengerData: {
                    id: '',
                    passenger_name: '',
                    status: 'pending',
                    url: '',
                    date: '',
                    passenger_number: '',
                    location: '',
                    count: 1,
                    total_commission: 0,
                    broker_name: '',
                    broker_phone: '',
                    send_whatsapp: true,
                    note: ''
                },
                editPassengerData: {
                    id: null,
                    date: '',
                    status: '',
                    passenger_number: '',
                    customer_id: null,
                    customer_name: '',
                    customer_phone: '',
                    location: '',
                    count: null,
                    total_commission: null,
                    driver_id: null,
                    driver_name: '',
                    driver_phone: '',
                    cancel_reason: '',
                    note: '',
                    url: ''
                },
                getPrintUrl() {
                    let parts = [];
                    @if(request('date'))
                        const d = '{{ request('date') === 'today' ? now()->format('Y-m-d') : request('date') }}';
                        parts.push('from:' + d);
                        parts.push('to:' + d);
                    @endif
                    if (this.selectedStatus) parts.push('status:' + this.selectedStatus);
                    parts.push('branch_id:{{ auth()->user()->branch_id }}');
                    return parts.join('|');
                },
                matchSearch(number, location, cName, cPhone, dName, dPhone, statusKey, passengerName = '') {
                    if (this.selectedStatus !== '' && statusKey !== this.selectedStatus) {
                        return false;
                    }

                    if (this.searchQuery.trim() === '') return true;

                    const query = this.searchQuery.toLowerCase().trim();
                    const cleanQuery = query.replace(/^(\+967|967|00967|0)/, '');

                    const check = (str) => {
                        if (!str) return false;
                        const cleanStr = String(str).toLowerCase();
                        return cleanStr.includes(query) || (cleanQuery !== '' && cleanStr.includes(
                            cleanQuery));
                    };

                    const statusMap = {
                        'pending': 'قيد الانتظار',
                        'completed': 'مكتمل',
                        'cancel': 'ملغي'
                    };
                    const arabicStatus = statusMap[statusKey] || statusKey;

                    return check(number) || check(location) || check(cName) || check(cPhone) || check(
                        dName) || check(dPhone) || check(arabicStatus) || check(passengerName);
                },
                openEditModal(data) {
                    this.editPassengerData = {
                        ...data,
                        url: '{{ url('passengers') }}/' + data.id
                    };
                    this.showEditModal = true;
                },
                openDeleteModal(id, passengerNumber, passengerName = '') {
                    this.deletePassengerData = {
                        id: id,
                        passenger_name: passengerName || '',
                        passenger_number: passengerNumber,
                        url: '{{ url('passengers') }}/' + id
                    };
                    this.showDeleteModal = true;
                },
                openStatusModal(passenger) {
                    this.statusPassengerData = {
                        ...passenger,
                        passenger_name: passenger.passenger_name || '',
                        broker_name: passenger.broker_name || 'غير محدد',
                        broker_phone: passenger.broker_phone || '',
                        send_whatsapp: true,
                        cancel_reason: passenger.cancel_reason || '',
                        note: passenger.note || '',
                        url: '{{ url('passengers') }}/' + passenger.id + '/status'
                    };
                    this.showStatusModal = true;
                },
                openRebookModal(passenger) {
                    this.rebookPassengerData = {
                        ...passenger,
                        passenger_name: passenger.passenger_name || '',
                        broker_name: passenger.broker_name || 'غير محدد',
                        broker_phone: passenger.broker_phone || '',
                        send_whatsapp: true,
                        note: passenger.note || '',
                        url: '{{ url('passengers') }}/' + passenger.id + '/status'
                    };
                    this.showRebookModal = true;
                },
                closeModals() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.showDeleteModal = false;
                    this.showStatusModal = false;
                    this.showRebookModal = false;
                }
            }));

            Alpine.data('recordSelect', ({
                records,
                countries,
                initialId = null,
                initialName = '',
                initialPhone = ''
            }) => ({
                records: records || [],
                countries: countries || [],
                openDropdown: false,
                selectedCountry: null,
                phone: '',
                fullPhone: '',
                nameInput: initialName || '',
                selectedId: initialId || '',
                isExisting: initialId ? true : false,
                filteredRecords: [],
                showDropdown: false,

                init() {
                    const yemen = this.countries.find(c => c.dial_code === '+967');
                    this.selectedCountry = yemen || (this.countries.length > 0 ? this.countries[0] : {
                        dial_code: '+967'
                    });

                    if (initialPhone) {
                        let phoneStr = String(initialPhone);
                        if (!phoneStr.startsWith('+')) {
                            phoneStr = '+' + phoneStr;
                        }

                        let matchedCountry = [...this.countries].sort((a, b) => b.dial_code.length - a
                            .dial_code.length)
                            .find(c => phoneStr.startsWith(c.dial_code));

                        if (matchedCountry) {
                            this.selectedCountry = matchedCountry;
                            this.phone = phoneStr.substring(matchedCountry.dial_code.length);
                        } else {
                            this.phone = initialPhone;
                        }
                        this.updateFullPhone();
                    }
                },

                selectCountry(country) {
                    this.selectedCountry = country;
                    this.openDropdown = false;
                    this.updateFullPhone();
                    this.searchRecord();
                },

                handlePhoneInput() {
                    this.phone = this.phone.replace(/[^0-9]/g, '');
                    this.updateFullPhone();
                    this.searchRecord();
                },

                updateFullPhone() {
                    this.fullPhone = this.phone ? (this.selectedCountry.dial_code + this.phone) : '';
                },

                searchRecord() {
                    this.selectedId = '';
                    this.isExisting = false;

                    if (this.phone.trim() === '') {
                        this.filteredRecords = [];
                        this.showDropdown = false;
                        // تم التعديل هنا: يتم مسح الاسم فقط إذا لم يكن هناك ID مسبق والمسح تم من قبل المستخدم
                        if (!initialId) this.nameInput = '';
                        return;
                    }

                    let query = this.fullPhone.replace(/[^\d]/g, '');
                    let cleanQuery = this.phone.replace(/[^\d]/g, '');

                    this.filteredRecords = this.records.filter(r => {
                        if (!r.phone) return false;
                        let p = String(r.phone).replace(/[^\d]/g, '');
                        return p.includes(query) || p.includes(cleanQuery);
                    });

                    this.showDropdown = true;

                    // تم التعديل هنا: السماح بالتحديد التلقائي إذا تطابق الرقم دون انتظار إغلاق القائمة
                    const exactMatch = this.records.find(r => {
                        let p = String(r.phone).replace(/[^\d]/g, '');
                        return p === query || p === cleanQuery;
                    });

                    if (exactMatch) {
                        this.selectRecord(exactMatch);
                    }
                },

                selectRecord(record) {
                    this.selectedId = record.id;
                    this.nameInput = record.name;
                    this.isExisting = true;
                    this.showDropdown = false;

                    let dialCode = this.selectedCountry ? String(this.selectedCountry.dial_code)
                        .replace('+', '') : '';
                    let searchDialCode = String(this.selectedCountry.dial_code);

                    if (record.phone && String(record.phone).startsWith(searchDialCode)) {
                        this.phone = String(record.phone).substring(searchDialCode.length);
                    } else if (record.phone && String(record.phone).startsWith(dialCode)) {
                        this.phone = String(record.phone).substring(dialCode.length);
                    } else {
                        this.phone = record.phone;
                    }
                    this.updateFullPhone();
                },

                resetSelection() {
                    this.selectedId = '';
                    this.phone = '';
                    this.nameInput = '';
                    this.isExisting = false;
                    this.showDropdown = false;
                    this.updateFullPhone();
                }
            }));

            Alpine.data('phonePickerOnly', ({
                countries,
                initialPhone = ''
            }) => ({
                countries: countries || [],
                openDropdown: false,
                selectedCountry: null,
                phone: '',
                fullPhone: '',

                init() {
                    const yemen = this.countries.find(c => c.dial_code === '+967');
                    this.selectedCountry = yemen || (this.countries.length > 0 ? this.countries[0] : {
                        dial_code: '+967'
                    });

                    if (initialPhone) {
                        let phoneStr = String(initialPhone);
                        if (!phoneStr.startsWith('+')) {
                            phoneStr = '+' + phoneStr;
                        }

                        let matchedCountry = [...this.countries].sort((a, b) => b.dial_code.length - a
                            .dial_code.length)
                            .find(c => phoneStr.startsWith(c.dial_code));

                        if (matchedCountry) {
                            this.selectedCountry = matchedCountry;
                            this.phone = phoneStr.substring(matchedCountry.dial_code.length);
                        } else {
                            this.phone = initialPhone;
                        }
                        this.updateFullPhone();
                    }
                },

                selectCountry(country) {
                    this.selectedCountry = country;
                    this.openDropdown = false;
                    this.updateFullPhone();
                },

                handlePhoneInput() {
                    this.phone = this.phone.replace(/[^0-9]/g, '');
                    this.updateFullPhone();
                },

                updateFullPhone() {
                    if (!this.phone) {
                        this.fullPhone = '';
                        return;
                    }
                    const dial = String(this.selectedCountry?.dial_code || '').replace('+', '');
                    let cleanPhone = String(this.phone).replace(/[^0-9]/g, '').replace(/^0+/, '');
                    if (dial && cleanPhone.startsWith(dial)) {
                        cleanPhone = cleanPhone.substring(dial.length).replace(/^0+/, '');
                    }
                    this.fullPhone = dial ? (dial + cleanPhone) : cleanPhone;
                }
            }));
        });

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