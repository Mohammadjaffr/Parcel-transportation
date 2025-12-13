@extends('layouts.app')

@section('title', 'Dashboard التقارير')
@section('Breadcrumb', 'Dashboard التقارير')

@section('content')

    <style>
        .stat-card {
            min-height: 190px;
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px -8px rgba(0, 0, 0, .15);
        }
    </style>

    <div class="space-y-6">

        <!-- رأس الصفحة -->
        <div class="flex flex-col sm:flex-row justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">لوحة تحكم التقارير</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    نظرة شاملة على أداء النظام والإحصائيات
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('reports.index') }}"
                    class="px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm">
                    جميع التقارير
                </a>
            </div>
        </div>

        <!-- الإحصائيات -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- إجمالي الشحنات -->
            <div
                class="stat-card relative overflow-hidden rounded-2xl p-6
                    bg-gradient-to-br from-brand-50 to-brand-100
                    border border-brand-200 flex flex-col justify-between">

                <div class="absolute -top-6 -end-6 w-24 h-24 opacity-5 pointer-events-none">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5z" />
                    </svg>
                </div>

                <div class="flex justify-between items-start">
                    <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center">
                        📦
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-black/5">
                        {{ number_format($shipmentsGrowth ?? 0, 1) }}%
                    </span>
                </div>

                <div>
                    <p class="text-sm text-gray-600">إجمالي الشحنات</p>
                    <p class="text-3xl font-bold">{{ number_format($shipmentsCount) }}</p>
                    <p class="text-xs text-gray-500 mt-1">مقارنة بالشهر الماضي</p>
                </div>
            </div>

            <!-- الإيرادات -->
            <div
                class="stat-card relative overflow-hidden rounded-2xl p-6
                    bg-gradient-to-br from-success-50 to-success-100
                    border border-success-200 flex flex-col justify-between">

                <div class="absolute -top-6 -end-6 w-24 h-24 opacity-5 pointer-events-none">💰</div>

                <div class="flex justify-between items-start">
                    <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center">
                        💵
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-black/5">
                        {{ number_format($revenueGrowth ?? 0, 1) }}%
                    </span>
                </div>

                <div>
                    <p class="text-sm text-gray-600">الإيرادات</p>
                    <p class="text-3xl font-bold">
                        {{ number_format($revenue, 0) }}
                        <span class="text-lg">ر.س</span>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">إجمالي الفترة</p>
                </div>
            </div>

            <!-- مديونية العملاء -->
            <div
                class="stat-card relative overflow-hidden rounded-2xl p-6
                    bg-gradient-to-br from-warning-50 to-warning-100
                    border border-warning-200 flex flex-col justify-between">

                <div class="absolute -top-6 -end-6 w-24 h-24 opacity-5 pointer-events-none">👥</div>

                <div class="flex justify-between items-start">
                    <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center">
                        👤
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-black/5">
                        {{ $customersDebt > 0 ? 'نشطة' : '—' }}
                    </span>
                </div>

                <div>
                    <p class="text-sm text-gray-600">مديونية العملاء</p>
                    <p class="text-3xl font-bold">
                        {{ number_format($customersDebt, 0) }}
                        <span class="text-lg">ر.س</span>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $customersDebt > 0 ? 'قابلة للتحصيل' : 'لا توجد مديونيات' }}
                    </p>
                </div>
            </div>

            <!-- صافي مديونية الفروع -->
            <div
                class="stat-card relative overflow-hidden rounded-2xl p-6
                    bg-gradient-to-br from-violet-50 to-violet-100
                    border border-violet-200 flex flex-col justify-between">

                <div class="absolute -top-6 -end-6 w-24 h-24 opacity-5 pointer-events-none">🏢</div>

                <div class="flex justify-between items-start">
                    <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center">
                        🏦
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-black/5">
                        {{ $branchesNet > 0 ? 'موجب' : ($branchesNet < 0 ? 'سالب' : 'متوازن') }}
                    </span>
                </div>

                <div>
                    <p class="text-sm text-gray-600">صافي مديونية الفروع</p>
                    <p class="text-3xl font-bold">
                        {{ number_format($branchesNet, 0) }}
                        <span class="text-lg">ر.س</span>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">بعد التسويات</p>
                </div>
            </div>

        </div>

        <!-- الإجراءات السريعة -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border p-6">
            <h3 class="text-lg font-semibold mb-4">إجراءات سريعة</h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('request.create') }}" class="quick-btn">➕ طلب جديد</a>
                <a href="{{ route('reports.shipments') }}" class="quick-btn">📊 تقارير الشحنات</a>
                <a href="{{ route('customers.create') }}" class="quick-btn">👤 عميل جديد</a>
                <a href="{{ route('reports.monthly.closing.pdf') }}" target="_blank" class="quick-btn bg-red-500">
                    📄 إقفال شهري
                </a>
            </div>
        </div>

    </div>

    <style>
        .quick-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: 1rem;
            border-radius: 1rem;
            background: #dc6803;
            color: white;
            font-weight: 600;
            transition: .2s;
        }

        .quick-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, .2);
        }
    </style>

@endsection
