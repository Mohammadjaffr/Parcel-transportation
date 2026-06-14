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
            <div class="flex gap-3 items-center">
                <a href="{{ route('trips.index') }}"
                    class="flex justify-center items-center w-10 h-10 bg-white rounded-full border shadow-sm transition-all dark:bg-boxdark border-slate-100 dark:border-boxdark-2 text-slate-500 dark:text-gray-400 hover:text-primary active:scale-90">
                    <span class="material-symbols-outlined text-[20px] mr-1">arrow_forward_ios</span>
                </a>
                <div>
                    <h1 class="text-lg font-black font-headline text-slate-800 dark:text-white">تفاصيل الرحلة</h1>
                    <p class="mt-0.5 text-xs text-gray-400">معاينة السائق والركاب المسافرين</p>
                </div>
            </div>

            {{-- إجراءات سريعة --}}
            <div class="flex gap-2">
                <a href="{{ route('receipt.generate', ['type' => 'trip', 'id' => $trip->uuid]) }}" target="_blank"
                    class="flex justify-center items-center w-10 h-10 text-emerald-600 bg-emerald-50 rounded-full border border-emerald-100 shadow-sm transition-all dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">print</span>
                </a>
                <a href="{{ route('trips.edit', $trip->id) }}"
                    class="flex justify-center items-center w-10 h-10 text-amber-600 bg-amber-50 rounded-full border border-amber-100 shadow-sm transition-all dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20 active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">edit</span>
                </a>
                {{-- زر إضافة راكب --}}
                 <button @click="addModalOpen = true" type="button"
                        class="flex justify-center items-center w-10 h-10 text-white rounded-full shadow-sm transition-all bg-primary hover:bg-primary/90 active:scale-90">
                        <span class="material-symbols-outlined text-[20px]">person_add</span>
                 </button>
            </div>
        </div>

        <div class="px-4 space-y-5">

            {{-- ================= كرت السائق المسؤول بالكامل ================= --}}
            <div class="w-full bg-white dark:bg-boxdark rounded-[2rem] shadow-sm p-5 border border-gray-100 dark:border-boxdark-2 relative overflow-hidden">
                <div class="flex gap-4 items-center mt-2">
                    <div class="flex justify-center items-center w-14 h-14 text-emerald-600 bg-emerald-50 rounded-2xl border border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400 shrink-0">
                        <span class="material-symbols-outlined text-[30px]">local_taxi</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="block mb-0.5 text-xs font-bold text-gray-400">السائق المسؤول</span>
                        <h3 class="text-base font-black truncate text-slate-800 dark:text-white">
                            {{ $trip->driver->name ?? 'سائق غير معين' }}
                        </h3>
                        @if($trip->driver?->phone)
                            <a href="tel:{{ $trip->driver->phone }}" class="inline-block mt-1.5" style="direction: ltr;">
                                <div class="px-2.5 py-1 rounded-xl border bg-primary/5 border-primary/10">
                                    <x-phone-number :value="$trip->driver->phone" class="text-xs text-primary !justify-start" />
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ================= الإحصائيات ================= --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm flex items-center gap-3">
                    <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-primary/10 text-primary shrink-0">
                        <span class="material-symbols-outlined text-[20px]">group_work</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold block">مجموعات الركاب</span>
                        <span class="text-base font-black text-slate-800 dark:text-white">{{ $trip->passengers->count() }}</span>
                    </div>
                </div>

                <div class="p-4 bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm flex items-center gap-3">
                    <div class="flex justify-center items-center w-10 h-10 text-amber-500 rounded-xl bg-amber-500/10 shrink-0">
                        <span class="material-symbols-outlined text-[20px]">person</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold block">إجمالي الأفراد</span>
                        <span class="text-base font-black text-slate-800 dark:text-white">{{ $trip->passengers->sum('count') }}</span>
                    </div>
                </div>
            </div>

            {{-- ================= قسم قائمة الركاب ================= --}}
            <div class="pt-2 space-y-3">
                <div class="flex justify-between items-center px-2">
                    <h3 class="flex gap-1.5 items-center text-sm font-black text-slate-800 dark:text-white font-headline">
                        <span class="material-symbols-outlined text-[18px] text-slate-400">group</span>
                         الركاب المقيدين في الرحلة
                    </h3>
                    <span class="text-[11px] font-bold text-gray-400" x-text="'المعروض: ' + countVisible"></span>
                </div>

                {{-- شريط البحث --}}
                <div class="relative px-1">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-[18px]">search</span>
                    <input type="text" x-model="searchQuery" placeholder="البحث برقم جوال الراكب..."
                        class="pr-11 pl-4 w-full h-12 text-sm bg-white rounded-2xl border shadow-sm transition-all outline-none dark:bg-boxdark text-slate-800 dark:text-white border-slate-200 dark:border-boxdark-2 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div class="grid grid-cols-1 gap-3 px-1 mt-2">
                    @forelse($trip->passengers as $passenger)
                        <div class="bg-white dark:bg-boxdark p-4 rounded-3xl border border-slate-100 dark:border-boxdark-2 shadow-[0_4px_20px_rgb(0,0,0,0.01)] flex items-center justify-between gap-4 transition-all"
                            x-show="searchQuery === '' || String(@js($passenger->passenger_number)).includes(searchQuery)">

                            <div class="flex flex-1 gap-3 items-center min-w-0">
                                <div class="flex flex-col justify-center items-center w-11 h-11 rounded-2xl border bg-slate-50 dark:bg-boxdark-2 text-slate-600 dark:text-gray-300 shrink-0 border-slate-100 dark:border-boxdark-2">
                                    <span class="text-sm font-black leading-none font-headline">{{ $passenger->count ?? 1 }}</span>
                                    <span class="text-[8px] font-bold mt-0.5">ركاب</span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <span class="block mb-0.5 text-sm font-black tracking-tight truncate font-headline text-slate-800 dark:text-white" style="direction: ltr; text-align: right;">
                                        <x-phone-number :value="$passenger->passenger_number" class="text-sm text-slate-800 dark:text-white !justify-start" />
                                    </span>
                                    <div class="flex gap-1 items-center mt-1">
                                        <span class="material-symbols-outlined text-[13px] text-amber-500">location_on</span>
                                        <span class="text-xs font-bold truncate text-slate-500 dark:text-gray-400">{{ $passenger->destination ?? ' ' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-2 items-center shrink-0">
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
                                <div class="flex justify-center items-center w-8 h-8 rounded-full border bg-primary/10 text-primary border-primary/20">
                                    <span class="material-symbols-outlined text-[16px] font-bold">done_all</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-boxdark p-10 rounded-[2.5rem] border border-slate-100 dark:border-boxdark-2 shadow-sm flex flex-col items-center justify-center text-slate-400">
                            <div class="flex justify-center items-center mb-3 w-16 h-16 rounded-full bg-slate-50 dark:bg-boxdark-2">
                                <span class="material-symbols-outlined text-[32px] opacity-30">group_off</span>
                            </div>
                            <p class="text-xs font-bold text-center text-slate-500 dark:text-gray-400">لا يوجد ركاب مضافين لهذه الرحلة حتى الآن</p>
                        </div>
                    @endforelse

                    @if($trip->passengers->isNotEmpty())
                        <div x-show="countVisible === 0" x-cloak
                            class="bg-white dark:bg-boxdark p-10 rounded-[2.5rem] border border-slate-100 dark:border-boxdark-2 shadow-sm flex flex-col items-center justify-center text-slate-400">
                            <div class="flex justify-center items-center mb-3 w-16 h-16 rounded-full bg-slate-50 dark:bg-boxdark-2">
                                <span class="material-symbols-outlined text-[32px] opacity-30">search_off</span>
                            </div>
                            <p class="text-xs font-bold text-center text-slate-500 dark:text-gray-400">لا توجد نتائج تطابق بحثك</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ================= نافذة إضافة راكب (Bottom Sheet) ================= --}}
        <div x-show="addModalOpen" x-transition
            class="fixed inset-0 z-[999] flex items-end justify-center bg-black/50 p-4 drop-shadow-2xl" x-cloak>

            <div @click.away="addModalOpen = false"
                class="bg-white dark:bg-boxdark w-full max-w-md rounded-t-[2.5rem] rounded-b-none p-6 animate-slide-up border border-slate-100 dark:border-boxdark-2 shadow-2xl">

                <div class="flex justify-between items-center mb-6">
                    <h3 class="flex gap-2 items-center text-base font-black font-headline text-slate-800 dark:text-white">
                        <span class="material-symbols-outlined text-primary text-[24px]">person_add</span>
                        إضافة راكب من الزاجل
                    </h3>
                    <button @click="addModalOpen = false" class="flex justify-center items-center w-8 h-8 rounded-full transition-colors text-slate-400 hover:text-slate-600 dark:hover:text-gray-300 bg-slate-50 dark:bg-boxdark-2">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form action="{{ route('trip.addPassenger', $trip->id) }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-gray-300">حدد الراكب المطلوب من القائمة</label>
                        <select name="passenger_id" required
                            class="px-4 w-full h-14 text-sm font-bold rounded-2xl border transition-all appearance-none outline-none bg-slate-50 dark:bg-boxdark-2 text-slate-800 dark:text-white border-slate-200 dark:border-boxdark-2 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="">-- اضغط للاختيار --</option>
                            @forelse($pendingPassengers ?? [] as $pending)
                                <option value="{{ $pending->id }}">
                                    {{ $pending->passenger_number }} - (عدد الركاب: {{ $pending->count ?? 1 }}) 
                                </option>
                            @empty
                                <option value="" disabled>لا يوجد ركاب في قائمة الانتظار حالياً</option>
                            @endforelse
                        </select>
                    </div>

                    <button type="submit"
                        class="flex gap-2 justify-center items-center px-4 py-4 w-full text-sm font-black text-white rounded-2xl shadow-md transition-all bg-primary hover:bg-primary/90 active:scale-95">
                        <span class="material-symbols-outlined text-[20px]">add_circle</span>
                        إضافة الراكب للرحلة
                    </button>
                </form>
            </div>
        </div>

        {{-- ================= نافذة فك الارتباط ================= --}}
        <div x-show="showDeleteModal" x-transition
            class="fixed inset-0 z-[999] flex items-end justify-center bg-black/50 p-4 drop-shadow-2xl" x-cloak>

            <div @click.away="showDeleteModal = false"
                class="bg-white dark:bg-boxdark w-full max-w-md rounded-t-[2.5rem] rounded-b-none p-6 text-center animate-slide-up border border-slate-100 dark:border-boxdark-2 shadow-2xl">

                <div class="flex justify-center items-center mx-auto mb-4 w-14 h-14 text-rose-500 bg-rose-50 rounded-full dark:bg-rose-500/10">
                    <span class="material-symbols-outlined text-[28px]">link_off</span>
                </div>

                <h3 class="mb-2 text-base font-black font-headline text-slate-800 dark:text-white">تأكيد فك ارتباط الراكب</h3>

                <p class="mb-6 text-xs font-medium leading-relaxed text-slate-500 dark:text-gray-400">
                    هل أنت متأكد من فك ارتباط الراكب ذو الرقم
                    <span class="inline-block font-black text-slate-700 dark:text-slate-200" x-text="deleteShipmentData.bond_number" style="direction: ltr;"></span>
                    وإعادته إلى قائمة الانتظار؟
                </p>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" @click="showDeleteModal = false"
                        class="px-4 py-3 w-full text-xs font-bold rounded-2xl transition-all text-slate-500 bg-slate-50 hover:bg-slate-100 dark:bg-boxdark-2 dark:text-gray-400">
                        إلغاء
                    </button>

                    <form :action="deleteShipmentData.url" method="POST" class="w-full">
                        @csrf
                        <button type="submit"
                            class="px-4 py-3 w-full text-xs font-bold text-white bg-rose-500 rounded-2xl shadow-md transition-all hover:bg-rose-600 shadow-rose-500/20">
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
                addModalOpen: false,      // تفعيل حالة نافذة الإضافة
                showDeleteModal: false,   // تفعيل حالة نافذة الحذف
                deleteShipmentData: { bond_number: '', url: '' },
                
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