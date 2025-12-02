@extends('layouts.app')
@section('title', 'تسجيل طرد جديد')
@section('Breadcrumb', 'تسجيل طرد جديد')
@section('content')

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">

        <form action="{{ route('request.store') }}" method="POST">
            @csrf

            <!-- الشبكة الرئيسية: عمود واحد في الموبايل وعمودين في الشاشات الأكبر -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6">

                <!-- بيانات المرسل -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300">بيانات المرسل</h3>

                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الاسم</label>
                        <input type="text" name="sender_name"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="اسم المرسل">
                    </div>

                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف</label>
                        <input type="text" name="sender_phone"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="رقم الهاتف">
                    </div>

                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">العنوان</label>
                        <input type="text" name="sender_address"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="عنوان المرسل">
                    </div>
                </div>

                <!-- بيانات المستلم -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300">بيانات المستلم</h3>

                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الاسم</label>
                        <input type="text" name="recipient_name" required
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="اسم المستلم">
                    </div>

                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف</label>
                        <input type="text" name="recipient_phone" required
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="رقم الهاتف">
                    </div>

                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">المحافظة</label>
                        <input type="text" name="governorate" id="governorate"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="اختر المحافظة">
                    </div>


                </div>


            </div>
            <div class="mt-3">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الفرع</label>
                <select name="branch"
                    class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                    <option value="">اختر الفرع</option>
                    <option>الفرع الرئيسي</option>
                    <option>تعز</option>
                    <option>إب</option>
                    <option>عدن</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6 mt-6">

                <div class="mt-3">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">سعر الشحن</label>
                    <input type="number" name="shipping_price" id="price" disabled
                        class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                        placeholder="يُحسب تلقائيًا">
                </div>

                <div class="mt-3">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الملاحظات</label>
                   <textarea placeholder="وصف المركبه" rows="4" name="description"
                class="hover:border-brand-500 dark:bg-dark-900 h-auto w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs resize-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"></textarea>
                </div>
                <!-- الوزن -->
                <div class="space-y-4 w-full md:col-span-2">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300">تفاصيل الطرد</h3>

                    <div class="mt-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الوزن (كجم)</label>
                        <input type="number" name="weight" id="weight"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="الوزن">
                    </div>
                </div class="mt-3">
                <!-- طريقة الدفع -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-sm font-bold mb-3 text-gray-700 dark:text-gray-300 my-6">طريقة الدفع</h3>

                    <div class="flex gap-6">
                        <div x-data="{ payment: 'prepaid' }" class="flex gap-6">

                            <!-- دفع مقدم -->
                            <label
                                :class="payment === 'prepaid' ? 'text-gray-700 dark:text-gray-400' :
                                    'text-gray-500 dark:text-gray-400'"
                                class="relative flex cursor-pointer items-center gap-3 text-sm font-medium select-none">

                                <input class="sr-only" type="radio" name="payment_method" value="prepaid"
                                    @change="payment = 'prepaid'">

                                <span
                                    :class="payment === 'prepaid'
                                        ?
                                        'border-brand-500 bg-brand-500' :
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
                                    @change="payment = 'cod'">

                                <span
                                    :class="payment === 'cod'
                                        ?
                                        'border-brand-500 bg-brand-500' :
                                        'bg-transparent border-gray-300 dark:border-gray-700'"
                                    class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">

                                    <span :class="payment === 'cod' ? 'block' : 'hidden'"
                                        class="h-2 w-2 rounded-full bg-white"></span>

                                </span>

                                دفع عند التسليم (آجل)
                            </label>

                        </div>


                    </div>

                </div>
            </div>
            <!-- زر التسجيل -->
            <div class="mt-6">
                <button type="submit"
                    class="bg-brand-500 hover:bg-brand-600 text-white font-medium py-2 px-4 rounded-lg w-full md:w-auto">
                    تسجيل الطرد
                </button>
            </div>

        </form>

    </div>

@endsection
