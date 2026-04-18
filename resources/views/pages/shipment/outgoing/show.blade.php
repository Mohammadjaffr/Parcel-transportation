@extends('layouts.app')
@section('title', 'تفاصيل الطرد #' . $shipment->bond_number)
@section('Breadcrumb', 'تفاصيل الطرد')

@section('content')

    {{-- Payment Modals --}}
    @include('components.modals.payment-modal')
    @include('components.modals.payments-list-modal')

    <div x-data="{
            isgreenModalOpen: false,
            greenTitle: '',
            greenMessage: '',
        }" 
        @open-green-modal.window="
            greenTitle = $event.detail.title;
            greenMessage = $event.detail.message;
            isgreenModalOpen = true;
        ">
    </div>

    @php
        $paidAmount = $shipment->payments->sum('amount');
        $remainingAmount = $shipment->total_amount - $paidAmount;
        $isUnpaid = $remainingAmount > 0;
    @endphp

    <div class="space-y-6 font-outfit" dir="rtl" x-data="{
            paymentModalOpen: false,
            paymentData: { shipmentId: {{ $shipment->id }}, amount: {{ $remainingAmount }}, maxAmount: {{ $remainingAmount }}, paymentType: 'cash', referenceNumber: '', notes: '' },
            paymentsListModalOpen: false,
            paymentsList: {{ Js::from($shipment->payments) }},
            returnModalOpen: false,
            returnReason: '',
            returnLoading: false,
            cancelModalOpen: false,
            cancelReason: '',
            cancelLoading: false,
            unlinkModalOpen: false,
            selectedShipmentId: null,
            selectedBondNumber: '',
            unlinkLoading: false,
            openUnlinkModal(shipmentId, bondNumber) {
                this.selectedShipmentId = shipmentId;
                this.selectedBondNumber = bondNumber;
                this.unlinkModalOpen = true;
            }
        }">

        <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-[2rem] dark:bg-boxdark dark:border-gray-800">
            <div class="flex flex-col gap-6 justify-between md:flex-row md:items-center">
                
                {{-- معلومات الطرد الأساسية --}}
                <div class="flex gap-4 items-center">
                    <div class="flex justify-center items-center w-14 h-14 rounded-2xl bg-primary/10 text-primary dark:bg-primary/20">
                        <span class="material-symbols-outlined text-[28px]">inventory_2</span>
                    </div>
                    <div>
                        <div class="flex gap-3 items-center">
                            <h1 class="text-xl font-black text-gray-900 dark:text-white">الطرد #{{ $shipment->bond_number }}</h1>
                            <span class="px-2.5 py-0.5 text-xs font-bold text-gray-600 bg-gray-100 rounded-lg border border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700">
                                Code: {{ $shipment->code ?? $shipment->id }}
                            </span>
                        </div>
                        <div class="flex gap-2 items-center mt-1.5 text-sm font-medium text-gray-500 dark:text-gray-400">
                            <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                            {{ $shipment->created_at->format('Y/m/d h:i A') }}
                        </div>
                    </div>
                </div>

                {{-- الأزرار والإجراءات --}}
                <div class="flex flex-wrap gap-3 items-center">
                    
                    {{-- شارة حالة الطرد --}}
                    @php
                        $statusConfig = [
                            'pending'    => ['bg' => 'bg-warning-50 dark:bg-warning-500/10', 'text' => 'text-warning-600 dark:text-warning-400', 'label' => 'قيد الانتظار', 'dot' => 'bg-warning-500', 'border' => 'border-warning-200 dark:border-warning-500/20'],
                            'in_transit' => ['bg' => 'bg-blue-50 dark:bg-blue-500/10', 'text' => 'text-blue-600 dark:text-blue-400', 'label' => 'في الطريق', 'dot' => 'bg-blue-500', 'border' => 'border-blue-200 dark:border-blue-500/20'],
                            'delivered'  => ['bg' => 'bg-green-50 dark:bg-green-500/10', 'text' => 'text-green-600 dark:text-green-400', 'label' => 'تم التسليم', 'dot' => 'bg-green-500', 'border' => 'border-green-200 dark:border-green-500/20'],
                            'cancelled'  => ['bg' => 'bg-error-50 dark:bg-error-500/10', 'text' => 'text-error-600 dark:text-error-400', 'label' => 'ملغي', 'dot' => 'bg-error-500', 'border' => 'border-error-200 dark:border-error-500/20'],
                            'returned'   => ['bg' => 'bg-gray-100 dark:bg-gray-800', 'text' => 'text-gray-600 dark:text-gray-400', 'label' => 'مرتجع', 'dot' => 'bg-gray-500', 'border' => 'border-gray-200 dark:border-gray-700'],
                        ];
                        $currentStatus = $statusConfig[$shipment->status] ?? $statusConfig['pending'];
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-black uppercase rounded-full border {{ $currentStatus['bg'] }} {{ $currentStatus['text'] }} {{ $currentStatus['border'] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $currentStatus['dot'] }}"></span>
                        {{ $currentStatus['label'] }}
                    </span>

                    <div class="hidden w-px h-8 bg-gray-200 dark:bg-gray-700 md:block"></div>

                    {{-- أزرار الطباعة --}}
                    <a href="{{ route('receipt.generate', ['type' => 'sender', 'id' => $shipment->id]) }}" target="_blank" title="طباعة الفاتورة"
                        class="flex justify-center items-center w-10 h-10 text-gray-600 bg-white rounded-xl border border-gray-200 transition-colors hover:bg-gray-50 hover:text-primary dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                    </a>
                    
                    <a href="{{ route('shipment.printThermal', $shipment->id) }}" target="_blank" title="طباعة بوليصة حرارية"
                        class="flex justify-center items-center w-10 h-10 text-gray-600 bg-white rounded-xl border border-gray-200 transition-colors hover:bg-gray-50 hover:text-primary dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                        <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                    </a>

                    {{-- زر فك الارتباط (إذا كان في الطريق) --}}
                    @if ($shipment->status === 'in_transit')
                        <button @click="openUnlinkModal({{ $shipment->id }}, '{{ $shipment->bond_number }}')" title="فك ربط الطرد من الرحلة"
                            class="flex justify-center items-center w-10 h-10 bg-white rounded-xl border border-gray-200 transition-colors text-warning-500 hover:bg-warning-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-warning-500/10">
                            <span class="material-symbols-outlined text-[20px]">link_off</span>
                        </button>
                    @endif

                    {{-- زر الإلغاء --}}
                    @if (in_array($shipment->status, ['pending']))
                        <button @click="cancelModalOpen = true" title="إلغاء الطرد"
                            class="flex justify-center items-center w-10 h-10 bg-white rounded-xl border border-gray-200 transition-colors text-error-500 hover:bg-error-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-error-500/10">
                            <span class="material-symbols-outlined text-[20px]">cancel</span>
                        </button>
                    @endif

                    {{-- زر التعديل --}}
                    <a href="{{ route('shipment.edit', $shipment->id) }}"
                        class="flex gap-2 items-center px-5 h-10 text-sm font-bold text-white rounded-xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">edit_square</span>
                        تعديل
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            <div class="space-y-6 lg:col-span-2">

                <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                    
                    <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-[2rem] dark:bg-boxdark dark:border-gray-800 transition-all hover:border-primary/30">
                        <div class="flex justify-between items-start mb-5">
                            <div class="flex gap-2 items-center text-sm font-black tracking-widest text-gray-400 uppercase dark:text-gray-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                بيانات المرسل
                            </div>
                            <div class="flex gap-2 items-center">
                                @if ($shipment->senderCustomer)
                                    <a href="{{ route('customers.show', $shipment->senderCustomer->id) }}" title="ملف العميل"
                                        class="flex justify-center items-center w-8 h-8 text-gray-400 bg-gray-50 rounded-lg transition-colors hover:bg-primary/10 hover:text-primary dark:bg-gray-800 dark:hover:bg-gray-700">
                                        <span class="material-symbols-outlined text-[18px]">person</span>
                                    </a>
                                @endif
                                <a href="{{ route('whatsapp.sender', $shipment->id) }}" target="_blank" title="مراسلة واتساب"
                                    class="flex justify-center items-center w-8 h-8 text-green-500 bg-green-50 rounded-lg transition-colors hover:bg-green-100 dark:bg-green-500/10">
                                    {{-- أيقونة واتساب مخصصة --}}
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div class="flex gap-4 items-center mb-5">
                            <div class="flex flex-shrink-0 justify-center items-center w-12 h-12 text-sm font-bold rounded-xl bg-primary/10 text-primary dark:bg-primary/20">
                                {{ mb_substr($shipment->senderCustomer->name ?? ($shipment->sender_name ?? '?'), 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-900 dark:text-white">
                                    {{ $shipment->senderCustomer->name ?? 'غير محدد' }}
                                </h4>
                                <div class="mt-1 text-sm font-medium text-gray-500 dir-ltr dark:text-gray-400">
                                    {{ $shipment->senderCustomer->phone ?? '-' }}
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-4 text-sm border-t border-gray-100 dark:border-gray-700">
                            <span class="font-bold text-gray-400 dark:text-gray-500">فرع الإرسال:</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ $shipment->senderBranch->name ?? 'غير محدد' }}</span>
                        </div>
                    </div>

                    <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-[2rem] dark:bg-boxdark dark:border-gray-800 transition-all hover:border-primary/30">
                        <div class="flex justify-between items-start mb-5">
                            <div class="flex gap-2 items-center text-sm font-black tracking-widest text-gray-400 uppercase dark:text-gray-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                بيانات المستلم
                            </div>
                            <div class="flex gap-2 items-center">
                                @if ($shipment->receiverCustomer)
                                    <a href="{{ route('customers.show', $shipment->receiverCustomer->id) }}" title="ملف العميل"
                                        class="flex justify-center items-center w-8 h-8 text-gray-400 bg-gray-50 rounded-lg transition-colors hover:bg-primary/10 hover:text-primary dark:bg-gray-800 dark:hover:bg-gray-700">
                                        <span class="material-symbols-outlined text-[18px]">person</span>
                                    </a>
                                @endif
                                <a href="{{ route('whatsapp.receiver', $shipment->id) }}" target="_blank" title="مراسلة واتساب"
                                    class="flex justify-center items-center w-8 h-8 text-green-500 bg-green-50 rounded-lg transition-colors hover:bg-green-100 dark:bg-green-500/10">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div class="flex gap-4 items-center mb-5">
                            <div class="flex flex-shrink-0 justify-center items-center w-12 h-12 text-sm font-bold rounded-xl bg-primary/10 text-primary dark:bg-primary/20">
                                {{ mb_substr($shipment->receiverCustomer->name ?? ($shipment->receiver_name ?? '?'), 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-900 dark:text-white">
                                    {{ $shipment->receiverCustomer->name ?? 'غير محدد' }}
                                </h4>
                                <div class="mt-1 text-sm font-medium text-gray-500 dir-ltr dark:text-gray-400">
                                    {{ $shipment->receiverCustomer->phone ?? '-' }}
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-4 text-sm border-t border-gray-100 dark:border-gray-700">
                            <span class="font-bold text-gray-400 dark:text-gray-500">فرع الاستلام:</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ $shipment->receiverBranch->name ?? 'غير محدد' }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-[2rem] dark:bg-boxdark dark:border-gray-800">
                    <div class="flex gap-2 items-center mb-6 text-sm font-black tracking-widest text-gray-400 uppercase dark:text-gray-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                        بيانات محتوى الطرد
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 dark:bg-gray-800/50 dark:border-gray-700/50">
                            <div class="mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">نوع الشحنة</div>
                            <div class="text-base font-black text-gray-900 dark:text-white">{{ $shipment->package_type ?? '-' }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 dark:bg-gray-800/50 dark:border-gray-700/50">
                            <div class="mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">عدد الجوالين</div>
                            <div class="text-base font-black text-gray-900 dark:text-white">{{ $shipment->no_gallons_honey ?? 0 }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 dark:bg-gray-800/50 dark:border-gray-700/50">
                            <div class="mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">عدد القروف</div>
                            <div class="text-base font-black text-gray-900 dark:text-white">{{ $shipment->no_honey_jars ?? 0 }}</div>
                        </div>
                    </div>

                    @if ($shipment->notes)
                        <div class="pt-5 mt-6 border-t border-gray-100 dark:border-gray-800">
                            <h4 class="mb-3 text-xs font-bold text-gray-500 dark:text-gray-400">ملاحظات هامة:</h4>
                            <div class="p-4 text-sm font-medium leading-relaxed rounded-xl border border-warning-200 bg-warning-50 text-warning-800 dark:bg-warning-500/10 dark:text-warning-400 dark:border-warning-500/20">
                                {{ $shipment->notes }}
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            <div class="space-y-6 lg:col-span-1">

                <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-[2rem] dark:bg-boxdark dark:border-gray-800">
                    
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex gap-2 items-center text-sm font-black tracking-widest text-gray-400 uppercase dark:text-gray-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                            المالية
                        </div>
                        <div class="flex justify-center items-center w-8 h-8 rounded-lg bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                            <div class="text-sm font-bold text-gray-500 dark:text-gray-400">إجمالي المبلغ</div>
                            <div class="font-mono text-2xl font-black tracking-tight text-gray-900 dark:text-white">
                                {{ number_format($shipment->total_amount, 0) }} 
                                <span class="font-sans text-[11px] font-bold text-gray-400">ر.ي</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-sm font-bold text-gray-500 dark:text-gray-400">طريقة الدفع</span>
                            @php
                                $paymentMethodText = match ($shipment->payment_method) {
                                    'prepaid' => 'دفع مسبق',
                                    'cod' => 'دفع عند الاستلام',
                                    'customer_credit' => 'آجل',
                                    'partial_payment' => 'دفع جزئي',
                                    default => $shipment->payment_method,
                                };
                            @endphp
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $paymentMethodText }}</span>
                        </div>

                        <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-sm font-bold text-gray-500 dark:text-gray-400">حالة الدين</span>
                            @php
                                $debtConfig = [
                                    'fully_paid'     => ['text' => 'text-green-600 dark:text-green-400', 'label' => 'مدفوع بالكامل'],
                                    'partially_paid' => ['text' => 'text-warning-600 dark:text-warning-400', 'label' => 'مدفوع جزئياً'],
                                    'pending'        => ['text' => 'text-primary', 'label' => 'غير مدفوع'],
                                    'overdue'        => ['text' => 'text-error-600 dark:text-error-400', 'label' => 'متأخر'],
                                ];
                                $debtStatus = $debtConfig[$shipment->customer_debt_status] ?? ['text' => 'text-gray-500', 'label' => 'غير محدد'];
                            @endphp
                            <span class="text-sm font-black {{ $debtStatus['text'] }}">{{ $debtStatus['label'] }}</span>
                        </div>

                        @if ($shipment->payment_method === 'partial_payment' && $paidAmount > 0)
                            <div class="flex justify-between items-center pt-3">
                                <span class="text-sm font-bold text-gray-500 dark:text-gray-400">المبلغ المدفوع</span>
                                <span class="font-mono text-lg font-black text-green-500">
                                    {{ number_format($paidAmount, 0) }} <span class="font-sans text-[10px]">ر.ي</span>
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- تحديث الحالة السريع (يظهر فقط إذا كان بالطريق) --}}
                    @if ($shipment->status === 'in_transit')
                        <div class="pt-6 mt-6 border-t border-gray-100 dark:border-gray-800" x-data="{
                                status: '{{ $shipment->status }}',
                                updating: false,
                                updateStatus() {
                                    this.updating = true;
                                    fetch('{{ route('shipment.updateStatus', $shipment->id) }}', {
                                        method: 'PATCH',
                                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                        body: JSON.stringify({ status: this.status })
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        this.updating = false;
                                        if (data.green) {
                                            $dispatch('open-green-modal', { title: data.green_title, message: data.green_message });
                                            setTimeout(() => window.location.reload(), 1500);
                                        }
                                    })
                                    .catch(() => {
                                        this.updating = false;
                                        alert('حدث خطأ في الاتصال');
                                    });
                                }
                            }">
                            
                            <h3 class="mb-3 text-sm font-bold text-gray-700 dark:text-gray-300">تحديث الحالة السريع</h3>
                            <div class="relative group">
                                <select x-model="status" @change="updateStatus()" :disabled="updating"
                                    class="pr-10 pl-4 w-full h-12 text-sm font-bold text-gray-900 bg-gray-50 rounded-xl border border-gray-200 transition-all appearance-none cursor-pointer outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary dark:bg-gray-900 dark:border-gray-700 dark:text-white disabled:opacity-50">
                                    <option value="in_transit">في الطريق</option>
                                    <option value="delivered">تم التسليم</option>
                                </select>
                                <div class="flex absolute inset-y-0 right-0 items-center pr-3 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary">
                                    <span class="material-symbols-outlined text-[20px]">keyboard_arrow_down</span>
                                </div>
                            </div>
                            
                            <div x-show="updating" class="flex gap-1.5 items-center mt-2 text-xs font-bold text-primary">
                                <span class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span>
                                جاري التحديث...
                            </div>
                        </div>
                    @endif
                </div>

            </div>

        </div>

        {{-- Modals Includes --}}
        @include('pages.shipment.modals.cancel-shipment-modal', ['shipment' => $shipment])
        @include('pages.shipmentpackage.modals.unlink-modal')
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('shipmentPage', (shipmentId, remainingAmount, paymentsList) => ({
                // Payment Modal State
                paymentModalOpen: false,
                paymentData: {
                    shipmentId: shipmentId,
                    amount: remainingAmount,
                    maxAmount: remainingAmount,
                    paymentType: 'cash',
                    referenceNumber: '',
                    notes: ''
                },
                openPaymentModal(sid, rem) {
                    this.paymentData.shipmentId = sid;
                    this.paymentData.amount = rem;
                    this.paymentData.maxAmount = rem;
                    this.paymentData.paymentType = 'cash';
                    this.paymentData.referenceNumber = '';
                    this.paymentData.notes = '';
                    this.paymentModalOpen = true;
                },
                closePaymentModal() {
                    this.paymentModalOpen = false;
                },

                // Payments List Modal State
                paymentsListModalOpen: false,
                paymentsList: paymentsList,
                openPaymentsListModal() {
                    this.paymentsListModalOpen = true;
                },
                closePaymentsListModal() {
                    this.paymentsListModalOpen = false;
                },

                // Return Modal State
                returnModalOpen: false,
                returnReason: '',
                returnLoading: false,
                submitReturn() {
                    if (!this.returnReason.trim()) {
                        alert('يرجى إدخال سبب الإرجاع');
                        return;
                    }
                    this.returnLoading = true;
                    fetch('/shipment/' + shipmentId + '/return', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ reason: this.returnReason })
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.returnLoading = false;
                        if (data.green) {
                            this.returnModalOpen = false;
                            alert(data.green_message);
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            alert(data.error_message || 'حدث خطأ');
                        }
                    })
                    .catch(() => {
                        this.returnLoading = false;
                        alert('حدث خطأ في الاتصال');
                    });
                },

                // Cancel Modal State
                cancelModalOpen: false,
                cancelReason: '',
                cancelLoading: false,
                submitCancel() {
                    if (!this.cancelReason.trim()) {
                        alert('يرجى إدخال سبب الإلغاء');
                        return;
                    }
                    this.cancelLoading = true;
                    fetch('/shipment/' + shipmentId + '/cancel', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ reason: this.cancelReason })
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.cancelLoading = false;
                        if (data.green) {
                            this.cancelModalOpen = false;
                            alert(data.green_message);
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            alert(data.error_message || 'حدث خطأ');
                        }
                    })
                    .catch(() => {
                        this.cancelLoading = false;
                        alert('حدث خطأ في الاتصال');
                    });
                }
            }));
        });
    </script>
@endsection