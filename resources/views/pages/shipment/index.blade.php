@extends('layouts.app')
@section('title', 'قائمة الطرود')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div class="space-y-6 font-outfit" dir="rtl" x-data="{
                            search: '',
                            filterStatus: 'all',
                            showRow(status, bond, sender, receiver) {
                                const matchesSearch = bond.includes(this.search) || sender.includes(this.search) || receiver.includes(this.search);
                                const matchesStatus = this.filterStatus === 'all' || status === this.filterStatus;
                                return matchesSearch && matchesStatus;
                            }
                        }">

        {{-- Tabs --}}
        <div class="flex p-1 bg-gray-100 dark:bg-gray-800 rounded-xl mb-6 w-fit">
            <a href="{{ route('shipment.index', ['type' => 'outgoing']) }}"
                class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all {{ request('type', 'outgoing') == 'outgoing' ? 'bg-white dark:bg-gray-700 text-brand-500 shadow-sm ring-1 ring-gray-200 dark:ring-gray-600' : 'text-gray-500 hover:text-gray-700' }}">
                الطرود الصادرة (من فرعنا)
            </a>
            <a href="{{ route('shipment.index', ['type' => 'incoming']) }}"
                class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all {{ request('type') == 'incoming' ? 'bg-white dark:bg-gray-700 text-brand-500 shadow-sm ring-1 ring-gray-200 dark:ring-gray-600' : 'text-gray-500 hover:text-gray-700' }}">
                الطرود الواردة (إلى فرعنا)
            </a>
        </div>

        <div class="flex gap-6">

            <div @click="filterStatus = 'all'"
                :class="filterStatus === 'all' ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-gray-100'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-50 dark:bg-gray-800 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="text-theme-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">إجمالي
                        الطرود</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $requests->count() }}</h4>
                </div>

            </div>



            <div @click="filterStatus = 'pending'"
                :class="filterStatus === 'pending' ? 'border-warning-500 ring-2 ring-warning-500/20' : 'border-gray-100'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-warning-50 dark:bg-warning-500/10 text-warning-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="text-theme-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">قيد
                        الانتظار</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $requests->where('status', 'pending')->count() }}</h4>
                </div>
            </div>

            <div @click="filterStatus = 'in_transit'" :class="filterStatus === 'in_transit' ? 'border-blue-light-500 ring-2 ring-blue-light-500/20' :
                                        'border-gray-100'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-light-50 dark:bg-blue-light-500/10 text-blue-light-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="text-theme-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">في
                        الطريق</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $requests->where('status', 'in_transit')->count() }}
                    </h4>
                </div>
            </div>

            <div @click="filterStatus = 'delivered'"
                :class="filterStatus === 'delivered' ? 'border-success-500 ring-2 ring-success-500/20' : 'border-gray-100'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="text-theme-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">تم
                        التسليم</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $requests->where('status', 'delivered')->count() }}
                    </h4>
                </div>
            </div>
        </div>

        <div
            class="grid grid-cols-1 md:grid-cols-2 items-center bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm gap-6">

            <div class="relative group w-full">
                <input type="text" x-model="search" placeholder="ابحث برقم السند، المرسل أو المستلم..."
                    class="w-full h-12 pr-11 pl-4 rounded-xl border-none bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 transition-all text-sm font-medium dark:text-white placeholder-gray-400">
                <div
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 group-focus-within:text-brand-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="flex md:justify-end w-full">
                <a href="{{ route('shipment.create') }}"
                    class="h-12 px-8 flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl transition-all shadow-lg shadow-brand-500/20 active:scale-95 text-sm font-bold w-full md:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    تسجيل طرد جديد
                </a>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
            <div class="overflow-x-auto px-4 pb-4">
                <table class="w-full border-separate border-spacing-y-3 text-right">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                            <th class="py-4 px-6">رقم السند</th>
                            <th class="py-4 px-6">الأطراف (مرسل/مستلم)</th>
                            <th class="py-4 px-6 text-center">خط السير</th>
                            <th class="py-4 px-6 text-center">النوع / الدفع</th>
                            <th class="py-4 px-6 text-center">الحالة</th>
                            <th class="py-4 px-6 text-left">التكلفة</th>
                            <th class="py-4 px-6 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        @forelse ($requests as $request)
                            <tr x-show="showRow('{{ $request->status }}', '{{ $request->bond_number }}', '{{ $request->senderCustomer->name ?? '' }}', '{{ $request->receiverCustomer->name ?? '' }}')"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm hover:shadow-md transition-all border border-transparent hover:border-gray-100 dark:hover:border-gray-800">

                                <td class="py-5 px-6 first:rounded-r-2xl border-y border-r dark:border-gray-800/50">
                                    <span
                                        class="px-3 py-1.5 bg-gray-50 dark:bg-gray-800 rounded-lg text-xs font-black text-brand-500 border border-gray-100 dark:border-gray-700 shadow-inner">
                                        #{{ $request->bond_number }}
                                    </span>
                                </td>


                                <td
                                    class="py-5 px-6 border-y dark:border-gray-800/50 text-center text-[10px] font-black uppercase text-gray-500">
                                    {{ $request->senderCustomer->name ?? '-' }} ⇠
                                    {{ $request->receiverCustomer->name ?? '-' }}
                                </td>
                                <td
                                    class="py-5 px-6 border-y dark:border-gray-800/50 text-center text-[10px] font-black uppercase text-gray-500">
                                    {{ $request->senderBranch->name ?? '-' }} ⇠
                                    {{ $request->receiverBranch->name ?? '-' }}
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span
                                            class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $request->package_type }}</span>
                                        <span
                                            class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase {{ $request->payment_method == 'prepaid' ? 'bg-success-50 text-success-600' : 'bg-warning-50 text-warning-600' }}">
                                            {{ $request->payment_method == 'prepaid' ? 'نقداً' : 'آجل' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50 text-center">
                                    @php
                                        $colors = [
                                            'pending' => 'bg-warning-500 shadow-warning-500/20',
                                            'in_transit' => 'bg-blue-light-500 shadow-blue-500/20',
                                            'delivered' => 'bg-success-500 shadow-success-500/20',
                                        ];
                                        $labels = [
                                            'pending' => 'قيد الانتظار',
                                            'in_transit' => 'في الطريق',
                                            'delivered' => 'تم التسليم',
                                        ];
                                    @endphp
                                    <span
                                        class="px-3 py-1 rounded-lg text-[10px] font-black text-white uppercase shadow-lg {{ $colors[$request->status] ?? 'bg-gray-500' }}">
                                        {{ $labels[$request->status] ?? $request->status }}
                                    </span>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50 text-left">
                                    <span class="text-base font-black text-gray-900 dark:text-white">
                                        {{ number_format($request->total_amount, 2) }}
                                        <small class="text-[10px] font-bold text-gray-400 mr-0.5">ر.ي</small>
                                    </span>
                                </td>

                                <td class="py-5 px-6 last:rounded-l-2xl border-y border-l dark:border-gray-800/50 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('shipment.show', $request->id) }}"
                                            class="p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 rounded-xl transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('shipment.invoice', $request->id) }}" target="_blank"
                                            class="p-2 text-gray-400 hover:text-success-500 hover:bg-success-50 rounded-xl transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-20 text-center text-gray-400 italic">
                                    {{ $type === 'incoming' ? 'لا توجد طرود واردة حالياً..' : 'لا توجد طرود صادرة حالياً..' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($requests->hasPages())
                <div class="p-8 bg-gray-50/50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-800">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection