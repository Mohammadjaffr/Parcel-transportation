@extends('layouts.app')
@section('title', 'تفاصيل رحلة الشحن #' . $package->tracking_number)

@section('style')
    <style>
        /* ============================================================
       Branch Cards – Minimalist Card Design
       Uses CSS variables from tailadmin/build/style.css
       ============================================================ */

        /* ---------- Grid Layout ---------- */
        .branch-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .branch-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .branch-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* ---------- Card ---------- */
        .branch-card {
            position: relative;
            padding: 1.25rem;
            background: var(--color-white);
            border-radius: var(--radius-2xl);
            border: 1px solid var(--color-gray-100);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            transition: box-shadow 0.25s ease, transform 0.25s ease;
        }

        .branch-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        /* ---------- Hover Overlay ---------- */
        .card-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom right, rgba(18, 183, 106, 0.08), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .branch-card:hover .card-overlay {
            opacity: 1;
        }

        /* ---------- Card Body ---------- */
        .card-body {
            position: relative;
            z-index: var(--z-index-1);
        }

        /* ---------- Card Header ---------- */
        .card-header {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .card-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
            border-radius: var(--radius-xl);
            background-color: var(--color-success-50);
            color: var(--color-success-600);
            flex-shrink: 0;
        }

        .card-info {
            flex: 1;
            min-width: 0;
        }

        .card-title {
            margin: 0;
            font-size: var(--text-base);
            font-weight: 900;
            color: var(--color-gray-900);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-subtitle {
            margin: 2px 0 0;
            font-size: var(--text-xs);
            font-weight: var(--font-weight-bold);
            color: var(--color-gray-400);
            text-transform: uppercase;
        }

        /* ---------- Stats Grid ---------- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .stat-box {
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius-xl);
            border: 1px solid transparent;
        }

        /* Warning variant (Parcel Count) */
        .stat-box--warning {
            background-color: var(--color-warning-50);
            border-color: var(--color-warning-400);
        }

        .stat-box--warning .stat-label,
        .stat-box--warning .stat-value {
            color: var(--color-warning-600);
        }

        /* Brand variant (Total Amount) */
        .stat-box--brand {
            background-color: var(--color-brand-50);
            border-color: var(--color-brand-100);
        }

        .stat-box--brand .stat-label,
        .stat-box--brand .stat-value {
            color: var(--color-brand-600);
        }

        .stat-label {
            margin: 0;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-value {
            margin: 2px 0 0;
            font-size: var(--text-lg);
            font-weight: 900;
        }

        .stat-currency {
            font-size: var(--text-xs);
        }

        /* ---------- Card Actions ---------- */
        .card-actions {
            display: flex;
            gap: 0.5rem;
        }

        /* Base button styles */
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
            border-radius: var(--radius-xl);
            font-size: var(--text-xs);
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
        }

        .action-btn:active {
            transform: scale(0.95);
        }

        /* Print button */
        .action-btn--print {
            flex: 1;
            height: 2.5rem;
            padding: 0 1rem;
            color: var(--color-white);
            background-color: var(--color-success-500);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
        }

        .action-btn--print:hover {
            background-color: var(--color-success-600);
            box-shadow: 0 4px 12px rgba(18, 183, 106, 0.35);
        }

        /* WhatsApp button */
        .action-btn--whatsapp {
            width: 2.5rem;
            height: 2.5rem;
            flex-shrink: 0;
            background-color: var(--color-success-50);
            color: var(--color-success-600);
        }

        .action-btn--whatsapp:hover {
            background-color: var(--color-success-500);
            color: var(--color-white);
            box-shadow: 0 4px 12px rgba(18, 183, 106, 0.35);
        }

        /* ---------- Icon Sizes ---------- */
        .action-btn__icon--sm {
            width: 1rem;
            height: 1rem;
        }

        .action-btn__icon--md {
            width: 1.25rem;
            height: 1.25rem;
        }
    </style>
@endsection

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div class="space-y-6 font-outfit" dir="rtl" x-data="{
                        unlinkModalOpen: false,
                        selectedShipmentId: null,
                        selectedBondNumber: '',
                        unlinkLoading: false,
                        openUnlinkModal(id, bondNumber) {
                            this.selectedShipmentId = id;
                            this.selectedBondNumber = bondNumber;
                            this.unlinkModalOpen = true;
                        }
                    }">

        <div
            class="bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm">
            <div class="flex flex-col gap-6 justify-between items-start md:flex-row md:items-center">
                <div class="flex gap-5 items-center">
                    <div
                        class="flex justify-center items-center w-16 h-16 text-white rounded-2xl shadow-xl bg-brand-500 shadow-brand-500/20">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1m-6 0a1 1 0 001-1m-6 0H4" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex gap-3 items-center">
                            <h2 class="text-2xl font-black text-brand-500 bg-brand-50 dark:text-white">رحلة رقم:
                                {{ $package->tracking_number }}
                            </h2>
                            @php
                                $statusColors = [
                                    'pending' =>
                                        'bg-warning-50 text-warning-500 border-warning-200 dark:bg-warning-500/10 dark:text-warning-400 dark:border-warning-500/20',
                                    'in_transit' =>
                                        'bg-blue-light-500 shadow-blue-500/20 text-white border-blue-light-200 dark:bg-blue-light-500/10 dark:text-blue-light-400 dark:border-blue-light-500/20',
                                    'delivered' =>
                                        'bg-success-50 text-success-500 border-success-200 dark:bg-success-500/10 dark:text-success-400 dark:border-success-500/20',
                                    'cancelled' =>
                                        'bg-error-50 text-error-500 border-error-200 dark:bg-error-500/10 dark:text-error-400 dark:border-error-500/20',
                                    'returned' =>
                                        'bg-gray-50 text-gray-500 border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/20',
                                ];
                                $statusText = [
                                    'pending' => 'قيد الانتظار',
                                    'in_transit' => 'في الطريق',
                                    'delivered' => 'تم التسليم',
                                    'cancelled' => 'ملغي',
                                    'returned' => 'مرتجع',
                                ];
                            @endphp
                            @if ($package->shipments->first())
                                <span
                                    class="px-3 py-1 {{ $statusColors[$package->shipments->first()->status] }} rounded-lg text-[10px] font-black uppercase tracking-widest animate-pulse">{{ $statusText[$package->shipments->first()->status] }}</span>
                            @else
                                <span
                                    class="px-3 py-1 bg-gray-50 text-gray-500 border-gray-200 rounded-lg text-[10px] font-black uppercase tracking-widest">لا
                                    توجد طرود</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm font-bold tracking-tighter text-gray-500 uppercase">تاريخ الإنشاء:
                            {{ $package->created_at->format('Y-m-d H:i') }}
                        </p>
                    </div>

                </div>

                <div class="flex flex-wrap gap-4 w-full md:w-auto">
                    <div
                        class="flex-1 px-6 py-3 text-center rounded-2xl border border-gray-100 bg-brand-50 md:flex-none dark:bg-gray-800 dark:border-gray-700">
                        <p class="text-[10px] font-black text-gray-400 uppercase mb-1">إجمالي الطرود</p>
                        <p class="text-xl font-black text-brand-500">{{ $package->shipments->count() }}</p>
                    </div>
                    <div
                        class="flex-1 px-6 py-3 text-center rounded-2xl border border-gray-100 bg-brand-50 md:flex-none dark:bg-gray-800 dark:border-gray-700">
                        <p class="text-[10px] font-black text-gray-400 uppercase mb-1">إجمالي المبالغ</p>
                        <p class="text-xl font-black text-brand-500 dark:text-white">
                            {{ number_format($package->shipments->sum('total_amount')) }} <small class="text-xs">ر.ي</small>
                        </p>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 gap-4 pt-6 mt-8 border-gray-50 md:grid-cols-2 dark:border-gray-800">
                <div
                    class="flex gap-4 items-center p-4 rounded-2xl border bg-brand-50/50 dark:bg-brand-500/5 border-brand-100/50">
                    <div class="flex justify-center items-center w-10 h-10 text-white rounded-full bg-brand-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                stroke-width="2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-brand-500 uppercase">اسم السائق المسؤول</p>
                        <p class="text-base font-black text-gray-900 dark:text-white">{{ $package->driver_name }}</p>
                    </div>
                </div>
                <div
                    class="flex gap-4 items-center p-4 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
                    <div
                        class="flex justify-center items-center w-10 h-10 text-white rounded-full bg-success-500 dark:bg-gray-700 dark:text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                stroke-width="2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-success-500 uppercase">رقم التواصل</p>
                        <p class="font-mono text-base font-black text-gray-900 dark:text-white">{{ $package->driver_phone }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="px-4 my-4">
                {{-- العنوان --}}
                <h3 class="mb-6 text-lg font-black tracking-widest text-gray-900 uppercase dark:text-white">
                    كشوف الحمولة للفروع
                </h3>

                @php
                    // استخراج الفروع الفريدة مع عدد الطرود والمبالغ
                    $branchesWithCounts = $package->shipments
                        ->groupBy('receiver_branch_code')
                        ->map(function ($shipments) {
                            return [
                                'branch' => $shipments->first()->receiverBranch,
                                'count' => $shipments->count(),
                                'total' => $shipments->sum('total_amount'),
                            ];
                        });
                @endphp

                {{-- شبكة البطاقات للفروع --}}
                <div class="branch-grid">
                    @foreach ($branchesWithCounts as $branchCode => $data)
                        {{-- بطاقة الفرع --}}
                        <div class="branch-card">
                            {{-- خلفية gradient خفيفة --}}
                            <div class="card-overlay"></div>

                            <div class="card-body">
                                {{-- رأس البطاقة --}}
                                <div class="card-header">
                                    <div class="card-icon">
                                        <svg class="action-btn__icon--md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div class="card-info">
                                        <h4 class="card-title">
                                            {{ $data['branch']->name }}
                                        </h4>
                                        <p class="card-subtitle">
                                            كود: {{ $branchCode }}
                                        </p>
                                    </div>
                                </div>

                                {{-- الإحصائيات --}}
                                <div class="stats-grid">
                                    <div class="stat-box stat-box--warning">
                                        <p class="stat-label">
                                            عدد الطرود
                                        </p>
                                        <p class="stat-value">
                                            {{ $data['count'] }}
                                        </p>
                                    </div>
                                    <div class="stat-box stat-box--brand">
                                        <p class="stat-label">
                                            المبلغ الإجمالي
                                        </p>
                                        <p class="stat-value">
                                            {{ number_format($data['total']) }} <span class="stat-currency">ر.ي</span>
                                        </p>
                                    </div>
                                </div>

                                {{-- الأزرار --}}
                                <div class="card-actions">
                                    {{-- زر الطباعة --}}
                                    <a href="{{ route('shipmentpackage.print', $package->id) }}" target="_blank"
                                        class="action-btn action-btn--print">
                                        <svg class="action-btn__icon--sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        <span>طباعة PDF</span>
                                    </a>

                                    {{-- زر واتساب --}}
                                    <a href="{{ route('whatsapp.branchManifest', [$package->id, $branchCode]) }}"
                                        target="_blank" class="action-btn action-btn--whatsapp"
                                        title="إرسال لـ {{ $data['branch']->name }} عبر واتساب">
                                        <svg class="action-btn__icon--md" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- فاصل --}}
                <div class="my-8 border-t border-gray-100 dark:border-gray-800"></div>

                {{-- كشف السائق --}}
                <div class="flex flex-col gap-3 my-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-black tracking-widest text-gray-900 uppercase dark:text-white">
                            كشف الحمولة للسائق
                        </h3>
                        <p class="mt-1 text-xs font-bold text-gray-500 dark:text-gray-400">
                            جميع الطرود في الرحلة
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('shipmentpackage.printD', $package->id) }}" target="_blank"
                            class="flex gap-2 items-center px-5 h-11 text-sm font-bold text-white rounded-xl shadow-md transition-all bg-brand-500 hover:bg-brand-600 hover:shadow-lg active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>طباعة كشف السائق</span>
                        </a>

                        <a href="{{ route('whatsapp.driverManifest', $package->id) }}" target="_blank"
                            class="inline-flex justify-center items-center w-11 h-11 rounded-xl transition-all bg-brand-50 text-brand-500 hover:bg-brand-500 hover:text-white hover:shadow-md active:scale-95 dark:bg-brand-500/10"
                            title="مشاركة عبر واتساب">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                            </svg>
                        </a>
                    </div>
                    {{-- زر تحويل كافة طرود الرحلة إلى "تم الاستلام" --}}
                    <form action="{{ route('shipmentpackage.mark-all-delivered', $package->id) }}" method="POST"
                        onsubmit="return confirm('تحذير: سيتم تحويل حالة جميع الطرود في هذه الرحلة ({{ $package->shipments->count() }} طرد) إلى تم الاستلام. هل أنت متأكد؟')"
                        class="inline-block">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="flex gap-2 items-center px-5 h-11 text-sm font-bold text-white rounded-xl shadow-md transition-all bg-success-500 hover:bg-success-600 hover:shadow-lg active:scale-95">
                            <i class="fas fa-check-double"></i>
                            <span>توصيل كافة الطرود</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- العنوان الأصلي --}}
            <div class="px-4 mt-8">
                <h3 class="text-lg font-black tracking-widest text-gray-900 uppercase dark:text-white">
                    قائمة الطرود الملحقة
                </h3>
            </div>


            <div
                class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
                <div class="overflow-x-auto px-4 pb-4">
                    <table class="w-full text-right border-separate border-spacing-y-3">
                        <thead>
                            <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                                <th class="px-6 py-4">رقم السند</th>
                                <th class="px-6 py-4">الأطراف (مرسل/مستلم)</th>
                                <th class="px-6 py-4 text-center">الوجهة</th>
                                <th class="px-6 py-4 text-center">النوع</th>
                                <th class="px-6 py-4 text-left">التكلفة</th>
                                <th class="px-6 py-4 text-left">الحالة</th>
                                <th class="px-6 py-4 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-0">
                            @foreach ($package->shipments as $shipment)
                                <tr
                                    class="bg-white rounded-2xl border border-transparent shadow-sm transition-all dark:bg-gray-900 hover:shadow-md hover:border-gray-100 dark:hover:border-gray-800 group">
                                    <td class="px-6 py-5 border-r first:rounded-r-2xl border-y dark:border-gray-800/50">
                                        <span
                                            class="px-3 py-1.5 text-xs font-black bg-gray-50 rounded-lg border border-gray-100 shadow-inner dark:bg-gray-800 text-brand-500 dark:border-gray-700">
                                            #{{ $shipment->bond_number }}
                                        </span>
                                    </td>

                                    <td
                                        class="py-5 px-6 border-y dark:border-gray-800/50 text-center text-[10px] font-black uppercase text-gray-500">
                                        {{ $shipment->senderCustomer->name ?? '-' }} ⇠
                                        {{ $shipment->receiverCustomer->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                        <span
                                            class="px-3 py-1 bg-brand-50 dark:bg-brand-500/10 text-brand-500 rounded-lg text-[10px] font-black uppercase">
                                            {{ $shipment->receiverBranch->name }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                        <span
                                            class="text-[10px] font-black text-gray-500 uppercase">{{ $shipment->package_type }}</span>
                                    </td>

                                    <td class="px-6 py-5 text-left border-y dark:border-gray-800/50">
                                        <span class="text-base font-black text-gray-900 dark:text-white">
                                            {{ number_format($shipment->total_amount) }}
                                            <small class="text-[10px] font-bold text-gray-400 mr-0.5 uppercase">ر.ي</small>
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                        @php
                                            $colors = [
                                                'pending' => 'bg-warning-500 shadow-warning-500/20',
                                                'in_transit' => 'bg-blue-light-500 shadow-blue-500/20',
                                                'delivered' => 'bg-success-500 shadow-success-500/20',
                                                'cancelled' => 'bg-error-500 shadow-error-500/20',
                                                'returned' => 'bg-gray-500 shadow-gray-500/20',
                                            ];
                                            $labels = [
                                                'pending' => 'قيد الانتظار',
                                                'in_transit' => 'في الطريق',
                                                'delivered' => 'تم التسليم',
                                                'cancelled' => 'ملغي',
                                                'returned' => 'مرتجع',
                                            ];
                                        @endphp
                                        <span
                                            class="px-3 py-1 rounded-lg text-[10px] font-black text-white uppercase shadow-lg {{ $colors[$shipment->status] ?? 'bg-gray-500' }}">
                                            {{ $labels[$shipment->status] ?? $shipment->status }}
                                        </span>
                                    </td>

                                    <td
                                        class="px-6 py-5 text-center border-l last:rounded-l-2xl border-y dark:border-gray-800/50">
                                        <div class="flex gap-1 justify-center items-center">
                                            {{-- زر عرض التفاصيل --}}
                                            <a href="{{ route('shipment.show', $shipment->id) }}"
                                                class="inline-flex p-2 text-gray-400 rounded-xl transition-all hover:text-brand-500 hover:bg-brand-50"
                                                title="عرض تفاصيل الطرد">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            {{-- زر فك الربط من الرحلة (للطرود في الطريق فقط) --}}
                                            @if ($shipment->status === 'in_transit')
                                                <button
                                                    @click="openUnlinkModal({{ $shipment->id }}, '{{ $shipment->bond_number }}')"
                                                    class="inline-flex p-2 rounded-xl transition-all text-warning-500 hover:bg-warning-50"
                                                    title="فك ربط الطرد من الرحلة">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        {{-- Unlink Shipment from Package Modal --}}
        @include('pages.shipmentpackage.modals.unlink-modal')

    </div>
    </div>
@endsection