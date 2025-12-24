@extends('layouts.app')
@section('title', 'تعديل طرد')
@section('Breadcrumb', 'تعديل طرد')
@section('content')

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6" x-data="{ payment_method: '{{ old('payment_method', $shipment->payment_method) }}' }">

        <form action="{{ route('request.update', $shipment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- الشبكة الرئيسية -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6">

                <!-- بيانات المرسل -->
                <div class="space-y-4" x-data="customerPicker('{{ route('customers.search') }}')">

                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">بيانات المرسل</h3>

                    <!-- البحث عن العميل -->
                    <div class="mt-3 relative">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            بحث عن عميل (اسم أو رقم)
                        </label>

                        <input type="text" x-model="query" @input.debounce.350ms="search()" @focus="open = true"
                            @keydown.escape="open=false" placeholder="اكتب اسم العميل أو رقمه..."
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">

                        <!-- Dropdown -->
                        <div x-show="open" x-transition
                            class="absolute z-50 mt-2 w-full rounded-xl border border-gray-200 bg-white shadow-lg dark:bg-gray-800 dark:border-gray-700 overflow-hidden">

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
                                    class="w-full text-right px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="text-sm font-semibold text-gray-800 dark:text-white" x-text="c.name"></div>
                                    <div class="text-xs text-gray-500" x-text="c.phone"></div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- sender_branch_code: يعرض فرع المستخدم + يخزن hidden -->
                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الجهة \ من</label>
                        <input type="text" value="{{ auth()->user()->branch->name ?? '' }}"
                            class="h-11 w-full rounded-lg border px-4 py-2.5 text-sm bg-gray-100 dark:text-gray-400 dark:bg-gray-700"
                            disabled>

                        <input type="hidden" name="sender_branch_code" value="{{ auth()->user()->branch_code }}">


                        @error('sender_branch_code')
                            <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- نخزن ID العميل الحقيقي -->
                    <input type="hidden" name="sender_customer_id"
                        value="{{ old('sender_customer_id', $shipment->sender_customer_id) }}" x-model="selectedId">
                    @error('sender_customer_id')
                        <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                    @enderror

                    <!-- اسم العميل -->
                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الاسم</label>
                        <input type="text" name="sender_name" value="{{ old('sender_name', $shipment->sender_name) }}"
                            x-model="selectedName" @input="selectedId=''"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="اسم المرسل">
                        @error('sender_name')
                            <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- هاتف العميل -->
                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف</label>
                        <input type="text" name="sender_phone"
                            value="{{ old('sender_phone', $shipment->sender_phone) }}" x-model="selectedPhone"
                            @input="selectedId=''"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="رقم هاتف المرسل">
                        @error('sender_phone')
                            <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- عدد قروف العسل -->
                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">عدد قروف
                            العسل</label>
                        <input type="number" name="no_honey_jars"
                            value="{{ old('no_honey_jars', $shipment->no_honey_jars) }}"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="0">
                        @error('no_honey_jars')
                            <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <!-- بيانات المستلم -->
                <div class="space-y-4" x-data="customerPicker('{{ route('customers.search') }}')">

                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">بيانات المستلم</h3>

                    <!-- البحث عن العميل -->
                    <div class="mt-3 relative">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            بحث عن مستلم (اسم أو رقم)
                        </label>

                        <input type="text" x-model="query" @input.debounce.350ms="search()" @focus="open = true"
                            @keydown.escape="open=false" placeholder="اكتب اسم المستلم أو رقمه..."
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">

                        <!-- Dropdown -->
                        <div x-show="open" x-transition
                            class="absolute z-50 mt-2 w-full rounded-xl border border-gray-200 bg-white shadow-lg dark:bg-gray-800 dark:border-gray-700 overflow-hidden">

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
                                    class="w-full text-right px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="text-sm font-semibold text-gray-800 dark:text-white" x-text="c.name"></div>
                                    <div class="text-xs text-gray-500" x-text="c.phone"></div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- نخزن ID العميل الحقيقي -->
                    <input type="hidden" name="receiver_customer_id"
                        value="{{ old('receiver_customer_id', $shipment->receiver_customer_id) }}" x-model="selectedId">
                    @error('receiver_customer_id')
                        <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                    @enderror

                    <!-- فرع المستلم -->
                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            الجهة إلى
                        </label>

                        <select name="receiver_branch_code"
                            class="h-11 w-full rounded-lg border px-4 py-2.5 text-sm
                                   dark:text-gray-400 dark:bg-dark-900 dark:border-gray-600"
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
                            <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- اسم المستلم -->
                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الاسم</label>
                        <input type="text" name="receiver_name"
                            value="{{ old('receiver_name', $shipment->receiver_name) }}" x-model="selectedName"
                            @input="selectedId=''"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="اسم المستلم">
                        @error('receiver_name')
                            <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- هاتف المستلم -->
                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف</label>
                        <input type="text" name="receiver_phone"
                            value="{{ old('receiver_phone', $shipment->receiver_phone) }}" x-model="selectedPhone"
                            @input="selectedId=''"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="رقم هاتف المستلم">
                        @error('receiver_phone')
                            <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- عدد جوالين العسل -->
                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">عدد جوالين
                            العسل</label>
                        <input type="number" name="no_gallons_honey"
                            value="{{ old('no_gallons_honey', $shipment->no_gallons_honey) }}"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="0">
                        @error('no_gallons_honey')
                            <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- حقول عامة -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 w-full mt-6">

                <div class="mt-3">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الرمز</label>
                    <input type="text" name="code" value="{{ old('code', $shipment->code) }}"
                        class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                        placeholder="اكتب الرمز">
                    @error('code')
                        <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-3">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">رقم السند</label>
                    <input type="text" value="{{ $shipment->bond_number }}"
                        class="h-11 w-full rounded-lg border px-4 py-2.5 text-sm bg-gray-100 dark:text-gray-400 dark:bg-gray-700"
                        disabled>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">لا يمكن تعديل رقم السند بعد الإنشاء</p>
                </div>
            </div>

            <!-- تفاصيل الطرد -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6 mt-6">
                <div class="space-y-4 w-full md:col-span-2">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">تفاصيل الطرد</h3>

                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">نوع الطرد</label>
                        <input type="text" name="package_type"
                            value="{{ old('package_type', $shipment->package_type) }}"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="مثال: كرتون / شنطة / ...">
                        @error('package_type')
                            <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الوزن
                            (كجم)</label>
                        <input type="number" name="weight" value="{{ old('weight', $shipment->weight) }}"
                            step="0.01" min="0"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="0.00">
                        @error('weight')
                            <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">إجمالي
                            المبلغ</label>
                        <input type="number" name="total_amount"
                            value="{{ old('total_amount', $shipment->total_amount) }}" step="0.01" min="0"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="0.00">
                        @error('total_amount')
                            <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">حالة الطلب</label>
                        <select name="status"
                            class="h-11 w-full rounded-lg border px-4 py-2.5 text-sm dark:text-gray-400 dark:bg-dark-900 dark:border-gray-600">
                            <option value="pending" @selected(old('status', $shipment->status) == 'pending')>قيد الانتظار</option>
                            <option value="in_transit" @selected(old('status', $shipment->status) == 'in_transit')>قيد الشحن</option>
                            <option value="delivered" @selected(old('status', $shipment->status) == 'delivered')>تم التسليم</option>
                        </select>
                        @error('status')
                            <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <!-- طريقة الدفع -->
                <div class="md:col-span-2 mt-2">
                    <h3 class="text-sm font-bold mb-3 text-gray-700 dark:text-gray-400 my-6">طريقة الدفع</h3>

                    <div class="flex flex-col gap-4">

                        <div class="flex gap-6">
                            <label class="relative flex cursor-pointer items-center gap-3 text-sm font-medium select-none">
                                <input class="sr-only" type="radio" name="payment_method" value="prepaid"
                                    @change="payment_method='prepaid'"
                                    {{ old('payment_method', $shipment->payment_method) == 'prepaid' ? 'checked' : '' }}>
                                <span
                                    :class="payment_method === 'prepaid' ? 'border-brand-500 bg-brand-500' :
                                        'bg-transparent border-gray-300 dark:border-gray-700'"
                                    class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                    <span :class="payment_method === 'prepaid' ? 'block' : 'hidden'"
                                        class="h-2 w-2 rounded-full bg-white"></span>
                                </span>
                                دفع مقدم
                            </label>

                            <label class="relative flex cursor-pointer items-center gap-3 text-sm font-medium select-none">
                                <input class="sr-only" type="radio" name="payment_method" value="cod"
                                    @change="payment_method='cod'"
                                    {{ old('payment_method', $shipment->payment_method) == 'cod' ? 'checked' : '' }}>
                                <span
                                    :class="payment_method === 'cod' ? 'border-brand-500 bg-brand-500' :
                                        'bg-transparent border-gray-300 dark:border-gray-700'"
                                    class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                    <span :class="payment_method === 'cod' ? 'block' : 'hidden'"
                                        class="h-2 w-2 rounded-full bg-white"></span>
                                </span>
                                دفع عند التسليم (آجل)
                            </label>

                            <label class="relative flex cursor-pointer items-center gap-3 text-sm font-medium select-none">
                                <input class="sr-only" type="radio" name="payment_method" value="customer_credit"
                                    @change="payment_method='customer_credit'"
                                    {{ old('payment_method', $shipment->payment_method) == 'customer_credit' ? 'checked' : '' }}>
                                <span
                                    :class="payment_method === 'customer_credit' ? 'border-brand-500 bg-brand-500' :
                                        'bg-transparent border-gray-300 dark:border-gray-700'"
                                    class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                    <span :class="payment_method === 'customer_credit' ? 'block' : 'hidden'"
                                        class="h-2 w-2 rounded-full bg-white"></span>
                                </span>
                                رصيد عميل
                            </label>
                        </div>

                        @error('payment_method')
                            <div class="text-sm text-error-600">{{ $message }}</div>
                        @enderror

                        <div class="mt-2">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                حالة مديونية العميل
                            </label>
                            <select name="customer_debt_status"
                                class="h-11 w-full rounded-lg border px-4 py-2.5 text-sm dark:text-gray-400 dark:bg-dark-900 dark:border-gray-600">
                                <option value="">اختياري</option>
                                <option value="pending" @selected(old('customer_debt_status', $shipment->customer_debt_status) == 'pending')>قيد الانتظار</option>
                                <option value="partially_paid" @selected(old('customer_debt_status', $shipment->customer_debt_status) == 'partially_paid')>مديون جزئياً</option>
                                <option value="fully_paid" @selected(old('customer_debt_status', $shipment->customer_debt_status) == 'fully_paid')>تم السداد</option>
                                <option value="overdue" @selected(old('customer_debt_status', $shipment->customer_debt_status) == 'overdue')>متأخر</option>
                            </select>
                            @error('customer_debt_status')
                                <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-2" x-show="payment_method==='cod'" x-transition>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                المبلغ (تحصيل عند التسليم)
                            </label>

                            <input type="number" name="cod_amount"
                                value="{{ old('cod_amount', $shipment->cod_amount) }}" min="0" step="0.01"
                                placeholder="0.00"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-400 rounded-lg
                                       bg-white dark:bg-gray-700 text-gray-700 dark:text-white
                                       focus:ring-2 focus:ring-brand-500 focus:border-brand-500
                                       @error('cod_amount') border-error-500 focus:ring-error-500 focus:border-error-500 @enderror">

                            @error('cod_amount')
                                <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                            @enderror

                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                أدخل المبلغ المطلوب تحصيله من العميل عند التسليم
                            </p>
                        </div>

                    </div>
                </div>
            </div>

            <!-- الملاحظات -->
            <div class="mt-6">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الملاحظات</label>
                <textarea placeholder="اكتب ملاحظاتك..." rows="4" name="notes"
                    class="hover:border-brand-500 dark:bg-dark-900 h-auto w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs resize-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">{{ old('notes', $shipment->notes) }}</textarea>
                @error('notes')
                    <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- زر التحديث -->
            <div class="mt-6 flex gap-3">
                <button type="submit"
                    class="bg-brand-500 hover:bg-brand-600 text-white font-medium py-2 px-4 rounded-lg w-full md:w-auto">
                    تحديث الطرد
                </button>

                <a href="{{ route('request.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg w-full md:w-auto text-center">
                    رجوع للقائمة
                </a>
            </div>

        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('customerPicker', (url) => ({
                query: '',
                open: false,
                loading: false,
                results: [],

                // قيم مبدئية من الشحنة/old
                selectedId: '',
                selectedName: '',
                selectedPhone: '',

                init() {
                    // تعبئة القيم الأولية في كل بلوك على حسب الحقول الموجودة بالـ DOM
                    // (مفيد لو تبغى تزامن البحث مع القيم الحالية)
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
