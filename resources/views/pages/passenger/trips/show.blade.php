@extends('layouts.app')

@section('title', 'تفاصيل الرحلة #' . $trip->id)
@section('Breadcrumb', 'تفاصيل الرحلة')

@section('content')

    <div class="pb-24 space-y-6 min-h-screen font-body lg:pb-12" dir="rtl" x-data="tripDetailsForm(@js($trip->passengers->map(function ($p) {
        return [
            'id' => $p->id,
            'passenger_number' => $p->passenger_number,
            'count' => $p->count ?? 1,
            'destination' => $p->destination ?? ' '
        ];
    }) ?? []))">

        {{-- Header --}}
        <div class="mx-auto w-full max-w-7xl">
            <div class="flex justify-between items-center mb-6">
                <div class="flex gap-4 items-center">
                    <a href="{{ route('trips.index') }}"
                        class="flex justify-center items-center w-12 h-12 bg-white rounded-2xl border shadow-sm transition-all dark:bg-boxdark border-slate-100 dark:border-boxdark-2 text-slate-500 dark:text-gray-400 hover:text-primary hover:border-primary/30 active:scale-95">
                        <span class="material-symbols-outlined text-[24px]">arrow_forward</span>
                    </a>
                    <div>
                        <h1
                            class="flex gap-2 items-center text-2xl font-black font-headline text-slate-800 dark:text-white">
                            تفاصيل الرحلة
                            <span
                                class="px-2 py-0.5 font-mono text-lg rounded-lg text-primary bg-primary/10">#{{ $trip->id }}</span>
                        </h1>
                        <p class="mt-1 text-sm font-bold text-gray-500 dark:text-gray-400">تاريخ الإنشاء:
                            {{ $trip->created_at->format('Y-m-d h:i A') }}
                        </p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex gap-3">
                    {{-- زر الإضافة المباشرة --}}
                    <button @click="addModalOpen = true" type="button"
                        class="flex gap-2 items-center px-5 h-12 text-sm font-bold text-white rounded-2xl shadow-sm transition-all bg-primary hover:bg-primary/90 active:scale-95">
                        <span class="material-symbols-outlined text-[20px]">person_add</span>
                        إضافة راكب
                    </button>

                    <a href="{{ route('receipt.generate', ['type' => 'trip', 'id' => $trip->uuid]) }}" target="_blank"
                        class="flex gap-2 items-center px-5 h-12 text-sm font-bold text-emerald-600 bg-white rounded-2xl border border-emerald-100 shadow-sm transition-all dark:bg-boxdark dark:text-emerald-400 dark:border-emerald-500/20 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 active:scale-95">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                        طباعة الكشف
                    </a>
                    <a href="{{ route('trips.edit', $trip->id) }}"
                        class="flex gap-2 items-center px-5 h-12 text-sm font-bold text-amber-600 bg-white rounded-2xl border border-amber-100 shadow-sm transition-all dark:bg-boxdark dark:text-amber-400 dark:border-amber-500/20 hover:bg-amber-50 dark:hover:bg-amber-500/10 active:scale-95">
                        <span class="material-symbols-outlined text-[20px]">edit</span>
                        تعديل الرحلة
                    </a>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="mx-auto w-full max-w-7xl">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- Right Column (Driver & Stats) --}}
                <div class="space-y-6 lg:col-span-1">

                    {{-- Driver Card --}}
                    <div
                        class="bg-white dark:bg-boxdark rounded-[2rem] p-6 border border-gray-100 dark:border-boxdark-2 shadow-sm">
                        <h3
                            class="flex gap-2 items-center mb-4 text-sm font-black text-gray-500 dark:text-gray-400 font-headline">
                            <span class="material-symbols-outlined text-[20px]">badge</span>
                            السائق المسؤول
                        </h3>

                        <div class="flex gap-4 items-center">
                            <div
                                class="flex justify-center items-center w-16 h-16 text-emerald-600 bg-emerald-50 rounded-2xl border border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400 shrink-0">
                                <span class="material-symbols-outlined text-[32px]">local_taxi</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-black truncate text-slate-800 dark:text-white">
                                    {{ $trip->driver->name ?? 'سائق غير معين' }}
                                </h3>
                                @if($trip->driver?->phone)
                                    <a href="tel:{{ $trip->driver->phone }}" class="inline-block mt-1.5"
                                        style="direction: ltr;">
                                        <div
                                            class="px-2.5 py-1 rounded-xl border transition-colors bg-primary/5 border-primary/10 hover:bg-primary/10">
                                            <x-phone-number :value="$trip->driver->phone"
                                                class="text-sm text-primary !justify-start" />
                                        </div>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Stats Cards --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div
                            class="p-5 bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm flex flex-col justify-center items-center gap-2 text-center">
                            <div class="flex justify-center items-center w-12 h-12 rounded-xl bg-primary/10 text-primary">
                                <span class="material-symbols-outlined text-[24px]">group_work</span>
                            </div>
                            <div>
                                <span class="block mb-0.5 text-xs font-bold text-gray-400">مجموعات الركاب</span>
                                <span
                                    class="text-2xl font-black text-slate-800 dark:text-white">{{ $trip->passengers->count() }}</span>
                            </div>
                        </div>

                        <div
                            class="p-5 bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm flex flex-col justify-center items-center gap-2 text-center">
                            <div
                                class="flex justify-center items-center w-12 h-12 text-amber-500 bg-amber-50 rounded-xl dark:bg-amber-500/10">
                                <span class="material-symbols-outlined text-[24px]">person</span>
                            </div>
                            <div>
                                <span class="block mb-0.5 text-xs font-bold text-gray-400">إجمالي الأفراد</span>
                                <span
                                    class="text-2xl font-black text-slate-800 dark:text-white">{{ $trip->passengers->sum('count') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Left Column (Passengers List) --}}
                <div class="lg:col-span-2">
                    <div
                        class="bg-white dark:bg-boxdark rounded-[2rem] p-6 border border-gray-100 dark:border-boxdark-2 shadow-sm h-full flex flex-col">
                        <div class="flex flex-col gap-4 justify-between items-start mb-6 md:flex-row md:items-center">
                            <div>
                                <h3
                                    class="flex gap-2 items-center text-lg font-black text-slate-800 dark:text-white font-headline">
                                    <span class="material-symbols-outlined text-primary">group</span>
                                    الركاب المقيدين في الرحلة
                                </h3>
                                <p class="mt-1 text-xs font-bold text-gray-500">قائمة الركاب الذين تم تسييرهم في هذه الرحلة.</p>
                            </div>

                            <div class="relative w-full md:w-64 shrink-0">
                                <span
                                    class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-[20px]">search</span>
                                <input type="text" x-model="searchQuery" placeholder="بحث عن راكب..."
                                    class="pr-11 pl-4 w-full h-12 text-sm rounded-xl border transition-all outline-none bg-slate-50 dark:bg-boxdark-2 text-slate-800 dark:text-white border-slate-200 dark:border-boxdark-2 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>
                        </div>

                        <div class="grid overflow-y-auto flex-1 grid-cols-1 auto-rows-max gap-4 content-start pr-2 md:grid-cols-2 custom-scrollbar"
                            style="max-height: 500px;">
                            @forelse($trip->passengers as $passenger)
                                <div class="flex gap-4 justify-between items-center p-4 rounded-2xl border transition-colors bg-slate-50/50 dark:bg-boxdark-2/50 border-slate-100 dark:border-boxdark-2 hover:border-primary/30"
                                    x-show="searchQuery === '' || String(@js($passenger->passenger_number)).includes(searchQuery)">

                                    <div class="flex flex-1 gap-4 items-center min-w-0">
                                        <div
                                            class="flex flex-col justify-center items-center w-12 h-12 bg-white rounded-xl border shadow-sm dark:bg-boxdark text-slate-600 dark:text-gray-300 shrink-0 border-slate-200 dark:border-boxdark-2">
                                            <span class="text-lg font-black leading-none">{{ $passenger->count ?? 1 }}</span>
                                            <span class="text-[9px] font-bold mt-0.5">ركاب</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span
                                                class="block mb-1 text-base font-black tracking-tight truncate font-headline text-slate-800 dark:text-white"
                                                style="direction: ltr; text-align: right;">
                                                <x-phone-number :value="$passenger->passenger_number"
                                                    class="text-base text-slate-800 dark:text-white !justify-start" />
                                            </span>
                                            <div class="flex gap-1.5 items-center">
                                                <span class="material-symbols-outlined text-[14px] text-primary">flag</span>
                                                <span
                                                    class="text-xs font-bold truncate text-slate-500 dark:text-gray-400">{{ $passenger->destination ?? ' ' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex gap-2 items-center shrink-0">
                                        {{-- زر فك الارتباط (موجود مسبقاً) --}}
                                     <button type="button" @click="
                                        deleteShipmentData = { 
                                            bond_number: '{{ $passenger->passenger_number }}', 
                                            url: '{{ route('trip.removePassenger', ['trip' => $trip->id, 'passenger' => $passenger->id]) }}' 
                                        }; 
                                        showDeleteModal = true;
                                    "
                                    class="flex justify-center items-center w-8 h-8 text-gray-400 rounded-full border shadow-sm transition-all bg-slate-50 border-slate-100 dark:bg-boxdark-2 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 dark:hover:border-rose-500/20"
                                    title="فك ارتباط">
                                    <span class="material-symbols-outlined text-[16px]">link_off</span>
                                </button>
                                    </div>

                                </div>
                            @empty
                                <div
                                    class="flex flex-col col-span-1 justify-center items-center py-12 text-center rounded-2xl border border-dashed md:col-span-2 text-slate-400 bg-slate-50 dark:bg-boxdark-2/30 border-slate-200 dark:border-boxdark-2">
                                    <div
                                        class="flex justify-center items-center mb-3 w-16 h-16 bg-white rounded-full shadow-sm dark:bg-boxdark">
                                        <span class="material-symbols-outlined text-[32px] opacity-30">group_off</span>
                                    </div>
                                    <p class="text-sm font-bold text-slate-500 dark:text-gray-400">لا يوجد ركاب مضافين لهذه
                                        الرحلة حتى الآن</p>
                                </div>
                            @endforelse

                            @if($trip->passengers->isNotEmpty())
                                <div x-show="countVisible === 0" x-cloak
                                    class="flex flex-col col-span-1 justify-center items-center py-12 text-center rounded-2xl border border-dashed md:col-span-2 text-slate-400 bg-slate-50 dark:bg-boxdark-2/30 border-slate-200 dark:border-boxdark-2">
                                    <div
                                        class="flex justify-center items-center mb-3 w-16 h-16 bg-white rounded-full shadow-sm dark:bg-boxdark">
                                        <span class="material-symbols-outlined text-[32px] opacity-30">person_search</span>
                                    </div>
                                    <p class="text-sm font-bold text-slate-500 dark:text-gray-400">لم يتم العثور على ركاب
                                        يطابقون بحثك</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- نافذة منبثقة (Modal) لإضافة راكب جديد للرحلة باستخدام تصميم الزاجل --}}
        <div x-show="addModalOpen" style="display: none"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100" 
            x-transition:leave="transition ease-in duration-200" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0">
            
            <div @click.away="addModalOpen = false"
                class="bg-white dark:bg-boxdark rounded-[2rem] w-full max-w-lg shadow-xl overflow-hidden border border-slate-100 dark:border-boxdark-2 transform transition-all"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <div class="flex justify-between items-center px-6 py-5 border-b border-slate-100 dark:border-boxdark-2 bg-slate-50/50 dark:bg-boxdark-2/50">
                    <h3 class="flex gap-2 items-center text-lg font-black text-slate-800 dark:text-white font-headline">
                        <span class="material-symbols-outlined text-primary">person_add</span>
                        إضافة راكب من قائمة الانتظار 
                    </h3>
                    <button @click="addModalOpen = false" class="transition-colors text-slate-400 hover:text-slate-600 dark:hover:text-gray-300">
                        <span class="material-symbols-outlined text-[24px]">close</span>
                    </button>
                </div>

                <form action="{{ route('trip.addPassenger', $trip->id) }}" method="POST" class="p-6">
                    @csrf
                    <div class="mb-6">
                        <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-gray-300">اختر الراكب لإضافته</label>
                        <select name="passenger_id" required
                            class="px-4 w-full h-12 text-sm rounded-xl border transition-all appearance-none outline-none bg-slate-50 dark:bg-boxdark-2 text-slate-800 dark:text-white border-slate-200 dark:border-boxdark-2 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="">-- اختر راكب من قائمة الانتظار --</option>
                            @forelse($pendingPassengers ?? [] as $pending)
                                <option value="{{ $pending->id }}">
                                    {{ $pending->passenger_number }} - (العدد: {{ $pending->count ?? 1 }}) - {{ $pending->destination }}
                                </option>
                            @empty
                                <option value="" disabled>لا يوجد ركاب في قائمة الانتظار حالياً</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="flex gap-3 justify-end items-center mt-8">
                        <button type="button" @click="addModalOpen = false"
                            class="px-5 h-11 text-sm font-bold rounded-xl transition-colors bg-slate-100 dark:bg-boxdark-2 text-slate-600 dark:text-gray-300 hover:bg-slate-200 dark:hover:bg-slate-700">
                            إلغاء
                        </button>
                        <button type="submit"
                            class="flex gap-2 items-center px-5 h-11 text-sm font-bold text-white rounded-xl shadow-sm transition-all bg-primary hover:bg-primary/90 active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">add_circle</span>
                            إضافة للرحلة
                        </button>
                    </div>
                </form>
            </div>
        </div>
