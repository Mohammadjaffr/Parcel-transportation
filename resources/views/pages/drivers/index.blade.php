@extends('layouts.app')
@section('title', 'السائقين')
@section('Breadcrumb', 'إدارة السائقين')
@section('addButton')
    <button @click="$dispatch('open-create-modal')"
        class="bg-brand-500 hover:bg-brand-600 text-white text-sm py-2 px-4 rounded-lg transition-all hover:shadow-md active:scale-95">
        + إضافة سائق جديد
    </button>
    <x-modals.success-modal />
    <x-modals.error-modal />
@endsection
@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6" x-data="{
        showCreateModal: false,
        showEditModal: false,
        editDriver: { id: null, name: '', phone: '', url: '' },
        openEditModal(id, name, phone) {
            this.editDriver = {
                id: id,
                name: name,
                phone: phone || '',
                url: '{{ url('drivers') }}/' + id
            };
            this.showEditModal = true;
        }
    }"
        @open-create-modal.window="showCreateModal = true">

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">قائمة السائقين</h3>


        </div>

        <div class="overflow-hidden">
            {{-- ===== Mobile View (Cards) ===== --}}
            <div class="flex flex-col gap-4 p-4 lg:hidden">
                @forelse ($drivers as $driver)
                    <div
                        class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-800">
                        <div class="flex justify-between items-center">
                            <div class="flex gap-3 items-center">
                                <div
                                    class="flex justify-center items-center w-10 h-10 text-sm font-bold text-white rounded-full bg-brand-500 dark:text-brand-300">
                                    {{ mb_substr($driver->name, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-semibold text-gray-900 dark:text-white">{{ $driver->name }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400" dir="ltr">
                                        <x-phone-number :value="$driver->phone" />
                                    </span>
                                </div>
                            </div>
                            <button
                                @click="openEditModal({{ $driver->id }}, '{{ addslashes($driver->name) }}', '{{ addslashes($driver->phone) }}')"
                                class="p-2 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors hover:text-brand-500 hover:border-brand-200 dark:bg-gray-900 dark:border-gray-800"
                                title="تعديل">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div
                        class="py-12 text-center rounded-xl border border-gray-100 border-dashed bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-800">
                        <div class="flex flex-col justify-center items-center">
                            <div class="p-3 mb-3 bg-white rounded-full shadow-sm dark:bg-gray-800">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">لا يوجد سائقين</h4>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">لا يوجد سائقين حالياً.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- ===== Desktop View (Table) ===== --}}
            <div class="hidden overflow-x-auto lg:block">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                #</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                الاسم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                الهاتف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($drivers as $driver)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $driver->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    <div class="flex items-center gap-2 justify-end" dir="ltr">
                                        <x-phone-number :value="$driver->phone" />
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex mx-5 space-x-reverse space-x-2">
                                        <button
                                            @click="openEditModal({{ $driver->id }}, '{{ addslashes($driver->name) }}', '{{ addslashes($driver->phone) }}')"
                                            class="text-brand-600 hover:text-brand-900 dark:text-brand-400 dark:hover:text-brand-300 mx-2"
                                            title="تعديل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    لا يوجد سائقين حالياً.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Create Driver Modal --}}
        @include('pages.drivers.modals.create')

        {{-- Edit Driver Modal --}}
        @include('pages.drivers.modals.edit')

    </div>

@endsection
