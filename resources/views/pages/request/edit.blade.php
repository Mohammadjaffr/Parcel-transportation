@extends('layouts.app')
@section('title', 'تعديل طرد')
@section('Breadcrumb', 'تعديل طرد')
@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">

        <form action="{{ route('request.update', $shipment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- الشبكة الرئيسية -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6">

                <!-- بيانات المرسل -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">بيانات المرسل</h3>

                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الاسم</label>
                        <input type="text" name="sender_name" value="{{ old('sender_name', $shipment->sender_name) }}"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="اسم المرسل">
                        <div class="text-sm text-error-600 mt-1">
                            @error('sender_name')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف</label>
                        <input type="text" name="sender_phone" value="{{ old('sender_phone', $shipment->sender_phone) }}"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="رقم الهاتف">
                        <div class="text-sm text-error-600 mt-1">
                            @error('sender_phone')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    {{-- <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الجهه\من</label>
                        <input type="text" name="from_city" value="{{ old('from_city', $shipment->from_city) }}"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="الجهه\من">
                        <div class="text-sm text-error-600 mt-1">
                            @error('from_city')
                                {{ $message }}
                            @enderror
                        </div>
                    </div> --}}
                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الجهه\من</label>

                        <select id="from_city" name="from_city" class="h-11 w-full rounded-lg border px-4 py-2.5 text-sm">
                            <option disabled selected>اختر الجهة</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->name }}"
                                    {{ old('from_city', $shipment->from_city) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}</option>
                            @endforeach
                        </select>

                        @error('from_city')
                            <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <!-- بيانات المستلم -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">بيانات المستلم</h3>

                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الاسم</label>
                        <input type="text" name="receiver_name"
                            value="{{ old('receiver_name', $shipment->receiver_name) }}"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="اسم المستلم">
                        <div class="text-sm text-error-600 mt-1">
                            @error('receiver_name')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف</label>
                        <input type="text" name="receiver_phone"
                            value="{{ old('receiver_phone', $shipment->receiver_phone) }}"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="رقم الهاتف">
                        <div class="text-sm text-error-600 mt-1">
                            @error('receiver_phone')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                    {{-- 
                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الجهه\الى</label>
                        <input type="text" name="to_city" value="{{ old('to_city', $shipment->to_city) }}"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="الجهه\الى">
                        <div class="text-sm text-error-600 mt-1">
                            @error('to_city')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                     --}}
                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الجهه\الى</label>

                        <select id="to_city" name="to_city" class="h-11 w-full rounded-lg border px-4 py-2.5 text-sm">
                            <option disabled selected>اختر الجهة</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->name }}"
                                    {{ old('to_city', $shipment->to_city) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}</option>
                            @endforeach
                        </select>

                        @error('to_city')
                            <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- <!-- الفرع -->
            <div class="mt-6">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الفرع</label>
                <select name="branch_id"
                    class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:gray-400">
                    <option value="">اختر الفرع</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}"
                            {{ old('branch_id', $shipment->branch_id) == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach

                </select>
                <div class="text-sm text-error-600 mt-1">
                    @error('branch_id')
                        {{ $message }}
                    @enderror
                </div>
            </div> --}}

            <!-- تفاصيل الطرد وطريقة الدفع -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6 mt-6">
                <!-- نوع الطرد -->
                <div class="space-y-4 w-full md:col-span-2">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">تفاصيل الطرد</h3>
                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">نوع الطرد</label>
                        <input type="text" name="package_type"
                            value="{{ old('package_type', $shipment->package_type) }}"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="نوع الطرد">
                        <div class="text-sm text-error-600 mt-1">
                            @error('package_type')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- طريقة الدفع -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-sm font-bold mb-3 text-gray-700 dark:text-gray-400 my-6">طريقة الدفع</h3>
                    <div class="flex gap-6">
                        <div x-data="{ payment: '{{ old('payment_method', $shipment->payment_method) }}' }" class="flex gap-6">
                            <!-- دفع مقدم -->
                            <label
                                :class="payment === 'prepaid' ? 'text-gray-700 dark:text-gray-400' :
                                    'text-gray-500 dark:text-gray-400'"
                                class="relative flex cursor-pointer items-center gap-3 text-sm font-medium select-none">
                                <input class="sr-only" type="radio" name="payment_method" value="prepaid"
                                    :checked="payment === 'prepaid'" @change="payment = 'prepaid'">
                                <span
                                    :class="payment === 'prepaid' ? 'border-brand-500 bg-brand-500' :
                                        'bg-transparent border-gray-300 dark:border-gray-700'"
                                    class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                    <span :class="payment === 'prepaid' ? 'block' : 'hidden'"
                                        class="h-2 w-2 rounded-full bg-white"></span>
                                </span>
                                دفع مقدم
                            </label>

                            <!-- دفع عند التسليم -->
                            <label
                                :class="payment === 'cod' ? 'text-gray-700 dark:text-gray-400' :
                                    'text-gray-500 dark:text-gray-400'"
                                class="relative flex cursor-pointer items-center gap-3 text-sm font-medium select-none">
                                <input class="sr-only" type="radio" name="payment_method" value="cod"
                                    :checked="payment === 'cod'" @change="payment = 'cod'">
                                <span
                                    :class="payment === 'cod' ? 'border-brand-500 bg-brand-500' :
                                        'bg-transparent border-gray-300 dark:border-gray-700'"
                                    class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                    <span :class="payment === 'cod' ? 'block' : 'hidden'"
                                        class="h-2 w-2 rounded-full bg-white"></span>
                                </span>
                                دفع عند التسليم (آجل)
                            </label>
                        </div>
                    </div>
                    <div class="text-sm text-error-600 mt-1">
                        @error('payment_method')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            <!-- الملاحظات -->
            <div class="mt-6">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الملاحظات</label>
                <textarea placeholder="وصف المركبه" rows="4" name="notes"
                    class="hover:border-brand-500 dark:bg-dark-900 h-auto w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs resize-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">{{ old('notes', $shipment->notes) }}</textarea>
                <div class="text-sm text-error-600 mt-1">
                    @error('notes')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <div class="relative">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">المبلغ</label>

                <input type="number" id="cod_amount" name="cod_amount"
                    value="{{ old('cod_amount', $shipment->cod_amount) }}" min="0" step="0.01"
                    placeholder="0.00"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg 
                               bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300
                               focus:ring-2 focus:ring-brand-500 focus:border-brand-500
                               @error('cod_amount') border-error-500 focus:ring-error-500 focus:border-error-500 @enderror">


            </div>

            @error('cod_amount')
                <div class="text-sm text-error-600 mt-1">
                    {{ $message }}
                </div>
            @enderror
            <!-- الأزرار -->
            <div class="mt-6 flex flex-col md:flex-row gap-4">
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
        document.getElementById('from_city').addEventListener('change', function() {
            let selectedFrom = this.value;
            let toCitySelect = document.getElementById('to_city');

            for (let option of toCitySelect.options) {
                option.hidden = false;
            }

            if (selectedFrom) {
                let optionToHide = toCitySelect.querySelector('option[value="' + selectedFrom + '"]');
                if (optionToHide) optionToHide.hidden = true;
            }

            toCitySelect.value = "";
        });
    </script>
@endsection
