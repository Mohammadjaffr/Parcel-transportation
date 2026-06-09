@extends('mobile.layouts.app')

@section('title', 'تفاصيل الرحلة')

@section('content')

    <div class="flex flex-col pt-4 pb-24 min-h-screen bg-slate-50/50 dark:bg-black" dir="rtl" 
         x-data="tripDetailsForm(@js($trip->passengers->map(function ($p) {
            return [
                'id' => $p->id,
                'passenger_number' => $p->passenger_number,
                'count' => $p->count ?? 1,
                'destination' => $p->destination ?? ' '
            ];
         })->toArray() ?? []))">

        {{-- ================= الهيدر وزر الرجوع ================= --}}
        <div class="flex justify-between items-center px-4 mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('trips.index') }}"
                    class="flex justify-center items-center w-10 h-10 bg-white dark:bg-boxdark rounded-full border shadow-sm transition-all border-slate-100 dark:border-boxdark-2 text-slate-500 dark:text-gray-400 hover:text-primary active:scale-90">
                    <span class="material-symbols-outlined text-[20px] mr-1">arrow_forward_ios</span>
                </a>
                <div>
                    <h1 class="text-lg font-black font-headline text-slate-800 dark:text-white">تفاصيل الرحلة</h1>
                    <p class="text-xs text-gray-400 mt-0.5">معاينة السائق والركاب المسافرين</p>
                </div>
            </div>

            {{-- زر سريع للطباعة أو Tعديل من داخل التفاصيل --}}
            <div class="flex gap-2">
                <a href="{{ route('receipt.generate', ['type' => 'trip', 'id' => $trip->uuid]) }}" target="_blank"
                    class="flex justify-center items-center w-10 h-10 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full border border-emerald-100 dark:border-emerald-500/20 shadow-sm transition-all active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">print</span>
                </a>
                <a href="{{ route('trips.edit', $trip->id) }}"
                    class="flex justify-center items-center w-10 h-10 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-full border border-amber-100 dark:border-amber-500/20 shadow-sm transition-all active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">edit</span>
                </a>
            </div>
        </div>

        <div class="px-4 space-y-5">

            {{-- ================= كرت السائق المسؤول بالكامل ================= --}}
            <div class="w-full bg-white dark:bg-boxdark rounded-[2rem] shadow-sm p-5 border border-gray-100 dark:border-boxdark-2 relative overflow-hidden">
                <div class="flex items-center gap-4 mt-2">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center border border-emerald-100 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400 shrink-0">
                        <span class="material-symbols-outlined text-[30px]">local_taxi</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-xs text-gray-400 font-bold block mb-0.5">السائق المسؤول</span>
                        <h3 class="text-base font-black text-slate-800 dark:text-white truncate">
                            {{ $trip->driver->name ?? 'سائق غير معين' }}
                        </h3>
                        @if($trip->driver?->phone)
                            <a href="tel:{{ $trip->driver->phone }}" class="inline-block mt-1.5" style="direction: ltr;">
                                <div class="bg-primary/5 px-2.5 py-1 rounded-xl border border-primary/10">
                                    <x-phone-number :value="$trip->driver->phone" class="text-xs text-primary !justify-start" />
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ================= الإحصائيات الحركية الرقمية ================= --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]">group_work</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold block">مجموعات الركاب</span>
                        <span class="text-base font-black text-slate-800 dark:text-white">{{ $trip->passengers->count() }}</span>
                    </div>
                </div>

                <div class="p-4 bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]">person</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold block">إجمالي الأفراد</span>
                        <span class="text-base font-black text-slate-800 dark:text-white">{{ $trip->passengers->sum('count') }}</span>
                    </div>
                </div>
            </div>

            {{-- ================= قسم قائمة ركاب الرحلة الحالية ================= --}}
            <div class="space-y-3 pt-2">
                <div class="flex justify-between items-center px-2">
                    <h3 class="font-black text-slate-800 dark:text-white font-headline text-sm flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px] text-slate-400">group</span>
                        ركاب الرحلة المقيدين بالداخل
                    </h3>
                    <span class="text-[11px] font-bold text-gray-400" x-text="'المعروض: ' + countVisible"></span>
                </div>

                {{-- شريط البحث الفوري داخل الركاب --}}
                <div class="relative px-1">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-[18px]">search</span>
                    <input type="text" x-model="searchQuery" placeholder="البحث برقم جوال الراكب المضاف..."
                        class="pr-11 pl-4 w-full h-12 text-sm bg-white dark:bg-boxdark text-slate-800 dark:text-white rounded-2xl border transition-all outline-none border-slate-200 dark:border-boxdark-2 focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-sm">
                </div>

                {{-- قائمة الركاب (Grid) للموبايل --}}
                <div class="grid grid-cols-1 gap-3 px-1 mt-2">
                    @forelse($trip->passengers as $passenger)
                        <div class="bg-white dark:bg-boxdark p-4 rounded-3xl border border-slate-100 dark:border-boxdark-2 shadow-[0_4px_20px_rgb(0,0,0,0.01)] flex items-center justify-between gap-4 transition-all"
                            x-show="searchQuery === '' || String(@js($passenger->passenger_number)).includes(searchQuery)">

                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                {{-- العداد المصغر --}}
                                <div class="flex flex-col justify-center items-center w-11 h-11 rounded-2xl bg-slate-50 dark:bg-boxdark-2 text-slate-600 dark:text-gray-300 shrink-0 border border-slate-100 dark:border-boxdark-2">
                                    <span class="text-sm font-black leading-none font-headline">{{ $passenger->count ?? 1 }}</span>
                                    <span class="text-[8px] font-bold mt-0.5">ركاب</span>
                                </div>

                                {{-- التفاصيل --}}
                                <div class="flex-1 min-w-0">
                                    <span class="block font-headline text-sm font-black text-slate-800 dark:text-white tracking-tight truncate mb-0.5" style="direction: ltr; text-align: right;">
                                        <x-phone-number :value="$passenger->passenger_number" class="text-sm text-slate-800 dark:text-white !justify-start" />
                                    </span>

                                    <div class="flex gap-1 items-center mt-1">
                                        <span class="material-symbols-outlined text-[13px] text-amber-500">location_on</span>
                                        <span class="text-xs font-bold text-slate-500 dark:text-gray-400 truncate">{{ $passenger->destination ?? ' ' }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- أزرار التحكم والحالة للموبايل --}}
                            <div class="flex items-center gap-2 shrink-0">

                                {{-- زر فك الارتباط (Alpine.js) --}}
                                <button type="button" @click="
                                        deleteShipmentData = { 
                                            bond_number: '{{ $passenger->passenger_number }}', 
                                            url: '{{ route('trip.removePassenger', ['trip' => $trip->id, 'passenger' => $passenger->id]) }}' 
                                        }; 
                                        showDeleteModal = true;
                                    "
                                    class="flex justify-center items-center w-8 h-8 text-gray-400 bg-slate-50 rounded-full border border-slate-100 shadow-sm transition-all dark:bg-boxdark-2 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 dark:hover:border-rose-500/20"
                                    title="فك ارتباط">
                                    <span class="material-symbols-outlined text-[16px]">link_off</span>
                                </button>

                                {{-- مؤشر مقيد الافتراضي --}}
                                <div class="flex justify-center items-center w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20">
                                    <span class="material-symbols-outlined text-[16px] font-bold">done_all</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-boxdark p-10 rounded-[2.5rem] border border-slate-100 dark:border-boxdark-2 shadow-sm flex flex-col items-center justify-center text-slate-400">
                            <div class="flex justify-center items-center mb-3 w-16 h-16 rounded-full bg-slate-50 dark:bg-boxdark-2">
                                <span class="material-symbols-outlined text-[32px] opacity-30">group_off</span>
                            </div>
                            <p class="text-xs font-bold text-slate-500 dark:text-gray-400 text-center">لا يوجد ركاب مضافين لهذه الرحلة حتى الآن</p>
                        </div>
                    @endforelse

                    {{-- رسالة لا توجد نتائج للبحث --}}
                    @if($trip->passengers->isNotEmpty())
                        <div x-show="countVisible === 0" x-cloak
                            class="bg-white dark:bg-boxdark p-10 rounded-[2.5rem] border border-slate-100 dark:border-boxdark-2 shadow-sm flex flex-col items-center justify-center text-slate-400">
                            <div class="flex justify-center items-center mb-3 w-16 h-16 rounded-full bg-slate-50 dark:bg-boxdark-2">
                                <span class="material-symbols-outlined text-[32px] opacity-30">search_off</span>
                            </div>
                            <p class="text-xs font-bold text-slate-500 dark:text-gray-400 text-center">لا توجد نتائج تطابق بحثك</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ================= نافذة تأكيد فك الارتباط للموبايل (Modal) ================= --}}
        <div x-show="showDeleteModal" x-transition
            class="fixed inset-0 z-[999] flex items-end justify-center bg-black/50 p-4 drop-shadow-2xl" x-cloak>

            <div @click.away="showDeleteModal = false"
                class="bg-white dark:bg-boxdark w-full max-w-md rounded-t-[2.5rem] rounded-b-none p-6 text-center animate-slide-up border border-slate-100 dark:border-boxdark-2 shadow-2xl">

                {{-- أيقونة تحذيرية --}}
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-500">
                    <span class="material-symbols-outlined text-[28px]">link_off</span>
                </div>

                <h3 class="text-base font-black font-headline text-slate-800 dark:text-white mb-2">تأكيد فك ارتباط الراكب</h3>

                <p class="text-xs font-medium text-slate-500 dark:text-gray-400 mb-6 leading-relaxed">
                    هل أنت متأكد من فك ارتباط الراكب ذو الرقم
                    <span class="font-black text-slate-700 dark:text-slate-200" x-text="deleteShipmentData.bond_number"></span>
                    وإعادته إلى قائمة الانتظار؟
                </p>

                {{-- أزرار التحكم --}}
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" @click="showDeleteModal = false"
                        class="w-full py-3 px-4 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 dark:bg-boxdark-2 dark:text-gray-400 rounded-2xl transition-all">
                        إلغاء
                    </button>

                    {{-- الفورم الديناميكي الذي يستقبل الرابط من الزر --}}
                    <form :action="deleteShipmentData.url" method="POST" class="w-full">
                        @csrf
                        <button type="submit"
                            class="w-full py-3 px-4 text-xs font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-2xl shadow-md shadow-rose-500/20 transition-all">
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
        function tripDetailsForm(passengersData) {
            return {
                passengers: passengersData || [],
                searchQuery: '',
                showDeleteModal: false,
                deleteShipmentData: { bond_number: '', url: '' },
                
                // حساب عدد العناصر المطابقة للبحث ديناميكياً بدون لود إضافي
                get countVisible() {
                    if (this.searchQuery === '') return this.passengers.length;
                    return this.passengers.filter(p => 
                        String(p.passenger_number).includes(this.searchQuery)
                    ).length;
                }
            }
        }
    </script>
@endsection