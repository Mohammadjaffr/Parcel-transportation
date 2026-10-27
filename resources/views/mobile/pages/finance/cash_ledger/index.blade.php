@extends('mobile.layouts.app')
@section('title', 'المالية والصندوق')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div class="flex relative flex-col gap-6 px-4 pb-24 min-h-screen bg-slate-50/50" dir="rtl" x-data="cashLedgerHandler()">

        {{-- ================= 1. رأس الصفحة والعنوان ================= --}}
        <div class="flex flex-col gap-4 justify-between items-start mt-6 sm:flex-row sm:items-center">
            <!-- قسم العناوين -->
            <div class="flex flex-col">
                <h1 class="text-3xl font-black font-headline text-slate-800">دفتر الصندوق</h1>
                <p class="mt-1 text-sm font-medium text-slate-500">
                    إجمالي <span class="font-bold text-primary">{{ number_format($totalCount) }}</span> حركة مالية
                </p>
            </div>

            <!-- قسم الأزرار -->
            <div class="flex gap-3 items-center w-full sm:w-auto">
                <!-- زر التصدير -->
                <a href="{{ route('cash.ledger.export', request()->query()) }}"
                    class="w-12 h-12 bg-[#107c41] hover:bg-[#0c5e31] text-white rounded-xl flex items-center justify-center shadow-[0_8px_20px_rgba(16,124,65,0.3)] active:scale-95 transition-all shrink-0"
                    title="تصدير إلى Excel">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M14.504 3.033L5.435 4.544C5.19 4.585 5 4.793 5 5.042v14.416c0 .249.19.457.435.498l9.069 1.511c.3.05.572-.18.572-.483V3.516c0-.303-.272-.533-.572-.483zM9.544 16.513l-1.34-3.136-1.42 3.136H5.4l2.128-4.316L5.514 8.013h1.411l1.24 3.018 1.34-3.018h1.34l-2.008 4.254 2.127 4.246h-1.42z" />
                        <path
                            d="M15.548 3.327v17.846h5.816c.35 0 .636-.286.636-.636V3.963c0-.35-.286-.636-.636-.636h-5.816zm2.348 11.233H16.64v-1.251h1.256v1.251zm0-2.484H16.64v-1.25h1.256v1.25zm0-2.484H16.64V8.342h1.256v1.25zm2.49 4.968h-1.256v-1.251h1.256v1.251zm0-2.484h-1.256v-1.25h1.256v1.25zm0-2.484h-1.256V8.342h1.256v1.25z" />
                    </svg>
                </a>

                <!-- زر إيراد -->
                <button @click="openCreateModal('income')" type="button"
                    class="flex-1 sm:flex-none h-12 px-6 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl flex items-center justify-center gap-2 shadow-[0_8px_20px_rgba(16,185,129,0.3)] active:scale-95 transition-all font-bold text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>إيراد</span>
                </button>

                <!-- زر صرف -->
                <button @click="openCreateModal('expense')" type="button"
                    class="flex-1 sm:flex-none h-12 px-6 bg-rose-500 hover:bg-rose-600 text-white rounded-xl flex items-center justify-center gap-2 shadow-[0_8px_20px_rgba(244,63,94,0.3)] active:scale-95 transition-all font-bold text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path>
                    </svg>
                    <span>صرف</span>
                </button>
            </div>
        </div>

        {{-- ================= 2. بطاقات الإحصائيات (Swipeable/Grid) ================= --}}
        <div class="grid grid-cols-2 gap-3 mt-2">
            <div
                class="bg-white rounded-[1.5rem] p-4 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-emerald-50 rounded-full"></div>
                <div class="flex relative z-10 flex-col">
                    <div class="flex gap-1.5 items-center mb-2 text-emerald-500">
                        <span class="material-symbols-outlined text-[16px]">south_west</span>
                        <span class="text-[11px] font-bold text-slate-500">الوارد</span>
                    </div>
                    <span
                        class="text-xl font-black font-headline text-slate-800">{{ number_format($totalIncome, 2) }}</span>
                </div>
            </div>

            <div
                class="bg-white rounded-[1.5rem] p-4 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-rose-50 rounded-full"></div>
                <div class="flex relative z-10 flex-col">
                    <div class="flex gap-1.5 items-center mb-2 text-rose-500">
                        <span class="material-symbols-outlined text-[16px]">north_east</span>
                        <span class="text-[11px] font-bold text-slate-500">المنصرف</span>
                    </div>
                    <span
                        class="text-xl font-black font-headline text-slate-800">{{ number_format($totalExpense, 2) }}</span>
                </div>
            </div>

            <div class="col-span-2 bg-slate-900 rounded-[1.5rem] p-4 shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 rounded-full blur-2xl bg-primary/20"></div>
                <div class="flex relative z-10 justify-between items-center">
                    <div class="flex gap-2 items-center mb-1 text-white/80">
                        <span class="material-symbols-outlined text-[16px]">balance</span>
                        <span class="text-xs font-bold">الصافي للفترة</span>
                    </div>
                    <span class="text-2xl font-headline font-black {{ $netPeriod >= 0 ? 'text-white' : 'text-rose-400' }}">
                        {{ number_format($netPeriod, 2) }} <span class="text-xs font-bold text-slate-400">ريال</span>
                    </span>
                </div>
            </div>
        </div>

        {{-- ================= 3. الفلترة والبحث ================= --}}
        <form method="GET" action="{{ route('cash.ledger.index') }}" x-ref="filterForm" class="flex flex-col gap-3 mt-2">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" x-model="searchQuery"
                    placeholder="ابحث برقم السند، أو المرجع..."
                    class="pr-4 pl-12 w-full h-14 text-sm bg-white rounded-2xl border-none shadow-[0_8px_30px_rgb(0,0,0,0.04)] outline-none focus:ring-2 focus:ring-primary/20 text-slate-700 placeholder-slate-400">
                <span
                    class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">search</span>
            </div>

            <div x-data="{ showFilters: {{ request()->hasAny(['type', 'cash_category_id', 'start_date', 'end_date', 'branch_id', 'payment_method']) ? 'true' : 'false' }} }" class="flex flex-col gap-3">
                <button type="button" @click="showFilters = !showFilters"
                    class="flex justify-between items-center px-4 h-12 text-sm font-bold bg-white rounded-2xl border shadow-sm border-slate-100 text-slate-600">
                    <div class="flex gap-2 items-center">
                        <span class="material-symbols-outlined text-[18px]">filter_list</span>
                        فلاتر متقدمة
                    </div>
                    <span class="transition-transform material-symbols-outlined"
                        :class="showFilters ? 'rotate-180' : ''">expand_more</span>
                </button>

                <div x-show="showFilters" x-collapse class="flex flex-col gap-3">
                    @if (in_array(auth()->user()->type, ['admin', 'super_admin']) && isset($branches) && $branches->isNotEmpty())
                        <select name="branch_id"
                            class="px-3 w-full h-12 text-sm bg-white rounded-xl border outline-none border-slate-200 text-slate-700">
                            <option value="">كافة الفروع</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ isset($selectedBranchId) && $selectedBranchId == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    <div class="grid grid-cols-2 gap-3">
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="px-3 w-full h-12 text-xs bg-white rounded-xl border outline-none border-slate-200 text-slate-700"
                            placeholder="من تاريخ">
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="px-3 w-full h-12 text-xs bg-white rounded-xl border outline-none border-slate-200 text-slate-700"
                            placeholder="إلى تاريخ">
                    </div>

                    <select name="type"
                        class="px-3 w-full h-12 text-sm bg-white rounded-xl border outline-none border-slate-200 text-slate-700">
                        <option value="">الكل (وارد ومنصرف)</option>
                        <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>وارد فقط</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>منصرف فقط</option>
                    </select>

                    <select name="cash_category_id"
                        class="px-3 w-full h-12 text-sm bg-white rounded-xl border outline-none border-slate-200 text-slate-700">
                        <option value="">كافة التصنيفات</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ request('cash_category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $cat->type === 'income' ? 'وارد' : 'منصرف' }})
                            </option>
                        @endforeach
                    </select>

                    <select name="payment_method"
                        class="px-3 w-full h-12 text-sm bg-white rounded-xl border outline-none border-slate-200 text-slate-700">
                        <option value="">كافة طرق الدفع</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقداً</option>
                        <option value="bank_transfer"
                            {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>حوالة</option>
                    </select>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 h-12 text-sm font-bold text-white rounded-xl shadow-md bg-slate-800">تطبيق</button>
                        <a href="{{ route('cash.ledger.index') }}"
                            class="flex justify-center items-center px-6 h-12 text-sm font-bold rounded-xl bg-slate-100 text-slate-600">إعادة
                            ضبط</a>
                    </div>
                </div>
            </div>
        </form>

        {{-- ================= 4. بطاقات الحركات المالية ================= --}}
        <div class="mt-2 space-y-5">
            @forelse($transactions as $trx)
                <div x-show="searchQuery === '' || '{{ $trx->receipt_number }}'.includes(searchQuery) || '{{ $trx->reference_number }}'.includes(searchQuery)"
                    class="bg-white rounded-[24px] border border-slate-200/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] overflow-visible transition-all duration-300 relative group">

                    {{-- شريط لوني علوي خفيف --}}
                    <div
                        class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r {{ $trx->type === 'income' ? 'from-emerald-500/80 to-emerald-400/80' : 'from-rose-500/80 to-rose-400/80' }} rounded-t-[24px] opacity-70">
                    </div>

                    {{-- ================= الرأس (Header) ================= --}}
                    <div class="flex justify-between items-start p-5">
                        <div class="flex gap-3 items-center">
                            <div
                                class="w-11 h-11 rounded-[14px] {{ $trx->type === 'income' ? 'bg-emerald-50 text-emerald-500' : 'bg-rose-50 text-rose-500' }} flex items-center justify-center border border-slate-100/80 group-hover:scale-105 transition-transform duration-300">
                                <span
                                    class="material-symbols-outlined text-[22px]">{{ $trx->type === 'income' ? 'south_west' : 'north_east' }}</span>
                            </div>
                            <div class="flex flex-col">
                                <p class="text-[10px] font-bold text-slate-400 mb-0.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">schedule</span>
                                    {{ $trx->transaction_date->locale('ar')->translatedFormat('l') }} {{ $trx->transaction_date->format('Y/m/d') }}
                                </p>
                                <h3 class="text-sm font-black tracking-tight text-slate-900 font-headline" dir="ltr">
                                    {{ $trx->formatted_receipt_number }}
                                </h3>
                            </div>
                        </div>

                        <div class="flex gap-2 items-center">
                            <span
                                class="px-2.5 py-1 rounded-full text-[10px] font-black {{ $trx->type === 'income' ? 'bg-emerald-50 text-emerald-600 ring-emerald-500/20' : 'bg-rose-50 text-rose-600 ring-rose-500/20' }} ring-1 ring-inset whitespace-nowrap max-w-[100px] truncate">
                                {{ $trx->category->name }}
                            </span>

                            {{-- قائمة الثلاث نقاط --}}
                            <div x-data="{ openMenu: false }" class="relative">
                                <button type="button" @click="openMenu = !openMenu" @click.away="openMenu = false"
                                    class="flex justify-center items-center w-8 h-8 rounded-full transition-colors text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>

                                <div x-show="openMenu" x-transition.opacity.duration.200ms x-cloak
                                    class="absolute top-full left-0 mt-1.5 w-44 bg-white/90 backdrop-blur-md rounded-2xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.15)] border border-slate-100/50 z-50 overflow-hidden py-1.5">

                                    @if(is_null($trx->source_id) && is_null($trx->source_type))
                                        <button type="button"
                                            @click="openEditModal(@js([
                                                'id' => $trx->id,
                                                'type' => $trx->type,
                                                'branch_id' => $trx->branch_id,
                                                'cash_category_id' => $trx->cash_category_id,
                                                'category_name' => $trx->category?->name,
                                                'driver_id' => $trx->driver_id,
                                                'driver_name' => $trx->driver_name,
                                                'driver_phone' => $trx->driver_phone,
                                                'amount' => (float) $trx->amount,
                                                'payment_method' => $trx->payment_method,
                                                'transaction_date' => $trx->transaction_date->format('Y-m-d'),
                                                'reference_number' => $trx->reference_number ?? '',
                                                'notes' => $trx->notes ?? '',
                                                'attachment_path' => $trx->attachment_path,
                                            ])); openMenu = false"
                                            class="flex gap-2.5 items-center px-4 py-2 w-full text-xs font-bold text-right transition-colors text-amber-600 hover:bg-slate-50">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                            تعديل السند
                                        </button>
                                    @endif

                                    <button type="button" @click="viewDetails({{ $trx->id }}); openMenu = false"
                                        class="flex gap-2.5 items-center px-4 py-2 w-full text-xs font-bold text-right transition-colors text-slate-600 hover:bg-slate-50 hover:text-primary">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        التفاصيل
                                    </button>

                                    <a href="{{ route('cash.ledger.receipt', $trx->id) }}" target="_blank"
                                        class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold transition-colors text-slate-600 hover:bg-slate-50 hover:text-primary">
                                        <span class="material-symbols-outlined text-[18px]">print</span>
                                        طباعة السند
                                    </a>

                                    @php
                                        $whatsappFallback = \App\Services\WhatsApp\WhatsAppLinkService::generate($trx, 'cash_ledger');
                                    @endphp
                                    <button type="button" 
                                        onclick="sendCashReceiptDirect({{ $trx->id }}, '{{ $trx->driver_phone ?? '' }}', this, '{{ $whatsappFallback ?? '' }}'); openMenu = false"
                                        class="flex gap-2.5 items-center px-4 py-2 w-full text-xs font-bold text-right transition-colors text-emerald-600 hover:bg-slate-50">
                                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                        </svg>
                                        إرسال واتساب
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================= الفاصل المقطّع ================= --}}
                    <div class="flex overflow-hidden relative items-center h-4">
                        <div
                            class="absolute -right-2 w-4 h-4 rounded-full border-l shadow-inner bg-slate-50/50 border-slate-200/60">
                        </div>
                        <div class="w-full border-t-[1.5px] border-dashed border-slate-200/70"></div>
                        <div
                            class="absolute -left-2 w-4 h-4 rounded-full border-r shadow-inner bg-slate-50/50 border-slate-200/60">
                        </div>
                    </div>

                    {{-- ================= جسد البطاقة ================= --}}
                    <div class="p-5 pt-4 space-y-5">
                        <div class="flex gap-4 justify-between items-start">
                            <div class="flex gap-3 items-stretch w-1/2">
                                <div class="flex flex-col flex-1 space-y-3">
                                    @if (in_array(auth()->user()->type, ['admin', 'super_admin']))
                                        <div>
                                            <p class="text-[9px] font-black text-slate-400 mb-0.5 tracking-wide">الفرع</p>
                                            <p class="text-xs font-bold text-slate-800">{{ $trx->branch->name ?? '-' }}
                                            </p>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 mb-0.5 tracking-wide">المسؤول</p>
                                        <p class="text-xs font-bold text-slate-800">{{ $trx->user->name ?? '-' }}</p>
                                    </div>
                                    @if ($trx->related_entity)
                                        <div>
                                            <p class="text-[9px] font-black text-slate-400 mb-0.5 tracking-wide">المرجع</p>
                                            @if ($trx->related_entity['url'])
                                                <a href="{{ $trx->related_entity['url'] }}" class="text-[10px] font-mono font-bold text-primary hover:underline flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[13px]">
                                                        {{ $trx->related_entity['type'] === 'trip' ? 'explore' : ($trx->related_entity['type'] === 'shipment' ? 'package_2' : 'tag') }}
                                                    </span>
                                                    <span>{{ $trx->related_entity['label'] }}</span>
                                                </a>
                                            @else
                                                <p class="text-[10px] font-mono font-bold text-slate-800 flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[13px] text-gray-400">
                                                        {{ $trx->related_entity['type'] === 'trip' ? 'explore' : ($trx->related_entity['type'] === 'shipment' ? 'package_2' : 'tag') }}
                                                    </span>
                                                    <span>{{ $trx->related_entity['label'] }}</span>
                                                </p>
                                            @endif
                                        </div>
                                    @elseif ($trx->reference_number)
                                        <div>
                                            <p class="text-[9px] font-black text-slate-400 mb-0.5 tracking-wide">المرجع</p>
                                            <p class="text-[10px] font-mono text-slate-800">{{ $trx->reference_number }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div
                                class="flex flex-col gap-2.5 p-3 w-1/2 rounded-xl border bg-slate-50/70 border-slate-100/80 min-h-[80px]">
                                @if($trx->driver_name)
                                    <div class="flex flex-col">
                                        <span class="text-[9px] text-slate-400 font-bold mb-1">السائق:</span>
                                        <span class="text-[11px] font-bold text-slate-800 line-clamp-1">
                                            {{ $trx->driver_name }}
                                        </span>
                                        @if($trx->driver_phone)
                                            <span class="text-[10px] font-mono text-slate-500 mt-0.5" dir="ltr">
                                                {{ $trx->driver_phone }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex flex-col">
                                        <span class="text-[9px] text-slate-400 font-bold mb-1">البيان / ملاحظات:</span>
                                        <span class="text-[10px] font-bold text-slate-600 line-clamp-3 leading-relaxed">
                                            {{ $trx->notes ?: 'لا يوجد' }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- ================= كبسولة المالية ================= --}}
                        <div
                            class="bg-slate-800 rounded-[18px] p-3.5 flex justify-between items-center shadow-lg shadow-slate-900/10">
                            <div class="flex gap-2.5 items-center">
                                <div
                                    class="flex justify-center items-center w-9 h-9 rounded-xl bg-slate-700 text-slate-300">
                                    <span
                                        class="material-symbols-outlined text-[18px]">{{ $trx->payment_method === 'cash' ? 'payments' : 'account_balance' }}</span>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-300 mb-0.5">طريقة الدفع</p>
                                    <p class="text-[11px] font-bold text-white tracking-wide">
                                        {{ $trx->payment_method === 'cash' ? 'نقداً (كاش)' : 'تحويل بنكي' }}
                                    </p>
                                </div>
                            </div>

                            <div class="pl-2 text-left">
                                <p class="text-[9px] font-bold text-slate-400 mb-0.5">المبلغ</p>
                                <p
                                    class="text-lg font-black tracking-tight leading-none {{ $trx->type === 'income' ? 'text-emerald-400' : 'text-rose-400' }} font-headline">
                                    {{ number_format($trx->amount, 2) }} <span
                                        class="text-[10px] font-bold text-slate-300">ريال</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div
                    class="flex flex-col items-center justify-center py-20 bg-white rounded-[24px] border-2 border-dashed border-slate-200/70 mt-4 shadow-sm">
                    <div class="relative mb-4">
                        <div class="absolute inset-0 rounded-full blur-xl bg-primary/20"></div>
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-slate-50 to-slate-100 rounded-[18px] flex items-center justify-center border border-white shadow-sm relative z-10">
                            <span class="material-symbols-outlined text-[32px] text-slate-300">receipt_long</span>
                        </div>
                    </div>
                    <h3 class="text-sm font-black text-slate-700 font-headline">لا توجد حركات مالية</h3>
                    <p class="text-[11px] font-bold text-slate-400 mt-1">لم نعثر على أي حركات تطابق بحثك حالياً.</p>
                </div>
            @endforelse

            @if (method_exists($transactions, 'hasPages') && $transactions->hasPages())
                <div class="mt-8">
                    {{ $transactions->links('vendor.pagination.mobile') }}
                </div>
            @endif
        </div>

        {{-- تضمين المودالات --}}
        @include('mobile.pages.finance.cash_ledger.modals.create-transaction-modal')
        @include('mobile.pages.finance.cash_ledger.modals.details-transaction-modal')
    </div>

    <script>
        function cashLedgerHandler() {
            return {
                searchQuery: '',
                showCreateModal: false,
                showDetailsModal: false,
                createType: 'income',
                rawCategories: @js($categories),
                rawDrivers: @js($drivers ?? []),
                details: {},

                // حالة وضع التعديل
                isEditMode: false,
                editId: null,
                formData: {
                    branch_id: '{{ auth()->user()->branch_id }}',
                    amount: '',
                    payment_method: 'cash',
                    transaction_date: '{{ now()->toDateString() }}',
                    reference_number: '',
                    notes: '',
                    attachment_path: null
                },

                categoryDropdownOpen: false,
                categorySearchText: '',
                selectedCategoryId: '',
                selectedCategoryName: '',

                driverDropdownOpen: false,
                driverSearchText: '',
                selectedDriverId: '',
                selectedDriverName: '',
                selectedDriverPhone: '',

                get formActionUrl() {
                    if (this.isEditMode && this.editId) {
                        return `{{ url('finance/cash-ledger') }}/${this.editId}`;
                    }
                    return `{{ route('cash.ledger.store') }}`;
                },

                get availableCategories() {
                    return this.rawCategories.filter(c => c.type === this.createType);
                },

                get filteredCategories() {
                    if (!this.categorySearchText || this.categorySearchText.trim() === '') {
                        return this.availableCategories;
                    }
                    const query = this.categorySearchText.trim().toLowerCase();
                    return this.availableCategories.filter(c => c.name.toLowerCase().includes(query));
                },

                selectCategory(category) {
                    this.selectedCategoryId = category.id;
                    this.selectedCategoryName = category.name;
                    this.categoryDropdownOpen = false;
                    this.categorySearchText = '';
                },

                clearCategory() {
                    this.selectedCategoryId = '';
                    this.selectedCategoryName = '';
                    this.categorySearchText = '';
                },

                toggleCategoryDropdown() {
                    this.categoryDropdownOpen = !this.categoryDropdownOpen;
                    if (this.categoryDropdownOpen) {
                        this.categorySearchText = '';
                        this.$nextTick(() => {
                            this.$refs.categorySearchInput?.focus();
                        });
                    }
                },

                get filteredDrivers() {
                    if (!this.driverSearchText || this.driverSearchText.trim() === '') {
                        return this.rawDrivers;
                    }
                    const query = this.driverSearchText.trim().toLowerCase();
                    return this.rawDrivers.filter(d => 
                        (d.name && d.name.toLowerCase().includes(query)) ||
                        (d.phone && d.phone.toLowerCase().includes(query))
                    );
                },

                selectDriver(driver) {
                    this.selectedDriverId = driver.id;
                    this.selectedDriverName = driver.name;
                    this.selectedDriverPhone = driver.phone || '';
                    this.driverDropdownOpen = false;
                    this.driverSearchText = '';
                },

                clearDriver() {
                    this.selectedDriverId = '';
                    this.selectedDriverName = '';
                    this.selectedDriverPhone = '';
                    this.driverDropdownOpen = false;
                    this.driverSearchText = '';
                },

                toggleDriverDropdown() {
                    this.driverDropdownOpen = !this.driverDropdownOpen;
                    if (this.driverDropdownOpen) {
                        this.driverSearchText = '';
                        this.$nextTick(() => {
                            this.$refs.driverSearchInput?.focus();
                        });
                    }
                },

                openCreateModal(type) {
                    this.isEditMode = false;
                    this.editId = null;
                    this.createType = type;
                    this.formData = {
                        branch_id: '{{ auth()->user()->branch_id }}',
                        amount: '',
                        payment_method: 'cash',
                        transaction_date: '{{ now()->toDateString() }}',
                        reference_number: '',
                        notes: '',
                        attachment_path: null
                    };
                    this.selectedCategoryId = '';
                    this.selectedCategoryName = '';
                    this.categorySearchText = '';
                    this.categoryDropdownOpen = false;

                    this.selectedDriverId = '';
                    this.selectedDriverName = '';
                    this.selectedDriverPhone = '';
                    this.driverSearchText = '';
                    this.driverDropdownOpen = false;

                    this.showCreateModal = true;
                },

                openEditModal(item) {
                    this.isEditMode = true;
                    this.editId = item.id;
                    this.createType = item.type;
                    this.formData = {
                        branch_id: item.branch_id,
                        amount: item.amount,
                        payment_method: item.payment_method,
                        transaction_date: item.transaction_date,
                        reference_number: item.reference_number || '',
                        notes: item.notes || '',
                        attachment_path: item.attachment_path || null
                    };
                    this.selectedCategoryId = item.cash_category_id;
                    this.selectedCategoryName = item.category_name || '';
                    if (!this.selectedCategoryName && this.selectedCategoryId) {
                        const found = this.rawCategories.find(c => c.id == this.selectedCategoryId);
                        if (found) this.selectedCategoryName = found.name;
                    }
                    this.categorySearchText = '';
                    this.categoryDropdownOpen = false;

                    this.selectedDriverId = item.driver_id || '';
                    this.selectedDriverName = item.driver_name || '';
                    this.selectedDriverPhone = item.driver_phone || '';
                    this.driverSearchText = '';
                    this.driverDropdownOpen = false;

                    this.showCreateModal = true;
                },

                async viewDetails(id) {
                    try {
                        const response = await fetch(`{{ url('finance/cash-ledger') }}/${id}/details`);
                        const res = await response.json();
                        if (res.success) {
                            this.details = res.data;
                            this.showDetailsModal = true;
                        }
                    } catch (error) {
                        alert('تعذر جلب تفاصيل السند.');
                    }
                }
            }
        }

        async function sendCashReceiptDirect(transactionId, recipientPhone, triggerElement, fallbackUrl) {
            let phone = recipientPhone ? recipientPhone.trim() : '';

            if (!phone) {
                const inputPhone = prompt('الرجاء إدخال رقم هاتف المستلم (مثال: 967770000000):');
                if (!inputPhone || !inputPhone.trim()) {
                    return;
                }
                phone = inputPhone.trim();
            }

            const originalContent = triggerElement ? triggerElement.innerHTML : null;
            if (triggerElement) {
                triggerElement.disabled = true;
                triggerElement.innerHTML = `
                    <svg class="animate-spin h-4 w-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>جارٍ الإرسال...</span>
                `;
            }

            try {
                const response = await fetch(`{{ url('whatsapp/cash-ledger') }}/${transactionId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ phone: phone })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    showNotificationToast(data.message || 'تم إرسال السند عبر الواتساب بنجاح', 'success');
                } else if (data.fallback_url || fallbackUrl) {
                    const targetUrl = data.fallback_url || fallbackUrl;
                    window.open(targetUrl, '_blank');
                    showNotificationToast(data.message || 'تم فتح محادثة الواتساب عبر الرابط المباشر', 'info');
                } else {
                    showNotificationToast(data.message || 'تعذر إرسال الرسالة عبر الواتساب', 'error');
                }
            } catch (err) {
                console.error('WhatsApp Cash Receipt Error:', err);
                if (fallbackUrl) {
                    window.open(fallbackUrl, '_blank');
                    showNotificationToast('تم فتح محادثة الواتساب كخيار بديل', 'info');
                } else {
                    showNotificationToast('حدث خطأ أثناء محاولة الإرسال', 'error');
                }
            } finally {
                if (triggerElement && originalContent) {
                    triggerElement.disabled = false;
                    triggerElement.innerHTML = originalContent;
                }
            }
        }

        function showNotificationToast(message, type = 'info') {
            const toast = document.createElement('div');
            const bgClass = type === 'success' ? 'bg-emerald-600' : (type === 'error' ? 'bg-rose-600' : 'bg-slate-800');
            toast.className = `fixed bottom-6 right-6 z-[999999] px-4 py-3 rounded-xl text-white text-xs font-bold shadow-xl flex items-center gap-2 ${bgClass} transition-all duration-300 transform translate-y-4 opacity-0`;
            toast.innerHTML = `
                <span class="material-symbols-outlined text-[18px]">
                    ${type === 'success' ? 'check_circle' : (type === 'error' ? 'error' : 'info')}
                </span>
                <span>${message}</span>
            `;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('translate-y-4', 'opacity-0');
            }, 10);
            setTimeout(() => {
                toast.classList.add('translate-y-4', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }
    </script>
@endsection