<div x-show="showDeleteModal" style="display: none;"
    class="fixed inset-0 z-[9999] flex items-center justify-center px-4 bg-slate-900/40 backdrop-blur-sm"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">

    <div @click.away="showDeleteModal = false"
        class="p-8 w-full max-w-md text-center bg-white rounded-[2rem] border shadow-2xl transition-all transform dark:bg-boxdark border-slate-100 dark:border-boxdark-2"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95">

        {{-- أيقونة تحذيرية --}}
        <div class="flex justify-center items-center mx-auto mb-5 w-16 h-16 text-rose-500 bg-rose-50 rounded-2xl border border-rose-100 dark:bg-rose-500/10 dark:border-rose-500/20">
            <span class="material-symbols-outlined text-[32px]">link_off</span>
        </div>

        <h3 class="mb-2 text-lg font-black font-headline text-slate-800 dark:text-white">تأكيد فك ارتباط الراكب</h3>

        <p class="mb-8 text-sm font-medium leading-relaxed text-slate-500 dark:text-gray-400">
            هل أنت متأكد من فك ارتباط الراكب ذو الرقم 
            <span class="inline-block px-2 py-0.5 mt-1 font-black rounded-md text-slate-700 bg-slate-100 dark:bg-boxdark-2 dark:text-slate-200" x-text="deleteShipmentData.bond_number" style="direction: ltr;"></span>
            <br>وإعادته إلى قائمة الانتظار؟
        </p>

        {{-- أزرار التحكم --}}
        <div class="flex gap-3 items-center">
            <button type="button" @click="showDeleteModal = false"
                class="flex-1 px-4 h-12 text-sm font-bold rounded-xl transition-all text-slate-600 bg-slate-100 hover:bg-slate-200 dark:bg-boxdark-2 dark:text-gray-300 dark:hover:bg-slate-700 active:scale-95">
                تراجع
            </button>

            {{-- الفورم الديناميكي الذي يستقبل الرابط من الزر --}}
            <form :action="deleteShipmentData.url" method="POST" class="flex-1">
                @csrf
                <button type="submit"
                    class="flex gap-2 justify-center items-center w-full h-12 text-sm font-bold text-white bg-rose-500 rounded-xl shadow-sm transition-all hover:bg-rose-600 shadow-rose-500/20 active:scale-95">
                    <span class="material-symbols-outlined text-[20px]">link_off</span>
                    تأكيد الفك
                </button>
            </form>
        </div>
    </div>
</div>
    </div>

@endsection
@section('script')
    <script>
        function tripDetailsForm() {
            return {
                searchQuery: '',
                addModalOpen: false, // التحكم في إظهار وإخفاء نافذة الإضافة
                showDeleteModal: false, // التحكم في نافذة فك الارتباط (الحذف)
                deleteShipmentData: { 
                    bond_number: '', 
                    url: '' 
                }, // تخزين بيانات الراكب المراد فك ارتباطه
                
                get countVisible() {
                    if (this.searchQuery === '') return {{ $trip->passengers->count() }};
                    let count = 0;
                    @foreach($trip->passengers as $passenger)
                        if (String(@js($passenger->passenger_number)).includes(this.searchQuery)) count++;
                    @endforeach
                    return count;
                }
            }
        }
    </script>

@endsection