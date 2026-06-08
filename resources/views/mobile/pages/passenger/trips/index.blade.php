@extends('mobile.layouts.app')

@section('title', 'إدارة الرحلات')

@section('content')

    <div class="flex flex-col pt-4 pb-24 min-h-screen bg-slate-50/50 dark:bg-black" dir="rtl">

        {{-- ================= الهيدر وزر إضافة رحلة جديدة ================= --}}
        <div class="flex justify-between items-center px-4 mb-6">
            <div>
                <h1 class="text-lg font-black font-headline text-slate-800 dark:text-white">قائمة الرحلات الحالية</h1>
                <p class="text-xs text-gray-400 mt-0.5">إدارة وتجميع الركاب داخل الرحلات</p>
            </div>
            
            {{-- زر إضافة رحلة جديدة --}}
            <a href="{{ route('trips.create') }}" class="flex justify-center items-center w-11 h-11 bg-primary text-white rounded-full shadow-md shadow-primary/20 transition-all active:scale-90">
                <span class="material-symbols-outlined text-[24px]">add</span>
            </a>
        </div>

        {{-- ================= حاوية الرحلات الأساسية ================= --}}
        <div class="px-4 space-y-4">
            
            @forelse($trips as $trip)
                {{-- بطاقة كرت الرحلة الواحدة مع تهيئة النطاق المستقل للقائمة المنسدلة --}}
                <div x-data="{ openMenu: false }" class="w-full bg-white dark:bg-boxdark rounded-[2rem] shadow-sm p-5 border border-gray-100 dark:border-boxdark-2 relative transition-all hover:shadow-md">
                    
                    <div class="flex justify-between items-start mb-4">
                        {{-- معلومات السائق كعنوان رئيسي للكرت --}}
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center border border-emerald-100 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400">
                                <span class="material-symbols-outlined text-[26px]">local_taxi</span>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-800 dark:text-white">
                                    {{ $trip->driver->name ?? 'سائق غير معين بعد' }}
                                </h3>
                                @if($trip->driver?->phone)
                                    <span class="text-xs text-gray-400 font-mono block text-right mt-0.5" style="direction: ltr;">{{ $trip->driver->phone }}</span>
                                @else
                                    <span class="text-xs text-amber-500 font-medium block mt-0.5">يرجى تعيين سائق للرحلة</span>
                                @endif
                            </div>
                        </div>

                        {{-- ================= القائمة المنسدلة الثلاث نقاط الذكية ================= --}}
                        <div class="relative">
                            <button @click="openMenu = !openMenu" @click.away="openMenu = false" type="button" 
                                class="flex justify-center items-center w-8 h-8 text-slate-400 hover:text-slate-600 dark:hover:text-white bg-slate-50 dark:bg-boxdark-2 rounded-full transition-all active:scale-90">
                                <span class="material-symbols-outlined text-[20px]">more_vert</span>
                            </button>

                            {{-- صندوق الخيارات --}}
                            <div x-show="openMenu" x-cloak x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute left-0 mt-2 w-44 bg-white dark:bg-boxdark border border-slate-100 dark:border-boxdark-2 rounded-2xl shadow-xl z-30 overflow-hidden py-1">
                                
                                {{-- خيار عرض التفاصيل --}}
                                <a href="{{ route('trips.show', $trip->id) }}" class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-boxdark-2 transition-colors">
                                    <span class="material-symbols-outlined text-[16px] text-blue-500">visibility</span>
                                    عرض التفاصيل
                                </a>

                                {{-- خيار تعديل الرحلة --}}
                                <a href="{{ route('trips.edit', $trip->id) }}" class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-boxdark-2 transition-colors">
                                    <span class="material-symbols-outlined text-[16px] text-amber-500">edit</span>
                                    تعديل الرحلة
                                </a>

                                {{-- خيار طباعة السند --}}
                                <a href="" target="_blank" class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-boxdark-2 transition-colors border-t border-slate-50 dark:border-boxdark-2">
                                    <span class="material-symbols-outlined text-[16px] text-emerald-500">print</span>
                                    طباعة الكشف / السند
                                </a>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-50 dark:border-boxdark-2 my-3">

                    {{-- الإحصائيات السريعة للرحلة --}}
                    <div class="grid grid-cols-2 gap-3">
                        
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-boxdark-2/60 border border-slate-100/50 dark:border-boxdark-2 flex flex-col items-center justify-center">
                            <span class="text-xs text-gray-400 font-bold mb-1">إجمالي الركاب</span>
                            <span class="text-base font-black text-slate-700 dark:text-gray-300">
                                {{ $trip->passengers->sum('count') }}
                            </span>
                        </div>
                    </div>

                </div>
            @empty
                {{-- حالة عدم وجود رحلات مضافة --}}
                <div class="w-full bg-white dark:bg-boxdark rounded-[2.5rem] p-12 border border-gray-100 dark:border-boxdark-2 text-center flex flex-col items-center justify-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-boxdark-2 flex items-center justify-center text-gray-400 mb-3">
                        <span class="material-symbols-outlined text-[32px]">explore_off</span>
                    </div>
                    <h3 class="text-sm font-black text-slate-700 dark:text-white">لا توجد رحلات مضافة حالياً</h3>
                    <p class="text-xs text-gray-400 mt-1 max-w-[200px] mx-auto leading-relaxed">قم بإنشاء أول رحلة الآن لتبدأ بتوزيع الركاب بداخلها.</p>
                    <a href="{{ route('trips.create') }}" class="mt-4 px-6 h-10 bg-primary text-white text-xs font-black rounded-xl shadow-md shadow-primary/20 flex items-center gap-1 transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[16px]">add</span> إنشاء أول رحلة
                    </a>
                </div>
            @endforelse

            {{-- الترقيم وصفحات التنقل (Pagination) --}}
            <div class="pt-4 custom-pagination">
                {{ $trips->links() }}
            </div>

        </div>
    </div>

@endsection