@extends('layouts.app')
@section('title', 'لوحة تحكم المشرف العام')

@section('content')
<div x-data="superAdminDashboard()" class="space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">لوحة تحكم المشرف العام</h1>
            <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">نظرة شاملة على أداء منصة مرسل</p>
        </div>
        <div class="flex gap-2 items-center px-4 py-2 text-sm font-bold text-emerald-700 bg-emerald-50 rounded-xl dark:bg-emerald-500/10 dark:text-emerald-400">
            <span class="relative flex w-2.5 h-2.5"><span class="absolute inline-flex w-full h-full rounded-full opacity-75 animate-ping bg-emerald-400"></span><span class="relative inline-flex w-2.5 h-2.5 rounded-full bg-emerald-500"></span></span>
            النظام يعمل بشكل طبيعي
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @php $cards = [
            ['label'=>'إجمالي المكاتب','value'=>number_format($totalApps),'icon'=>'apartment','color'=>'blue','sub'=>'جميع المكاتب المسجلة'],
            ['label'=>'المكاتب النشطة','value'=>number_format($activeApps),'icon'=>'check_circle','color'=>'emerald','sub'=>($totalApps>0?round(($activeApps/$totalApps)*100):0).'% من الإجمالي'],
            ['label'=>'إجمالي الشحنات','value'=>number_format($totalShipments),'icon'=>'inventory_2','color'=>'amber','sub'=>'على مستوى المنصة'],
            ['label'=>'إجمالي الإيرادات','value'=>number_format($totalRevenue,0).' ر.ي','icon'=>'payments','color'=>'violet','sub'=>'من الاشتراكات النشطة'],
        ]; @endphp
        @foreach($cards as $c)
        <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 transition-all duration-300 hover:shadow-lg hover:ring-primary/20 dark:bg-gray-800 dark:ring-gray-700">
            <div class="absolute -top-8 -left-8 h-24 w-24 rounded-full bg-gradient-to-br from-{{ $c['color'] }}-500/10 to-{{ $c['color'] }}-600/5 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="relative flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400">{{ $c['label'] }}</p>
                    <h3 class="mt-2 text-3xl font-black text-gray-900 dark:text-white">{{ $c['value'] }}</h3>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-{{ $c['color'] }}-50 text-{{ $c['color'] }}-600 dark:bg-{{ $c['color'] }}-500/10 dark:text-{{ $c['color'] }}-400">
                    <span class="material-symbols-outlined text-[28px]">{{ $c['icon'] }}</span>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1 text-xs font-bold text-gray-400 dark:text-gray-500">{{ $c['sub'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <a href="{{ route('superadmin.offices.index') }}" class="flex items-center gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100 transition-all hover:shadow-md dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400"><span class="material-symbols-outlined text-[20px]">apartment</span></div>
            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">إدارة المكاتب</span>
        </a>
        <a href="{{ route('superadmin.packages.index') }}" class="flex items-center gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100 transition-all hover:shadow-md dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400"><span class="material-symbols-outlined text-[20px]">workspace_premium</span></div>
            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">الباقات</span>
        </a>
        <a href="{{ route('superadmin.subscriptions.index') }}" class="flex items-center gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100 transition-all hover:shadow-md dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400"><span class="material-symbols-outlined text-[20px]">card_membership</span></div>
            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">الاشتراكات</span>
        </a>
        <div class="flex items-center gap-3 rounded-xl bg-gradient-to-br from-primary/5 to-primary/10 p-4 ring-1 ring-primary/20">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 text-primary"><span class="material-symbols-outlined text-[20px]">shield_person</span></div>
            <span class="text-sm font-bold text-primary">مشرف عام</span>
        </div>
    </div>

    {{-- Latest Tenants Table --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 dark:border-gray-700">
            <h2 class="text-lg font-black text-gray-900 dark:text-white">آخر المكاتب المسجلة</h2>
            <a href="{{ route('superadmin.offices.index') }}" class="flex items-center gap-1 text-xs font-bold text-primary hover:underline">عرض الكل <span class="material-symbols-outlined text-[16px]">arrow_back</span></a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px]">
                <thead><tr class="bg-gray-50/50 dark:bg-gray-900/30">
                    <th class="px-6 py-3 text-right text-xs font-black uppercase text-gray-500 dark:text-gray-400">المكتب</th>
                    <th class="px-6 py-3 text-right text-xs font-black uppercase text-gray-500 dark:text-gray-400">الباقة</th>
                    <th class="px-6 py-3 text-center text-xs font-black uppercase text-gray-500 dark:text-gray-400">الحالة</th>
                    <th class="px-6 py-3 text-right text-xs font-black uppercase text-gray-500 dark:text-gray-400">تاريخ التسجيل</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                @forelse($latestApps as $app)
                <tr class="transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-700/20">
                    <td class="px-6 py-4"><div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl text-white text-sm font-black" style="background:{{ $app->color ?? '#6366f1' }}">{{ mb_substr($app->name ?? 'م',0,1) }}</div>
                        <div><p class="text-sm font-bold text-gray-900 dark:text-white">{{ $app->name ?? 'بدون اسم' }}</p><p class="text-xs text-gray-400">ID: {{ $app->id }}</p></div>
                    </div></td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-700 dark:text-gray-300">{{ $app->currentSubscription?->package?->name ?? 'بدون باقة' }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($app->is_active)<span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>نشط</span>
                        @else<span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700 dark:bg-red-500/10 dark:text-red-400"><span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>معطل</span>@endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $app->created_at->format('Y/m/d') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-12 text-center"><span class="material-symbols-outlined text-[48px] text-gray-300">apartment</span><p class="mt-2 text-sm text-gray-400">لا توجد مكاتب مسجلة بعد</p></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>function superAdminDashboard() { return {}; }</script>
@endsection
