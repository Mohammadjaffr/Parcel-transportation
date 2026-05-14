@extends('layouts.app')

@section('title', 'ملف الموظف | ' . $user->name)

@section('content')
<div class="pb-24 min-h-screen bg-surface dark:bg-boxdark-2 font-body lg:pb-12" dir="rtl">

    {{-- ================= الشريط العلوي (Sticky Header) ================= --}}
    <div class="sticky top-0 z-40 border-b border-gray-100 shadow-sm backdrop-blur-md bg-white/90 dark:bg-boxdark/90 dark:border-boxdark-2">
        <div class="flex justify-between items-center px-4 py-4 mx-auto max-w-7xl md:px-6">
            <div class="flex gap-4 items-center">
                <a href="{{ route('users.index') }}"
                    class="flex justify-center items-center w-10 h-10 text-gray-500 rounded-xl border border-gray-100 shadow-sm transition-colors bg-surface dark:bg-boxdark-2 dark:text-bodydark hover:text-primary dark:hover:text-white dark:border-boxdark active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </a>
                <div>
                    <h1 class="text-xl font-black md:text-2xl font-headline text-on-surface dark:text-white">ملف الموظف</h1>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-bodydark">تفاصيل الحساب وإنتاجية النظام</p>
                </div>
            </div>
            
            {{-- حالة الحساب --}}
            <div class="px-3 py-1.5 rounded-lg border text-xs font-black shadow-sm 
                {{ $user->is_banned ? 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20' : 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' }}">
                <div class="flex gap-1.5 items-center">
                    <span class="w-1.5 h-1.5 rounded-full {{ $user->is_banned ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                    {{ $user->is_banned ? 'محظور' : 'نشط' }}
                </div>
            </div>
        </div>
    </div>

    {{-- ================= محتوى الصفحة (Grid Layout) ================= --}}
    <div class="grid grid-cols-1 gap-6 items-start p-4 mx-auto max-w-7xl md:p-6 xl:grid-cols-12">
        
        {{-- ================= الجانب الأيمن: بيانات الموظف (Sidebar) ================= --}}
        <div class="xl:col-span-4 flex flex-col gap-6 xl:sticky xl:top-[5.5rem]">
            
            <div class="bg-white dark:bg-boxdark p-6 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm relative overflow-hidden">
                {{-- زخرفة خلفية --}}
                <div class="absolute top-0 right-0 w-40 h-40 bg-primary/5 dark:bg-primary/10 rounded-bl-[100px] -z-0 pointer-events-none"></div>
                
                <div class="flex relative z-10 flex-col gap-4 items-center text-center">
                    <div class="w-24 h-24 rounded-[1.5rem] bg-primary-container dark:bg-primary/10 text-primary flex items-center justify-center text-4xl font-black shadow-inner border border-primary/20 dark:border-primary/10 shrink-0">
                        @php
                            $words = explode(' ', $user->name);
                            echo mb_substr($words[0] ?? '', 0, 1, 'utf-8') . (isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '');
                        @endphp
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-on-surface dark:text-white font-headline">{{ $user->name }}</h2>
                        
                        <div class="flex gap-2 justify-center items-center mt-2 text-gray-500 dark:text-bodydark">
                            <span class="material-symbols-outlined text-[16px]">call</span>
                            <p class="font-mono text-sm font-bold dir-ltr"> <x-phone-number :value="$user->phone" /></p>
                        </div>
                        
                        <div class="flex flex-wrap gap-2 justify-center pt-5 mt-5 border-t border-gray-50 dark:border-boxdark-2">
                            <span class="px-3 py-1.5 text-xs font-bold rounded-lg border {{ $user->type == 'admin' ? 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20' : 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20' }}">
                                {{ $user->type == 'admin' ? 'مدير نظام' : 'موظف فرع' }}
                            </span>
                            
                            <span class="flex gap-1.5 items-center px-3 py-1.5 text-xs font-bold text-gray-600 rounded-lg border border-gray-100 shadow-sm bg-surface dark:bg-boxdark-2 dark:text-gray-300 dark:border-boxdark">
                                <span class="material-symbols-outlined text-[16px] text-primary">store</span>
                                {{ $user->branch->name ?? 'غير محدد' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- يمكن إضافة كروت إضافية هنا مستقبلاً (مثل تفاصيل الصلاحيات أو آخر تسجيل دخول) --}}
        </div>

        {{-- ================= الجانب الأيسر: الإحصائيات والسجل ================= --}}
        <div class="flex flex-col gap-6 xl:col-span-8">
            
            {{-- لوحة تقرير الإنتاجية --}}
            <div class="bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm p-6">
                
                {{-- هيدر الإنتاجية وفلاتر الوقت --}}
                <div class="flex flex-col gap-4 justify-between mb-6 lg:flex-row lg:items-center">
                    <h3 class="flex gap-2 items-center text-lg font-black text-on-surface dark:text-white font-headline">
                        <div class="flex justify-center items-center w-8 h-8 rounded-lg bg-primary-container dark:bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[20px]">monitoring</span>
                        </div>
                        إنتاجية الموظف
                    </h3>

                    {{-- فلاتر الفترة الزمنية (Responsive Tabs) --}}
                    <div class="flex overflow-x-auto gap-2 pb-1 custom-scrollbar">
                        <a href="{{ request()->fullUrlWithQuery(['period' => 'all']) }}"
                            class="shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
                            {{ $period == 'all' ? 'bg-gray-100 text-gray-900 border-gray-100 shadow-md dark:bg-gray-100 dark:border-gray-100 dark:shadow-gray-100/20' : 'bg-gray-100 text-gray-500 border-gray-100 hover:bg-gray-100 dark:bg-boxdark-2 dark:text-gray-400 dark:border-boxdark dark:hover:bg-boxdark' }}">
                            السجل الشامل
                        </a>
                        
                        <a href="{{ request()->fullUrlWithQuery(['period' => 'today']) }}"
                            class="shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
                            {{ $period == 'today' ? 'bg-primary text-white border-primary shadow-md shadow-primary/20' : 'bg-surface text-gray-500 border-gray-100 hover:bg-gray-100 dark:bg-boxdark-2 dark:text-gray-400 dark:border-boxdark dark:hover:bg-boxdark' }}">
                            اليوم
                        </a>

                        <a href="{{ request()->fullUrlWithQuery(['period' => 'week']) }}"
                            class="shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
                            {{ $period == 'week' ? 'bg-primary text-white border-primary shadow-md shadow-primary/20' : 'bg-surface text-gray-500 border-gray-100 hover:bg-gray-100 dark:bg-boxdark-2 dark:text-gray-400 dark:border-boxdark dark:hover:bg-boxdark' }}">
                            هذا الأسبوع
                        </a>

                        <a href="{{ request()->fullUrlWithQuery(['period' => 'month']) }}"
                            class="shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
                            {{ $period == 'month' ? 'bg-primary text-white border-primary shadow-md shadow-primary/20' : 'bg-surface text-gray-500 border-gray-100 hover:bg-gray-100 dark:bg-boxdark-2 dark:text-gray-400 dark:border-boxdark dark:hover:bg-boxdark' }}">
                            هذا الشهر
                        </a>
                    </div>
                </div>

                {{-- كروت الإحصائيات (Grid) --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    {{-- إحصائية الطرود --}}
                    <div class="flex overflow-hidden relative flex-col justify-center p-5 rounded-2xl border border-gray-100 shadow-sm transition-all bg-surface dark:bg-boxdark-2 dark:border-boxdark group hover:shadow-md hover:border-orange-200 dark:hover:border-orange-500/30">
                        <div class="absolute -bottom-4 -left-4 transition-transform duration-500 pointer-events-none text-orange-500/5 dark:text-orange-500/5 group-hover:scale-110">
                            <span class="material-symbols-outlined text-[80px]">package_2</span>
                        </div>
                        <div class="flex relative z-10 justify-center items-center mb-3 w-10 h-10 text-orange-600 bg-orange-100 rounded-xl shadow-sm dark:bg-orange-500/20 dark:text-orange-400">
                            <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                        </div>
                        <p class="relative z-10 text-3xl font-black text-on-surface dark:text-white font-headline">{{ $shipmentsCount }}</p>
                        <p class="text-[11px] font-bold text-gray-500 dark:text-bodydark mt-1 relative z-10">طرد تم إصداره</p>
                    </div>

                    {{-- إحصائية الإرساليات --}}
                    <div class="flex overflow-hidden relative flex-col justify-center p-5 rounded-2xl border border-gray-100 shadow-sm transition-all bg-surface dark:bg-boxdark-2 dark:border-boxdark group hover:shadow-md hover:border-blue-200 dark:hover:border-blue-500/30">
                        <div class="absolute -bottom-4 -left-4 transition-transform duration-500 pointer-events-none text-blue-500/5 dark:text-blue-500/5 group-hover:scale-110">
                            <span class="material-symbols-outlined text-[80px]">local_shipping</span>
                        </div>
                        <div class="flex relative z-10 justify-center items-center mb-3 w-10 h-10 text-blue-600 bg-blue-100 rounded-xl shadow-sm dark:bg-blue-500/20 dark:text-blue-400">
                            <span class="material-symbols-outlined text-[20px]">all_inbox</span>
                        </div>
                        <p class="relative z-10 text-3xl font-black text-on-surface dark:text-white font-headline">{{ $manifestsCount }}</p>
                        <p class="text-[11px] font-bold text-gray-500 dark:text-bodydark mt-1 relative z-10">إرسالية مجمعة</p>
                    </div>

                    {{-- إحصائية العملاء --}}
                    <div class="flex overflow-hidden relative flex-col justify-center p-5 rounded-2xl border border-gray-100 shadow-sm transition-all bg-surface dark:bg-boxdark-2 dark:border-boxdark group hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-500/30">
                        <div class="absolute -bottom-4 -left-4 transition-transform duration-500 pointer-events-none text-emerald-500/5 dark:text-emerald-500/5 group-hover:scale-110">
                            <span class="material-symbols-outlined text-[80px]">group</span>
                        </div>
                        <div class="flex relative z-10 justify-center items-center mb-3 w-10 h-10 text-emerald-600 bg-emerald-100 rounded-xl shadow-sm dark:bg-emerald-500/20 dark:text-emerald-400">
                            <span class="material-symbols-outlined text-[20px]">group_add</span>
                        </div>
                        <p class="relative z-10 text-3xl font-black text-on-surface dark:text-white font-headline">{{ $customersCount ?? 0 }}</p>
                        <p class="text-[11px] font-bold text-gray-500 dark:text-bodydark mt-1 relative z-10">عميل جديد مسجل</p>
                    </div>
                </div>
            </div>

            {{-- ================= أحدث النشاطات (سجل الإرساليات) ================= --}}
            <div class="bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-gray-50 dark:border-boxdark-2">
                    <h3 class="flex gap-2 items-center text-lg font-black text-on-surface dark:text-white font-headline">
                        <div class="flex justify-center items-center w-8 h-8 text-gray-500 bg-gray-100 rounded-lg shadow-sm dark:bg-boxdark-2 dark:text-bodydark">
                            <span class="material-symbols-outlined text-[20px]">history</span>
                        </div>
                        سجل النشاطات الحديثة
                    </h3>
                </div>

                @if($recentManifests->isNotEmpty())
                    {{-- عرض الديسكتوب (Data Table) --}}
                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] bg-surface dark:bg-boxdark-2 border-b border-gray-100 dark:border-boxdark">
                                    <th class="px-6 py-4">رقم التتبع</th>
                                    <th class="px-6 py-4">السائق</th>
                                    <th class="px-6 py-4 text-center">الحالة</th>
                                    <th class="px-6 py-4 text-center">التوقيت</th>
                                    <th class="px-6 py-4 text-center">التفاصيل</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-boxdark-2">
                                @foreach($recentManifests as $manifest)
                                    <tr class="transition-all hover:bg-surface/50 dark:hover:bg-boxdark-2/50 group">
                                        <td class="px-6 py-4">
                                            <div class="flex gap-3 items-center">
                                                <div class="flex justify-center items-center w-10 h-10 text-gray-400 rounded-xl border border-gray-100 shadow-sm transition-colors bg-surface dark:bg-boxdark-2 dark:text-bodydark dark:border-boxdark group-hover:text-primary">
                                                    <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                                                </div>
                                                <span class="font-mono text-sm font-black text-on-surface dark:text-white">
                                                    #{{ $manifest->tracking_number }}
                                                </span>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="text-sm font-bold text-gray-600 dark:text-gray-300">
                                                {{ $manifest->driver->name ?? 'غير محدد' }}
                                            </span>
                                        </td>
                                        
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2.5 py-1 rounded-md text-[10px] font-black border {{ $manifest->status == 'delivered' ? 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' : 'bg-surface text-gray-500 border-gray-200 dark:bg-boxdark-2 dark:text-gray-400 dark:border-boxdark' }}">
                                                {{ $manifest->status == 'delivered' ? 'مكتملة' : 'تحديث' }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <span class="flex gap-1.5 justify-center items-center text-xs font-bold text-gray-500 dark:text-bodydark">
                                                <span class="material-symbols-outlined text-[14px]">schedule</span>
                                                {{ $manifest->created_at->diffForHumans() }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('shipmentpackage.outgoing.show', $manifest->id) }}"
                                                class="inline-flex p-2 text-gray-400 rounded-xl border border-gray-100 shadow-sm transition-all bg-surface hover:text-primary hover:bg-primary-container hover:border-primary/20 dark:bg-boxdark-2 dark:border-boxdark dark:hover:bg-primary/10 dark:hover:border-primary/30"
                                                title="عرض التفاصيل">
                                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- عرض الموبايل (Cards) --}}
                    <div class="flex flex-col gap-3 p-4 md:hidden">
                        @foreach($recentManifests as $manifest)
                            <a href="{{ route('shipmentpackage.outgoing.show', $manifest->id) }}" class="flex justify-between items-center p-4 rounded-xl border border-gray-100 shadow-sm transition-colors bg-surface dark:bg-boxdark-2 dark:border-boxdark hover:border-primary/30">
                                <div class="flex gap-3 items-center">
                                    <div class="flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-50 shadow-sm dark:bg-boxdark dark:text-bodydark dark:border-boxdark-2">
                                        <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                                    </div>
                                    <div>
                                        <p class="font-mono text-sm font-black text-on-surface dark:text-white">#{{ $manifest->tracking_number }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 mt-0.5 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[12px]">schedule</span>
                                            {{ $manifest->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2 items-end text-left">
                                    <span class="px-2 py-1 rounded-md text-[9px] font-black {{ $manifest->status == 'delivered' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-white text-gray-500 dark:bg-boxdark dark:text-gray-400 border border-gray-100 dark:border-boxdark-2' }}">
                                        {{ $manifest->status == 'delivered' ? 'مكتملة' : 'تحديث' }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    {{-- حالة عدم وجود نشاطات --}}
                    <div class="flex flex-col justify-center items-center p-12 text-center">
                        <div class="flex justify-center items-center mb-4 w-16 h-16 rounded-full border border-gray-100 shadow-sm bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                            <span class="material-symbols-outlined text-[32px] text-gray-300 dark:text-gray-600">history_toggle_off</span>
                        </div>
                        <h3 class="text-sm font-black text-on-surface dark:text-white font-headline">لا توجد نشاطات مسجلة</h3>
                        <p class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">هذا الموظف لم يقم بإنشاء أي إرساليات بعد.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection