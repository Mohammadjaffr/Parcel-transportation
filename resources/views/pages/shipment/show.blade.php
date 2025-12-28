@extends('layouts.app')
@section('title', 'تفاصيل الطرد #' . $shipment->bond_number)
@section('Breadcrumb', 'تفاصيل الطرد')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <!-- Success Modal Data Handler -->
    <div x-data="{
        isSuccessModalOpen: false,
        successTitle: '',
        successMessage: '',
    }"
        @open-success-modal.window="
            successTitle = $event.detail.title;
            successMessage = $event.detail.message;
            isSuccessModalOpen = true;
        ">
    </div>

    <div class="space-y-6">

        <!-- Header Section -->
        <div class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex flex-col gap-6 justify-between md:flex-row md:items-center">
                <div class="flex gap-4 items-center">
                    <div
                        class="flex justify-center items-center w-14 h-14 rounded-full bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex gap-3 items-center">
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white">الطرد #{{ $shipment->bond_number }}
                            </h1>
                            <span
                                class="px-2.5 py-0.5 text-xs font-medium text-gray-500 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-400">
                                #{{ $shipment->code ?? $shipment->id }}
                            </span>
                        </div>
                        <div class="flex gap-4 items-center mt-1 text-sm text-gray-500 dark:text-gray-400">
                            <span class="flex gap-1.5 items-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                {{ $shipment->created_at->format('Y/m/d h:i A') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 items-center">
                    @php
                        $statusColors = [
                            'pending' =>
                                'bg-warning-50 text-warning-500 border-warning-200 dark:bg-warning-500/10 dark:text-warning-400 dark:border-warning-500/20',
                            'in_transit' =>
                                'bg-blue-50 text-blue-500 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20',
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
                    <span
                        class="px-3 py-1 text-sm font-medium rounded-full border {{ $statusColors[$shipment->status] ?? $statusColors['pending'] }}">
                        {{ $statusText[$shipment->status] ?? $shipment->status }}
                    </span>

                    <div class="hidden w-px h-8 bg-gray-200 dark:bg-gray-700 md:block"></div>

                    <a href="{{ route('request.invoice', $shipment->id) }}" target="_blank"
                        class="flex gap-2 items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg border border-gray-300 dark:text-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                        طباعة
                    </a>

                    <a href="{{ route('request.edit', $shipment->id) }}"
                        class="flex gap-2 items-center px-4 py-2 text-sm font-medium text-white rounded-lg bg-brand-500 hover:bg-brand-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        تعديل
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            <!-- Main Content: Sender/Receiver + Details -->
            <div class="space-y-6 lg:col-span-2">

                <!-- Stakeholders -->
                <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                    <!-- Sender -->
                    <div
                        class="p-5 bg-white rounded-xl border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-sm font-bold tracking-wider text-gray-500 uppercase dark:text-gray-400">المرسل
                            </h3>
                            <a href="{{ route('whatsapp.sender', $shipment->id) }}" target="_blank"
                                class="text-success-500 hover:text-success-500">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                            </a>
                        </div>
                        <div class="flex gap-4 items-center">
                            <div class="flex-shrink-0">
                                <span
                                    class="inline-flex justify-center items-center p-3 text-teal-500 bg-teal-50 rounded-lg dark:bg-teal-500/10 dark:text-teal-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </span>
                            </div>
                            <div>
                                <h4 class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{ $shipment->senderCustomer->name ?? 'غير محدد' }}</h4>
                                <div class="text-sm text-gray-500 dir-ltr dark:text-gray-400">
                                    {{ $shipment->senderCustomer->phone ?? '-' }}
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex justify-between items-center pt-4 mt-4 text-sm border-t border-gray-100 dark:border-gray-700">
                            <span class="text-gray-500 dark:text-gray-400">الفرع:</span>
                            <span
                                class="font-medium text-gray-900 dark:text-white">{{ $shipment->senderBranch->name ?? 'غير محدد' }}</span>
                        </div>
                    </div>

                    <!-- Receiver -->
                    <div
                        class="p-5 bg-white rounded-xl border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-sm font-bold tracking-wider text-gray-500 uppercase dark:text-gray-400">المستلم
                            </h3>
                            <a href="{{ route('whatsapp.receiver', $shipment->id) }}" target="_blank"
                                class="text-success-500 hover:text-success-500">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                            </a>
                        </div>
                        <div class="flex gap-4 items-center">
                            <div class="flex-shrink-0">
                                <span
                                    class="inline-flex justify-center items-center p-3 text-purple-500 bg-purple-50 rounded-lg dark:bg-purple-500/10 dark:text-purple-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </span>
                            </div>
                            <div>
                                <h4 class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{ $shipment->receiverCustomer->name ?? 'غير محدد' }}</h4>
                                <div class="text-sm text-gray-500 dir-ltr dark:text-gray-400">
                                    {{ $shipment->receiverCustomer->phone ?? '-' }}</div>
                            </div>
                        </div>
                        <div
                            class="flex justify-between items-center pt-4 mt-4 text-sm border-t border-gray-100 dark:border-gray-700">
                            <span class="text-gray-500 dark:text-gray-400">الفرع:</span>
                            <span
                                class="font-medium text-gray-900 dark:text-white">{{ $shipment->receiverBranch->name ?? 'غير محدد' }}</span>
                        </div>
                    </div>
                </div>
                <!-- Sidebar: Financials & Actions -->
                <div class="space-y-6">

                    <!-- Financial Card -->
                    <div
                        class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">البيانات المالية</h3>
                            <div class="p-2 rounded-lg bg-brand-50 text-brand-500 dark:bg-brand-500/10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="p-5 mb-6 text-center bg-gray-50 rounded-xl dark:bg-gray-700">
                            <div class="mb-1 text-sm text-gray-500 dark:text-gray-400">المبلغ الإجمالي</div>
                            <div class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                                {{ number_format($shipment->total_amount, 2) }} <span
                                    class="text-sm font-medium text-gray-500">ر.ي</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div
                                class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-500 dark:text-gray-400">طريقة الدفع</span>
                                @php
                                    $paymentMethodText = match ($shipment->payment_method) {
                                        'prepaid' => 'دفع مسبق',
                                        'cod' => 'دفع عند الاستلام',
                                        'customer_credit' => 'آجل',
                                        'partial_payment' => 'دفع جزئي',
                                        default => $shipment->payment_method,
                                    };
                                @endphp
                                <span class="font-medium text-gray-900 dark:text-white">{{ $paymentMethodText }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-gray-500 dark:text-gray-400">حالة الدين</span>
                                @php
                                    $debtStatusColorRaw = match ($shipment->customer_debt_status) {
                                        'fully_paid' => 'text-success-500',
                                        'partially_paid' => 'text-warning-500',
                                        'pending' => 'text-brand-500',
                                        'overdue' => 'text-error-500',
                                        default => 'text-gray-500',
                                    };
                                    $debtStatusText = match ($shipment->customer_debt_status) {
                                        'fully_paid' => 'مدفوع بالكامل',
                                        'partially_paid' => 'مدفوع جزئياً',
                                        'pending' => 'غير مدفوع',
                                        'overdue' => 'متأخر',
                                        default => 'غير محدد',
                                    };
                                @endphp
                                <span class="font-medium {{ $debtStatusColorRaw }}">{{ $debtStatusText }}</span>
                            </div>
                        </div>
                        <div class="mt-6" x-data="{
                            status: '{{ $shipment->status }}',
                            updating: false,
                            updateStatus() {
                                this.updating = true;
                                fetch('{{ route('request.updateStatus', $shipment->id) }}', {
                                        method: 'PATCH',
                                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                        body: JSON.stringify({ status: this.status })
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        this.updating = false;
                                        if (data.success) {
                                            $dispatch('open-success-modal', { title: data.success_title, message: data.success_message });
                                            setTimeout(() => window.location.reload(), 1500);
                                        }
                                    })
                                    .catch(() => {
                                        this.updating = false;
                                        alert('Error');
                                    });
                            }
                        }">
                            <h3 class="mb-4 text-sm font-bold text-gray-700 dark:text-gray-300">تحديث الحالة السريع</h3>

                            <div class="relative">
                                <select x-model="status" @change="updateStatus()" :disabled="updating"
                                    class="py-3 pr-10 pl-4 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 appearance-none cursor-pointer focus:ring-brand-500 focus:border-brand-500 dark:bg-gray-900 dark:border-gray-500 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500 disabled:opacity-50">
                                    <option value="pending">قيد الانتظار</option>
                                    <option value="in_transit">في الطريق</option>
                                    <option value="delivered">تم التسليم</option>
                                    {{-- <option value="cancelled">ملغي</option>
                            <option value="returned">مرتجع</option> --}}
                                </select>
                                <div
                                    class="flex absolute inset-y-0 left-0 items-center pl-3 text-gray-500 pointer-events-none">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            <div x-show="updating" class="flex gap-1 items-center mt-2 text-xs text-brand-500">
                                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                جاري التحديث...
                            </div>
                        </div>
                    </div>

                    <!-- Status Update Card -->


                </div>
                <!-- Shipment Info -->
                <div
                    class="p-6 bg-white rounded-xl border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <h3 class="mb-5 text-lg font-bold text-gray-900 dark:text-white">تفاصيل الشحنة</h3>
                    <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                        <div class="p-4 bg-gray-50 rounded-xl dark:bg-gray-700">
                            <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">الوزن</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $shipment->weight }} <span
                                    class="text-xs font-normal text-gray-500">كجم</span></div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl dark:bg-gray-700">
                            <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">النوع</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $shipment->package_type ?? '-' }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl dark:bg-gray-700">
                            <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">عدد الجوالين</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $shipment->no_gallons_honey ?? 0 }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl dark:bg-gray-700">
                            <div class="mb-1 text-xs text-gray-500 dark:text-gray-400">عدد القروف</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $shipment->no_honey_jars ?? 0 }}</div>
                        </div>
                    </div>

                    @if ($shipment->notes)
                        <div class="mt-6">
                            <h4 class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">ملاحظات:</h4>
                            <div
                                class="p-4 text-sm leading-relaxed rounded-lg border border-warning-100 bg-warning-50 text-warning-800 dark:bg-gray-900 dark:text-warning-400 dark:border-warning-900">
                                {{ $shipment->notes }}
                            </div>
                        </div>
                    @endif
                </div>

            </div>


        </div>
    </div>
@endsection
