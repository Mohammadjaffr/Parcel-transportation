@extends('mobile.layouts.app')

@section('title', 'الرئيسية - الداشبورد')

@section('content')
    <section class="grid grid-cols-1 gap-6 mb-10">
        <div class="bg-surface-container-lowest rounded-3xl p-8 flex flex-col justify-between min-h-[280px] shadow-sm relative overflow-hidden group">
            <div class="z-10">
                <h1 class="text-3xl font-headline font-bold text-on-surface mb-2">أهلاً بك مجدداً، محمد</h1>
                <p class="text-on-surface-variant max-w-md">لديك 4 شحنات جديدة بانتظار التوزيع اليوم. تحقق من قائمة المهام للبدء.</p>
            </div>
            <div class="z-10 mt-6">
                <button class="bg-gradient-to-br from-primary to-primary-container text-white px-8 py-3 rounded-full font-medium shadow-lg shadow-primary/20 active:scale-95 transition-all">ابدأ العمل</button>
            </div>
            <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-colors"></div>
        </div>

        <div class="bg-indigo-50 rounded-3xl p-8 flex flex-col items-center justify-center text-center border border-indigo-100/50">
            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-4">
                <span class="material-symbols-outlined text-3xl text-indigo-700" data-icon="bolt" style="font-variation-settings: 'FILL' 1;">bolt</span>
            </div>
            <h3 class="font-headline font-bold text-indigo-900 text-xl">معدل الإنجاز</h3>
            <p class="text-indigo-700/70 text-sm mt-1">أنت تتصدر قائمة الأداء هذا الأسبوع!</p>
            <div class="mt-4 text-4xl font-black text-indigo-900">94%</div>
        </div>
    </section>

    <section class="grid grid-cols-2 gap-4 mb-10">
        <div class="bg-surface-container-low rounded-2xl p-6 flex flex-col gap-2">
            <span class="text-on-surface-variant text-sm font-medium">إجمالي الطرود</span>
            <span class="text-3xl font-headline font-black text-on-surface">1,284</span>
        </div>
        <div class="bg-secondary-container/10 rounded-2xl p-6 flex flex-col gap-2">
            <span class="text-on-secondary-fixed-variant text-sm font-medium">قيد التوصيل</span>
            <span class="text-3xl font-headline font-black text-secondary">42</span>
        </div>
        <div class="bg-tertiary-fixed/20 rounded-2xl p-6 flex flex-col gap-2">
            <span class="text-on-tertiary-fixed-variant text-sm font-medium">تم التسليم</span>
            <span class="text-3xl font-headline font-black text-tertiary">1,150</span>
        </div>
        <div class="bg-surface-container-highest rounded-2xl p-6 flex flex-col gap-2">
            <span class="text-on-surface-variant text-sm font-medium">المرتجعات</span>
            <span class="text-3xl font-headline font-black text-on-surface">12</span>
        </div>
    </section>

    <section class="bg-surface-container-lowest rounded-3xl p-2 shadow-sm mb-10">
        <div class="p-6 flex justify-between items-center">
            <h2 class="text-xl font-headline font-bold text-on-surface">آخر الشحنات</h2>
            <button class="text-primary font-medium text-sm hover:underline">عرض الكل</button>
        </div>
        <div class="space-y-1">
            <div class="flex items-center justify-between p-4 hover:bg-surface rounded-2xl transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-indigo-700" data-icon="inventory_2">inventory_2</span>
                    </div>
                    <div>
                        <p class="font-bold text-on-surface">طرد #AD-9021</p>
                        <p class="text-xs text-on-surface-variant">من: شركة التكنولوجيا الحديثة</p>
                    </div>
                </div>
                <div class="text-left">
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">تم الاستلام</span>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 hover:bg-surface rounded-2xl transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-indigo-700" data-icon="local_shipping">local_shipping</span>
                    </div>
                    <div>
                        <p class="font-bold text-on-surface">شحنة #AD-8842</p>
                        <p class="text-xs text-on-surface-variant">إلى: حي المعلا، شارع الشهيد</p>
                    </div>
                </div>
                <div class="text-left">
                    <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-bold">في الطريق</span>
                </div>
            </div>
        </div>
    </section>
@endsection