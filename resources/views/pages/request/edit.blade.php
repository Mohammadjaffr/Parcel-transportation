@extends('layouts.app')
@section('title', 'تعديل طرد')
@section('Breadcrumb', 'تعديل طرد')

@section('content')

    <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800"
        x-data="{
            payment_method: @js(old('payment_method', $shipment->payment_method)),
            prepaid_method: @js(old('prepaid_payment_method', 'cash')),
            isSenderReceiverModalOpen: false,
            isDetailsModalOpen: false,
            isPaymentModalOpen: false
        }">

        <form action="{{ route('request.update', $shipment->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- كروت مختصرة + أزرار فتح المودالات --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                {{-- كرت بيانات المرسل والمستلم --}}
                <div
                    class="flex flex-col justify-between p-4 h-full bg-gray-50 rounded-xl border border-gray-200 dark:bg-gray-900 dark:border-gray-700">
                    <div>
                        <h3 class="mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">بيانات المرسل والمستلم</h3>

                        <div class="space-y-2 text-xs text-gray-600 dark:text-gray-400">
                            <div>
                                <span class="font-semibold">المرسل:</span>
                                <span>{{ $shipment->sender_name }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">المستلم:</span>
                                <span>{{ $shipment->receiver_name }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">من الفرع:</span>
                                <span>{{ auth()->user()->branch->name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">إلى الفرع:</span>
                                <span>{{ $shipment->receiverBranch->name ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <button type="button" @click="isSenderReceiverModalOpen = true"
                        class="inline-flex justify-center items-center px-3 py-2 mt-4 text-xs font-medium text-white rounded-lg bg-brand-500 hover:bg-brand-600">
                        تعديل بيانات المرسل والمستلم
                    </button>
                </div>

                {{-- كرت تفاصيل الطرد --}}
                <div
                    class="flex flex-col justify-between p-4 h-full bg-gray-50 rounded-xl border border-gray-200 dark:bg-gray-900 dark:border-gray-700">
                    <div>
                        <h3 class="mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">تفاصيل الطرد</h3>

                        <div class="space-y-2 text-xs text-gray-600 dark:text-gray-400">
                            <div>
                                <span class="font-semibold">الرمز:</span>
                                <span>{{ $shipment->code }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">نوع الطرد:</span>
                                <span>{{ $shipment->package_type }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">الوزن:</span>
                                <span>{{ $shipment->weight }} كجم</span>
                            </div>
                            <div>
                                <span class="font-semibold">إجمالي المبلغ:</span>
                                <span>{{ $shipment->total_amount }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">الحالة:</span>
                                <span>{{ __('status.' . $shipment->status) ?? $shipment->status }}</span>
                            </div>
                        </div>
                    </div>

                    <button type="button" @click="isDetailsModalOpen = true"
                        class="inline-flex justify-center items-center px-3 py-2 mt-4 text-xs font-medium text-white rounded-lg bg-brand-500 hover:bg-brand-600">
                        تعديل تفاصيل الطرد
                    </button>
                </div>

                {{-- كرت طريقة الدفع --}}
                <div
                    class="flex flex-col justify-between p-4 h-full bg-gray-50 rounded-xl border border-gray-200 dark:bg-gray-900 dark:border-gray-700">
                    <div>
                        <h3 class="mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">طريقة الدفع</h3>

                        <div class="space-y-2 text-xs text-gray-600 dark:text-gray-400">
                            <div>
                                <span class="font-semibold">طريقة الدفع:</span>
                                <span>
                                    @switch($shipment->payment_method)
                                        @case('prepaid')
                                            دفع مقدم
                                        @break

                                        @case('cod')
                                            عند التسليم (COD)
                                        @break

                                        @case('partial_payment')
                                            دفع جزئي
                                        @break

                                        @case('customer_credit')
                                            آجل على حساب العميل
                                        @break

                                        @default
                                            {{ $shipment->payment_method }}
                                    @endswitch
                                </span>
                            </div>

                            @if ($shipment->customer_debt_status)
                                <div>
                                    <span class="font-semibold">حالة المديونية:</span>
                                    <span>{{ $shipment->customer_debt_status }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <button type="button" @click="isPaymentModalOpen = true"
                        class="inline-flex justify-center items-center px-3 py-2 mt-4 text-xs font-medium text-white rounded-lg bg-brand-500 hover:bg-brand-600">
                        تعديل طريقة الدفع
                    </button>
                </div>
            </div>

            {{-- ====================== مودال المرسل والمستلم ====================== --}}
            <div x-show="isSenderReceiverModalOpen"
                x-transition
                class="flex overflow-y-auto fixed inset-0 justify-center items-center p-5 modal z-99999"
                style="display: none;">

                {{-- الخلفية --}}
                <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
                    @click="isSenderReceiverModalOpen = false"></div>

                {{-- الكارد --}}
                <div class="relative w-full max-w-[600px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">

                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-base font-bold text-gray-800 dark:text-gray-100">
                            تعديل بيانات المرسل والمستلم
                        </h2>
                        <button type="button" @click="isSenderReceiverModalOpen = false"
                            class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                            ✕
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-2">

                        {{-- بيانات المرسل --}}
                        <div class="space-y-4"
                            x-data="customerPicker(
                                '{{ route('customers.search') }}',
                                @js([
                                    'id' => old('sender_customer_id', $shipment->sender_customer_id),
                                    'name' => old('sender_name', $shipment->senderCustomer->name ?? $shipment->sender_name),
                                    'phone' => old('sender_phone', $shipment->senderCustomer->phone ?? $shipment->sender_phone),
                                ])
                            )">

                            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">بيانات المرسل</h3>

                            {{-- البحث عن العميل --}}
                            <div class="relative mt-3">
                                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                    بحث عن عميل (اسم أو رقم)
                                </label>

                                <input type="text" x-model="query" @input.debounce.350ms="search()" @focus="open = true"
                                    @keydown.escape="open=false" placeholder="اكتب اسم العميل أو رقمه..."
                                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">

                                {{-- Dropdown --}}
                                <div x-show="open" x-transition
                                    class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700">

                                    <template x-if="loading">
                                        <div class="p-3 text-sm text-gray-500">جاري البحث...</div>
                                    </template>

                                    <template x-if="!loading && results.length === 0 && query.trim().length >= 2">
                                        <div class="p-3 text-sm text-gray-500">
                                            لا توجد نتائج — يمكنك إدخال البيانات يدويًا
                                        </div>
                                    </template>

                                    <template x-for="c in results" :key="c.id">
                                        <button type="button" @click="select(c)"
                                            class="px-4 py-3 w-full text-right hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <div class="text-sm font-semibold text-gray-800 dark:text-white"
                                                x-text="c.name"></div>
                                            <div class="text-xs text-gray-500" x-text="c.phone"></div>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            {{-- sender_branch_code ثابت من المستخدم --}}
                            <div class="mt-3">
                                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                    الجهة \ من
                                </label>
                                <input type="text" value="{{ auth()->user()->branch->name ?? '' }}"
                                    class="px-4 py-2.5 w-full h-11 text-sm bg-gray-100 rounded-lg border dark:text-gray-400 dark:bg-gray-700"
                                    disabled>

                                <input type="hidden" name="sender_branch_code"
                                    value="{{ auth()->user()->branch_code }}">

                                @error('sender_branch_code')
                                    <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- sender_customer_id --}}
                            <input type="hidden" name="sender_customer_id" x-model="selectedId">
                            @error('sender_customer_id')
                                <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                            @enderror

                            {{-- اسم المرسل --}}
                            <div class="mt-3">
                                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                    الاسم
                                </label>
                                <input type="text" name="sender_name" x-model="selectedName" @input="selectedId=''"
                                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                                    placeholder="اسم المرسل">
                                @error('sender_name')
                                    <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- هاتف المرسل --}}
                            <div class="mt-3">
                                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                    الهاتف
                                </label>
                                <input type="text" name="sender_phone" x-model="selectedPhone" @input="selectedId=''"
                                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                                    placeholder="رقم هاتف المرسل">
                                @error('sender_phone')
                                    <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- عدد قروف العسل --}}
                            <div class="mt-3">
                                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                    عدد قروف العسل
                                </label>
                                <input type="number" name="no_honey_jars"
                                    value="{{ old('no_honey_jars', $shipment->no_honey_jars) }}"
                                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                                    placeholder="0">
                                @error('no_honey_jars')
                                    <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- بيانات المستلم --}}
                        <div class="space-y-4"
                            x-data="customerPicker(
                                '{{ route('customers.search') }}',
                                @js([
                                    'id' => old('receiver_customer_id', $shipment->receiver_customer_id),
                                    'name' => old('receiver_name', $shipment->receiverCustomer->name ?? $shipment->receiver_name),
                                    'phone' => old('receiver_phone', $shipment->receiverCustomer->phone ?? $shipment->receiver_phone),
                                ])
                            )">

                            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">بيانات المستلم</h3>

                            {{-- البحث عن المستلم --}}
                            <div class="relative mt-3">
                                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                    بحث عن مستلم (اسم أو رقم)
                                </label>

                                <input type="text" x-model="query" @input.debounce.350ms="search()" @focus="open = true"
                                    @keydown.escape="open=false" placeholder="اكتب اسم المستلم أو رقمه..."
                                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">

                                {{-- Dropdown --}}
                                <div x-show="open" x-transition
                                    class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700">

                                    <template x-if="loading">
                                        <div class="p-3 text-sm text-gray-500">جاري البحث...</div>
                                    </template>

                                    <template x-if="!loading && results.length === 0 && query.trim().length >= 2">
                                        <div class="p-3 text-sm text-gray-500">
                                            لا توجد نتائج — يمكنك إدخال البيانات يدويًا
                                        </div>
                                    </template>

                                    <template x-for="c in results" :key="c.id">
                                        <button type="button" @click="select(c)"
                                            class="px-4 py-3 w-full text-right hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <div class="text-sm font-semibold text-gray-800 dark:text-white"
                                                x-text="c.name"></div>
                                            <div class="text-xs text-gray-500" x-text="c.phone"></div>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            {{-- receiver_customer_id --}}
                            <input type="hidden" name="receiver_customer_id" x-model="selectedId">
                            @error('receiver_customer_id')
                                <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                            @enderror

                            {{-- فرع المستلم --}}
                            <div class="mt-3">
                                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                    الجهة إلى
                                </label>

                                <select name="receiver_branch_code"
                                    class="px-4 py-2.5 w-full h-11 text-sm rounded-lg border dark:text-gray-400 dark:bg-dark-900 dark:border-gray-600"
                                    required>
                                    <option value=""
                                        {{ old('receiver_branch_code', $shipment->receiver_branch_code) ? '' : 'selected' }}
                                        disabled>
                                        اختر الجهة
                                    </option>

                                    @foreach ($branches as $branch)
                                        @continue($branch->code === auth()->user()->branch_code)

                                        <option value="{{ $branch->code }}"
                                            {{ old('receiver_branch_code', $shipment->receiver_branch_code) == $branch->code ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('receiver_branch_code')
                                    <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- اسم المستلم --}}
                            <div class="mt-3">
                                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                    الاسم
                                </label>
                                <input type="text" name="receiver_name" x-model="selectedName" @input="selectedId=''"
                                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                                    placeholder="اسم المستلم">
                                @error('receiver_name')
                                    <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- هاتف المستلم --}}
                            <div class="mt-3">
                                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                    الهاتف
                                </label>
                                <input type="text" name="receiver_phone" x-model="selectedPhone" @input="selectedId=''"
                                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                                    placeholder="رقم هاتف المستلم">
                                @error('receiver_phone')
                                    <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- عدد جوالين العسل --}}
                            <div class="mt-3">
                                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                    عدد جوالين العسل
                                </label>
                                <input type="number" name="no_gallons_honey"
                                    value="{{ old('no_gallons_honey', $shipment->no_gallons_honey) }}"
                                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                                    placeholder="0">
                                @error('no_gallons_honey')
                                    <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" @click="isSenderReceiverModalOpen = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white">
                            إغلاق
                        </button>
                    </div>
                </div>
            </div>

            {{-- ====================== مودال تفاصيل الطرد ====================== --}}
            <div x-show="isDetailsModalOpen"
                x-transition
                class="flex overflow-y-auto fixed inset-0 justify-center items-center p-5 modal z-99999"
                style="display: none;">

                <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
                    @click="isDetailsModalOpen = false"></div>

                <div class="relative w-full max-w-[600px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">

                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-base font-bold text-gray-800 dark:text-gray-100">
                            تعديل تفاصيل الطرد
                        </h2>
                        <button type="button" @click="isDetailsModalOpen = false"
                            class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                            ✕
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-4 w-full xl:grid-cols-2">

                        {{-- الرمز --}}
                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الرمز</label>
                            <input type="text" name="code" value="{{ old('code', $shipment->code) }}"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                                placeholder="اكتب الرمز">
                            @error('code')
                                <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- رقم السند (للعرض فقط) --}}
                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">رقم
                                السند</label>
                            <input type="text" value="{{ $shipment->bond_number }}"
                                class="px-4 py-2.5 w-full h-11 text-sm bg-gray-100 rounded-lg border dark:text-gray-400 dark:bg-gray-700"
                                disabled>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">لا يمكن تعديل رقم السند بعد الإنشاء</p>
                        </div>

                        {{-- نوع الطرد --}}
                        <div class="mt-3 xl:col-span-2">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">نوع
                                الطرد</label>
                            <input type="text" name="package_type"
                                value="{{ old('package_type', $shipment->package_type) }}"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                                placeholder="مثال: كرتون / شنطة / ...">
                            @error('package_type')
                                <div class="mt-1 textsm text-error-600">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- الوزن --}}
                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الوزن
                                (كجم)</label>
                            <input type="number" name="weight" value="{{ old('weight', $shipment->weight) }}"
                                step="0.01" min="0"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                                placeholder="0.00">
                            @error('weight')
                                <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- إجمالي المبلغ --}}
                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm text-gray-700 font.medium dark:text-gray-400">إجمالي
                                المبلغ</label>
                            <input type="number" name="total_amount"
                                value="{{ old('total_amount', $shipment->total_amount) }}" step="0.01" min="0"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                                placeholder="0.00">
                            @error('total_amount')
                                <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- حالة الطلب --}}
                        <div class="mt-3 xl:col-span-2">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                حالة الطلب
                            </label>
                            <select name="status"
                                class="px-4 py-2.5 w-full h-11 text-sm rounded-lg border dark:text-gray-400 dark:bg-dark-900 dark:border-gray-600">
                                <option value="pending" @selected(old('status', $shipment->status) == 'pending')>قيد الانتظار
                                </option>
                                <option value="in_transit" @selected(old('status', $shipment->status) == 'in_transit')>قيد
                                    الشحن</option>
                                <option value="delivered" @selected(old('status', $shipment->status) == 'delivered')>تم
                                    التسليم</option>
                            </select>
                            @error('status')
                                <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" @click="isDetailsModalOpen = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white">
                            إغلاق
                        </button>
                    </div>
                </div>
            </div>

            {{-- ====================== مودال طريقة الدفع ====================== --}}
            <div x-show="isPaymentModalOpen"
                x-transition
                class="flex overflow-y-auto fixed inset-0 justify-center items-center p-5 modal z-99999"
                style="display: none;">

                <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
                    @click="isPaymentModalOpen = false"></div>

                <div
                    class="relative w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-base font-bold text-gray-800 dark:text-gray-100">
                            تعديل طريقة الدفع
                        </h2>
                        <button type="button" @click="isPaymentModalOpen = false"
                            class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                            ✕
                        </button>
                    </div>

                    <div class="mt-2 md:col-span-2">
                        <h3 class="my-6 mb-3 text-sm font-bold text-gray-700 dark:text-gray-400">طريقة الدفع</h3>

                        <div class="flex flex-col gap-4">

                            {{-- الحالة العامة --}}
                            <div class="flex flex-wrap gap-6">

                                <label
                                    class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                    <input class="sr-only" type="radio" name="payment_method" value="prepaid"
                                        @change="payment_method='prepaid'"
                                        {{ old('payment_method', $shipment->payment_method) == 'prepaid' ? 'checked' : '' }}>
                                    <span
                                        :class="payment_method === 'prepaid' ? 'border-brand-500 bg-brand-500' :
                                            'bg-transparent border-gray-300 dark:border-gray-700'"
                                        class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                        <span :class="payment_method === 'prepaid' ? 'block' : 'hidden'"
                                            class="w-2 h-2 bg-white rounded-full"></span>
                                    </span>
                                    دفع مقدم
                                </label>

                                <label
                                    class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                    <input class="sr-only" type="radio" name="payment_method" value="cod"
                                        @change="payment_method='cod'"
                                        {{ old('payment_method', $shipment->payment_method) == 'cod' ? 'checked' : '' }}>
                                    <span
                                        :class="payment_method === 'cod' ? 'border-brand-500 bg-brand-500' :
                                            'bg-transparent border-gray-300 dark:border-gray-700'"
                                        class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                        <span :class="payment_method === 'cod' ? 'block' : 'hidden'"
                                            class="w-2 h-2 bg-white rounded-full"></span>
                                    </span>
                                    دفع عند التسليم (COD)
                                </label>

                                <label
                                    class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                    <input class="sr-only" type="radio" name="payment_method" value="partial_payment"
                                        @change="payment_method='partial_payment'"
                                        {{ old('payment_method', $shipment->payment_method) == 'partial_payment' ? 'checked' : '' }}>
                                    <span
                                        :class="payment_method === 'partial_payment' ? 'border-brand-500 bg-brand-500' :
                                            'bg-transparent border-gray-300 dark:border-gray-700'"
                                        class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                        <span :class="payment_method === 'partial_payment' ? 'block' : 'hidden'"
                                            class="w-2 h-2 bg-white rounded-full"></span>
                                    </span>
                                    دفع جزئي (المرسل يدفع جزء)
                                </label>

                                <label
                                    class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                    <input class="sr-only" type="radio" name="payment_method" value="customer_credit"
                                        @change="payment_method='customer_credit'"
                                        {{ old('payment_method', $shipment->payment_method) == 'customer_credit' ? 'checked' : '' }}>
                                    <span
                                        :class="payment_method === 'customer_credit' ? 'border-brand-500 bg-brand-500' :
                                            'bg-transparent border-gray-300 dark:border-gray-700'"
                                        class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                        <span :class="payment_method === 'customer_credit' ? 'block' : 'hidden'"
                                            class="w-2 h-2 bg-white rounded-full"></span>
                                    </span>
                                    آجل على حساب العميل
                                </label>
                            </div>

                            @error('payment_method')
                                <div class="text-sm text-error-600">{{ $message }}</div>
                            @enderror

                            {{-- prepaid --}}
                            <div class="p-4 mt-2 rounded-xl border border-gray-200 dark:border-gray-700"
                                x-show="payment_method === 'prepaid'" x-transition>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">
                                    طريقة الدفع (للدفع المقدم)
                                </label>

                                <div class="flex flex-wrap gap-6">

                                    <label
                                        class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                        <input class="sr-only" type="radio" name="prepaid_payment_method" value="cash"
                                            x-model="prepaid_method">
                                        <span
                                            :class="prepaid_method === 'cash' ? 'border-brand-500 bg-brand-500' :
                                                'bg-transparent border-gray-300 dark:border-gray-700'"
                                            class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                            <span :class="prepaid_method === 'cash' ? 'block' : 'hidden'"
                                                class="w-2 h-2 bg-white rounded-full"></span>
                                        </span>
                                        كاش
                                    </label>

                                    <label
                                        class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                        <input class="sr-only" type="radio" name="prepaid_payment_method"
                                            value="bank_transfer" x-model="prepaid_method">
                                        <span
                                            :class="prepaid_method === 'bank_transfer' ? 'border-brand-500 bg-brand-500' :
                                                'bg-transparent border-gray-300 dark:border-gray-700'"
                                            class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                            <span :class="prepaid_method === 'bank_transfer' ? 'block' : 'hidden'"
                                                class="w-2 h-2 bg-white rounded-full"></span>
                                        </span>
                                        تحويل بنكي
                                    </label>
                                </div>

                                @error('prepaid_payment_method')
                                    <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                                @enderror

                                <div class="mt-4" x-show="prepaid_method === 'bank_transfer'" x-transition>
                                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                        رفع سند التحويل
                                    </label>

                                    <label for="prepaid_attachment"
                                        class="cursor-pointer flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-6
                                            dark:border-gray-600 dark:bg-gray-800
                                            hover:border-brand-500 dark:hover:border-brand-500
                                            transition-colors.duration-200 w-full text-center
                                            @error('prepaid_attachment') border-error-500 @enderror">

                                        <div class="mb-[22px] flex.justify-center">
                                            <div
                                                class="flex h-[68px] w-[68px] items-center justify-center rounded-full bg-gray-200 text-gray-700
                                                     dark:bg-gray-700 dark:text-gray-400">
                                                <svg class="fill-current" width="29" height="28"
                                                    viewBox="0 0 29 28" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M14.5019 3.91699C14.2852 3.91699 14.0899 4.00891 13.953 4.15589L8.57363 9.53186C8.28065 9.82466 8.2805 10.2995 8.5733 10.5925C8.8661 10.8855 9.34097 10.8857 9.63396 10.5929L13.7519 6.47752V18.667C13.7519 19.0812 14.0877 19.417 14.5019 19.417C14.9161 19.417 15.2519 19.0812 15.2519 18.667V6.48234L19.3653 10.5929C19.6583 10.8857 20.1332 10.8855 20.426 10.5925C20.7188 10.2995 20.7186 9.82463 20.4256 9.53184L15.0838 4.19378C14.9463 4.02488 14.7367 3.91699 14.5019 3.91699ZM5.91626 18.667C5.91626 18.2528 5.58047 17.917 5.16626 17.917C4.75205 17.917 4.41626 18.2528 4.41626 18.667V21.8337C4.41626 23.0763 5.42362 24.0837 6.66626 24.0837H22.3339C23.5766 24.0837 24.5839 23.0763 24.5839 21.8337V18.667C24.5839 18.2528 24.2482 17.917 23.8339 17.917C23.4197 17.917 23.0839 18.2528 23.0839 18.667V21.8337C23.0839 22.2479 22.7482 22.5837 22.3339 22.5837H6.66626C6.25205 22.5837 5.91626 22.2479 5.91626 21.8337V18.667Z" />
                                                </svg>
                                            </div>
                                        </div>

                                        <h4 class="mb-2 font-semibold text-gray-800 text-theme-xl dark:text-white/90">
                                            Drop File Here
                                        </h4>

                                        <span class="block mb-4 text-sm text-gray-700 dark:text-gray-400">
                                            اسحب الملف هنا أو اضغط للاختيار<br>
                                            (PNG, JPG, PDF)
                                        </span>

                                        <span class="font-medium underline text-theme-sm text-brand-500">
                                            Browse File
                                        </span>

                                        <input id="prepaid_attachment" type="file" name="prepaid_attachment"
                                            accept="image/*,.pdf" class="hidden" />
                                    </label>

                                    @error('prepaid_attachment')
                                        <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- COD --}}
                            <div class="p-4 mt-2 border-gray-200 rounded-xl.border dark:border-gray-700"
                                x-show="payment_method === 'cod'" x-transition>
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    سيتم اعتبار مبلغ التحصيل عند التسليم =
                                    <span class="font-semibold">إجمالي المبلغ</span>.
                                </div>
                            </div>

                            {{-- partial_payment --}}
                            <div class="p-4 mt-2 rounded-xl border border-gray-200 dark:border-gray-700"
                                x-show="payment_method==='partial_payment'" x-transition>

                                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                    المبلغ المدفوع من المرسل الآن
                                </label>

                                <input type="number" name="partial_amount" value="{{ old('partial_amount') }}"
                                    min="0.01" step="0.01" placeholder="0.00"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-400 rounded-lg
                                          bg-white dark:bg-gray-700 text-gray-700 dark:text-white
                                          focus:ring-2 focus:ring-brand-500 focus:border-brand-500
                                          @error('partial_amount') border-error-500 @enderror">

                                @error('partial_amount')
                                    <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                                @enderror

                                <div class="mt-4">
                                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">
                                        طريقة الدفع (للدفع الجزئي)
                                    </label>

                                    <div class="flex flex-wrap gap-6">

                                        <label
                                            class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                            <input class="sr-only" type="radio" name="prepaid_payment_method"
                                                value="cash" x-model="prepaid_method">
                                            <span
                                                :class="prepaid_method === 'cash' ? 'border-brand-500 bg-brand-500' :
                                                    'bg-transparent border-gray-300 dark:border-gray-700'"
                                                class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                                <span :class="prepaid_method === 'cash' ? 'block' : 'hidden'"
                                                    class="w-2 h-2 bg-white rounded-full"></span>
                                            </span>
                                            كاش
                                        </label>

                                        <label
                                            class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                            <input class="sr-only" type="radio" name="prepaid_payment_method"
                                                value="bank_transfer" x-model="prepaid_method">
                                            <span
                                                :class="prepaid_method === 'bank_transfer' ? 'border-brand-500 bg-brand-500' :
                                                    'bg-transparent border-gray-300 dark:border-gray-700'"
                                                class="flex h-5 w-5 items-center justify-center rounded-full.border-[1.25px]">
                                                <span :class="prepaid_method === 'bank_transfer' ? 'block' : 'hidden'"
                                                    class="w-2 h-2 bg-white rounded-full"></span>
                                            </span>
                                            تحويل بنكي
                                        </label>

                                    </div>

                                    <div class="mt-4" x-show="prepaid_method === 'bank_transfer'" x-transition>
                                        <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                                            رفع سند التحويل
                                        </label>

                                        <label for="prepaid_attachment_partial"
                                            class="cursor-pointer flex flex.col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-6
                                                dark:border-gray-600.dark:bg-gray-800
                                                hover:border-brand-500 dark:hover:border-brand-500
                                                transition-colors duration-200 w-full.text-center
                                                @error('prepaid_attachment') border-error-500 @enderror">

                                            <div class="mb-[22px] flex justify-center">
                                                <div
                                                    class="flex h-[68px] w-[68px] items-center justify-center rounded-full bg-gray-200 text-gray-700
                                                         dark:bg-gray-700 dark:text-gray-400">
                                                    <svg class="fill-current" width="29" height="28"
                                                        viewBox="0 0 29 28" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M14.5019 3.91699C14.2852 3.91699 14.0899 4.00891 13.953 4.15589L8.57363 9.53186C8.28065 9.82466 8.2805 10.2995 8.5733 10.5925C8.8661 10.8855 9.34097 10.8857 9.63396 10.5929L13.7519 6.47752V18.667C13.7519 19.0812 14.0877 19.417 14.5019 19.417C14.9161 19.417 15.2519 19.0812 15.2519 18.667V6.48234L19.3653 10.5929C19.6583 10.8857 20.1332 10.8855 20.426 10.5925C20.7188 10.2995 20.7186 9.82463 20.4256 9.53184L15.0838 4.19378C14.9463 4.02488 14.7367 3.91699 14.5019 3.91699ZM5.91626 18.667C5.91626 18.2528 5.58047 17.917 5.16626 17.917C4.75205 17.917 4.41626 18.2528 4.41626 18.667V21.8337C4.41626 23.0763 5.42362 24.0837 6.66626 24.0837H22.3339C23.5766 24.0837 24.5839 23.0763 24.5839 21.8337V18.667C24.5839 18.2528 24.2482 17.917 23.8339 17.917C23.4197 17.917 23.0839 18.2528 23.0839 18.667V21.8337C23.0839 22.2479 22.7482 22.5837 22.3339 22.5837H6.66626C6.25205 22.5837 5.91626 22.2479 5.91626 21.8337V18.667Z" />
                                                    </svg>
                                                </div>
                                            </div>

                                            <h4
                                                class="mb-2 font-semibold text-gray-800 text-theme-xl dark:text-white/90">
                                                Drop File Here
                                            </h4>

                                            <span class="block mb-4 text-sm text-gray-700 dark:text-gray-400">
                                                اسحب الملف هنا أو اضغط للاختيار<br>
                                                (PNG, JPG, PDF)
                                            </span>

                                            <span class="font-medium underline text-theme-sm text-brand-500">
                                                Browse File
                                            </span>

                                            <input id="prepaid_attachment_partial" type="file"
                                                name="prepaid_attachment" accept="image/*,.pdf" class="hidden" />
                                        </label>

                                        @error('prepaid_attachment')
                                            <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- customer_credit --}}
                            <div class="p-4 mt-2 rounded-xl border border-gray-200 dark:border-gray-700"
                                x-show="payment_method==='customer_credit'" x-transition>
                                <label class="block mb-1.5 text-sm font-medium text-gray-700.dark:text-gray-400">
                                    حالة مديونية العميل
                                </label>
                                <select name="customer_debt_status"
                                    class="px-4 py-2.5 w-full h-11 text-sm rounded-lg border dark:text-gray-400 dark:bg-dark-900 dark:border-gray-600">
                                    <option value="pending"
                                        @selected(old('customer_debt_status', $shipment->customer_debt_status) == 'pending')>
                                        قيد الانتظار</option>
                                    <option value="partially_paid"
                                        @selected(old('customer_debt_status', $shipment->customer_debt_status) == 'partially_paid')>
                                        مدفوع جزئياً</option>
                                    <option value="fully_paid"
                                        @selected(old('customer_debt_status', $shipment->customer_debt_status) == 'fully_paid')>
                                        مدفوع بالكامل</option>
                                    <option value="overdue"
                                        @selected(old('customer_debt_status', $shipment->customer_debt_status) == 'overdue')>
                                        مديون</option>
                                </select>

                                @error('customer_debt_status')
                                    <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" @click="isPaymentModalOpen = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white">
                            إغلاق
                        </button>
                    </div>
                </div>
            </div>

            {{-- الملاحظات --}}
            <div class="mt-6">
                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الملاحظات</label>
                <textarea placeholder="اكتب ملاحظاتك..." rows="4" name="notes"
                    class="px-4 py-2.5 w-full h-auto text-sm text-gray-800 bg-transparent border-gray-300 resize-none rounded-lg.border hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">{{ old('notes', $shipment->notes) }}</textarea>
                @error('notes')
                    <div class="mt-1 text-sm text-error-600">{{ $message }}</div>
                @enderror
            </div>

            {{-- الأزرار --}}
            <div class="flex gap-3 mt-6">
                <button type="submit"
                    class="px-4 py-2 w-full font-medium text-white rounded-lg bg-brand-500 hover:bg-brand-600 md:w-auto">
                    تحديث الطرد
                </button>

                <a href="{{ route('request.index') }}"
                    class="px-4 py-2 w-full font-medium text-center text-gray-800 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white md:w-auto">
                    رجوع للقائمة
                </a>
            </div>

        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('customerPicker', (url, initial = null) => ({
                query: '',
                open: false,
                loading: false,
                results: [],

                selectedId: '',
                selectedName: '',
                selectedPhone: '',

                init() {
                    if (initial) {
                        this.selectedId = initial.id ?? '';
                        this.selectedName = initial.name ?? '';
                        this.selectedPhone = initial.phone ?? '';
                        this.query = this.selectedName;
                    }
                },

                async search() {
                    const q = (this.query || '').trim();
                    this.open = true;

                    if (q.length < 2) {
                        this.results = [];
                        this.loading = false;
                        return;
                    }

                    this.loading = true;

                    try {
                        const res = await fetch(`${url}?q=${encodeURIComponent(q)}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!res.ok) throw new Error('Search failed');
                        this.results = await res.json();
                    } catch (e) {
                        console.error(e);
                        this.results = [];
                    } finally {
                        this.loading = false;
                    }
                },

                select(c) {
                    this.selectedId = c.id;
                    this.selectedName = c.name ?? '';
                    this.selectedPhone = c.phone ?? '';
                    this.query = this.selectedName;
                    this.open = false;
                    this.results = [];
                }
            }));
        });
    </script>

@endsection
