@extends('layouts.app')
@section('title', 'قائمة الطرود')
@section('Breadcrumb', 'قائمة الطرود')
@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
        <!-- رأس الصفحة مع زر إضافة جديد -->
        <div class="flex flex-col xl:flex-row md:items-center  justify-between mb-6">
            <a href="{{ route('request.create') }}"
                class="mt-4 md:mt-0 bg-brand-500 hover:bg-brand-600 text-white font-medium py-2 my-4 px-4 rounded-lg inline-flex items-center">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                تسجيل طرد جديد
            </a>

            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">جميع الطرود المسجلة</h2>

        </div>

        <!-- جدول الطرود -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            رقم السند
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            المرسل
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            المستلم
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            الوجهة
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            نوع الطرد
                        </th>
                        {{-- <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            عدد قروف العسل
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            عدد جوالين العسل
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            عدد الرمز العسل
                        </th> --}}
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            السائق
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            حالة الدفع
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            حالة الطرد
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            المبلغ
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($requests as $request)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">
                                {{ $request->bond_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-800 dark:text-gray-400">{{ $request->sender_name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $request->sender_phone }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-800 dark:text-gray-400">{{ $request->receiver_name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $request->receiver_phone }}</div>
                            </td>

                            <td class="py-3">
                                <div class="flex items-center justify-center space-x-2">
                                    <p
                                        class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
                                        {{ $request->from_city }}
                                    </p>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    <p
                                        class="rounded-full bg-blue-light-50 px-2 py-0.5 text-theme-xs font-medium text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
                                        {{ $request->to_city }}
                                    </p>
                                </div>
                            </td>

                            {{-- <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-800 dark:text-gray-300"></div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">→ </div>
                            </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-400">
                                {{ $request->package_type }}
                            </td>
                            {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-400">
                                {{ $request->no_honey_jars }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-400">
                                {{ $request->no_gallons_honey }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-400">
                                {{ $request->code }}
                            </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-400">
                                {{ $request->driver->name ?? 'غير متاح' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($request->payment_method == 'prepaid')
                                    <span
                                        class="rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                        دفع مقدم
                                    </span>
                                @else
                                    <span
                                        class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-500">
                                        أجل
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($request->status == 'pending')
                                    <span
                                        class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-500">
                                        قيد الانتظار
                                    </span>
                                @elseif ($request->status == 'in_transit')
                                    <span
                                        class="rounded-full bg-blue-light-50 px-2 py-0.5 text-theme-xs font-medium text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
                                        قيد النقل
                                    </span>
                                @elseif ($request->status == 'deliverd')
                                    <span
                                        class="rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                        تم التسليم
                                    </span>
                                @elseif ($request->status == 'cancelled')
                                    <span
                                        class="rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
                                        ملغي
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-warning-600 dark:text-warning-400">
                                {{ $request->cod_amount ? number_format($request->cod_amount, 2) . ' ر.ي' : '0.00 ر.ي' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex mx-5 space-x-reverse space-x-2">
                                    <!-- زر التعديل -->
                                    <a href="{{ route('request.edit', $request->id) }}"
                                        class="text-brand-600 hover:text-brand-900 dark:text-brand-400 dark:hover:text-brand-300 mx-2"
                                        title="تعديل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    {{-- <!-- زر الحذف -->
        <form action="{{ route('request.destroy', $request->id) }}" method="POST"
            class="inline">
            @csrf
            @method('DELETE')
            <button type="button"
                onclick="confirmDelete('{{ $request->id }}', '{{ $request->sender_name }}')"
                class="text-error-600 hover:text-error-900 dark:text-error-400 dark:hover:text-error-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </form> --}}

                                    <!-- زر الطباعة -->


                                    <!-- زر العرض -->
                                    <a href="{{ route('request.show', $request->id) }}"
                                        class="text-success-600 hover:text-success-900 dark:text-success-400 dark:hover:text-success-300"
                                        title="عرض التفاصيل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    <a href="{{ route('request.invoice', $request->id) }}" target="_blank"
                                        class="text-brand-600 hover:text-brand-900 dark:text-brand-400 dark:hover:text-brand-300 mx-2"
                                        title="طباعة الفاتورة">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                لا توجد طرود مسجلة حالياً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- الترقيم -->
        @if ($requests->hasPages())
            <div class="mt-6">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    <!-- نموذج تأكيد الحذف -->
    <div id="deleteModal"
        class="fixed inset-0 bg-gray-400/50 backdrop-blur-[32px] hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 m-4 max-w-md w-full">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-error-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">تأكيد الحذف</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500 dark:text-gray-400" id="deleteMessage">
                        هل أنت متأكد من رغبتك في حذف هذا الطرد؟ لا يمكن التراجع عن هذا الإجراء.
                    </p>
                </div>
                <div class="mt-6 flex justify-center space-x-4 space-x-reverse">
                    <button type="button" onclick="hideDeleteModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        إلغاء
                    </button>
                    <form id="deleteForm" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 mx-2 text-sm font-medium text-white bg-error-500 hover:bg-error-700 rounded-lg">
                            حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id, senderName) {
            const deleteForm = document.getElementById('deleteForm');
            const deleteMessage = document.getElementById('deleteMessage');
            const deleteModal = document.getElementById('deleteModal');

            deleteForm.action = `/request/${id}`;
            deleteMessage.textContent = `هل أنت متأكد من رغبتك في حذف طرد ${senderName}؟ لا يمكن التراجع عن هذا الإجراء.`;
            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex');
        }

        function hideDeleteModal() {
            const deleteModal = document.getElementById('deleteModal');
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
        }

        // إغلاق النافذة عند النقر خارجها
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideDeleteModal();
            }
        });
    </script>

@endsection
