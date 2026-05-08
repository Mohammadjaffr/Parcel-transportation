@extends('SuperAdmin.layouts.app')
@section('title', 'لوحة تحكم المشرف العام')

@section('content')
<div x-data="superAdminDashboard()" class="space-y-6 lg:space-y-8">
    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 font-headline">لوحة تحكم المشرف العام</h1>
            <p class="mt-1 text-sm font-medium text-slate-500">نظرة شاملة على أداء منصة الإدارة</p>
        </div>
        <div class="inline-flex gap-2 items-center self-start px-4 py-2 text-sm font-bold text-emerald-700 bg-emerald-50 rounded-xl border border-emerald-100 shadow-sm md:self-auto">
            <span class="flex relative w-2.5 h-2.5">
                <span class="inline-flex absolute w-full h-full bg-emerald-400 rounded-full opacity-75 animate-ping"></span>
                <span class="inline-flex relative w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            </span>
            النظام يعمل بشكل طبيعي
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 lg:gap-6">
        @php $cards = [
            ['label'=>'إجمالي المكاتب','value'=>number_format($totalApps),'icon'=>'apartment','color'=>'blue','sub'=>'جميع المكاتب المسجلة'],
            ['label'=>'المكاتب النشطة','value'=>number_format($activeApps),'icon'=>'check_circle','color'=>'emerald','sub'=>($totalApps>0?round(($activeApps/$totalApps)*100):0).'% من الإجمالي'],
            ['label'=>'إجمالي الشحنات','value'=>number_format($totalShipments),'icon'=>'inventory_2','color'=>'amber','sub'=>'على مستوى المنصة'],
            ['label'=>'إجمالي الإيرادات','value'=>number_format($totalRevenue,0).' ر.ي','icon'=>'payments','color'=>'violet','sub'=>'من الاشتراكات النشطة'],
        ]; @endphp
        
        @foreach($cards as $c)
        <div class="relative p-6 overflow-hidden transition-all duration-300 bg-white border border-slate-100 rounded-2xl shadow-sm hover:shadow-lg hover:border-{{ $c['color'] }}-200 group">
            <div class="absolute -top-8 -left-8 h-24 w-24 rounded-full bg-{{ $c['color'] }}-50 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="flex relative justify-between items-center">
                <div>
                    <p class="text-sm font-bold text-slate-500">{{ $c['label'] }}</p>
                    <h3 class="mt-2 text-3xl font-black text-slate-900 font-headline">{{ $c['value'] }}</h3>
                </div>
                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-{{ $c['color'] }}-50 text-{{ $c['color'] }}-600">
                    <span class="material-symbols-outlined text-[28px]">{{ $c['icon'] }}</span>
                </div>
            </div>
            <div class="flex relative gap-1 items-center mt-4 text-xs font-bold text-slate-400">{{ $c['sub'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:gap-4">
        <a href="{{ route('superadmin.offices.index') }}" class="flex flex-col gap-3 items-center p-4 bg-white rounded-xl border shadow-sm transition-all sm:flex-row border-slate-100 hover:shadow-md hover:border-blue-200">
            <div class="flex justify-center items-center w-10 h-10 text-blue-600 bg-blue-50 rounded-xl"><span class="material-symbols-outlined text-[20px]">apartment</span></div>
            <span class="text-sm font-bold text-center text-slate-700 sm:text-right">إدارة المكاتب</span>
        </a>
        <a href="{{ route('superadmin.packages.index') }}" class="flex flex-col gap-3 items-center p-4 bg-white rounded-xl border shadow-sm transition-all sm:flex-row border-slate-100 hover:shadow-md hover:border-amber-200">
            <div class="flex justify-center items-center w-10 h-10 text-amber-600 bg-amber-50 rounded-xl"><span class="material-symbols-outlined text-[20px]">workspace_premium</span></div>
            <span class="text-sm font-bold text-center text-slate-700 sm:text-right">الباقات</span>
        </a>
        <a href="{{ route('superadmin.subscriptions.index') }}" class="flex flex-col gap-3 items-center p-4 bg-white rounded-xl border shadow-sm transition-all sm:flex-row border-slate-100 hover:shadow-md hover:border-violet-200">
            <div class="flex justify-center items-center w-10 h-10 text-violet-600 bg-violet-50 rounded-xl"><span class="material-symbols-outlined text-[20px]">card_membership</span></div>
            <span class="text-sm font-bold text-center text-slate-700 sm:text-right">الاشتراكات</span>
        </a>
        <div class="flex flex-col gap-3 items-center p-4 bg-gradient-to-br rounded-xl border sm:flex-row border-primary/20 from-primary/5 to-primary/10">
            <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-primary/10 text-primary"><span class="material-symbols-outlined text-[20px]">shield_person</span></div>
            <span class="text-sm font-bold text-center text-primary sm:text-right">إدارة النظام العليا</span>
        </div>
    </div>

    {{-- Latest Tenants Table --}}
    <div class="overflow-hidden bg-white rounded-2xl border shadow-sm border-slate-100">
        <div class="flex justify-between items-center px-4 py-5 border-b sm:px-6 border-slate-100 bg-slate-50/50">
            <h2 class="text-lg font-black text-slate-900 font-headline">آخر المكاتب المسجلة</h2>
            <a href="{{ route('superadmin.offices.index') }}" class="flex gap-1 items-center text-xs font-bold transition-colors text-primary hover:text-primary-hover">عرض الكل <span class="material-symbols-outlined text-[16px]">arrow_back</span></a>
        </div>
        <div class="overflow-x-auto scrollbar-hide">
            <table class="w-full min-w-[700px] text-right">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="px-6 py-4 text-xs font-black tracking-wider uppercase text-slate-500">المكتب</th>
                        <th class="px-6 py-4 text-xs font-black tracking-wider uppercase text-slate-500">الباقة</th>
                        <th class="px-6 py-4 text-xs font-black tracking-wider text-center uppercase text-slate-500">الحالة</th>
                        <th class="px-6 py-4 text-xs font-black tracking-wider text-center uppercase text-slate-500">تاريخ التسجيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($latestApps as $app)
                    <tr class="transition-colors hover:bg-slate-50/60">
                        <td class="px-6 py-4">
                            <div class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-10 h-10 text-sm font-black text-white rounded-xl shadow-sm" style="background:{{ $app->color ?? '#f79009' }}">{{ mb_substr($app->name ?? 'م',0,1) }}</div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $app->name ?? 'بدون اسم' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-400">ID: {{ $app->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-slate-700">{{ $app->currentSubscription?->package?->name ?? 'بدون باقة' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($app->is_active)
                                <span class="inline-flex gap-1 items-center px-3 py-1 text-xs font-bold text-emerald-700 bg-emerald-50 rounded-full border border-emerald-100"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>نشط</span>
                            @else
                                <span class="inline-flex gap-1 items-center px-3 py-1 text-xs font-bold text-red-700 bg-red-50 rounded-full border border-red-100"><span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>معطل</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-center text-slate-500">{{ $app->created_at->format('Y/m/d') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col justify-center items-center">
                                <div class="flex justify-center items-center mb-3 w-14 h-14 rounded-full bg-slate-50"><span class="material-symbols-outlined text-[32px] text-slate-400">domain_disabled</span></div>
                                <p class="text-sm font-bold text-slate-500">لا توجد مكاتب مسجلة بعد</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>function superAdminDashboard() { return {}; }</script>
@endsection