@extends('layouts.app')

@section('title', 'الطرود المرسلة')

@section('content')
    <div x-data="{ searchQuery: '' }" class="px-4 py-8 mx-auto w-full max-w-9xl sm:px-6 lg:px-8 font-body">

        {{-- ================= الرأس (Header) ================= --}}
        <div class="mb-8 sm:flex sm:justify-between sm:items-center">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl font-bold md:text-3xl text-slate-800 font-headline">الطرود المرسلة</h1>
                <p class="mt-1 text-sm font-bold text-slate-500">
                    إجمالي <span class="font-black text-primary">{{ $shipments->total() ?? 0 }}</span> طرد مسجل
                </p>
            </div>

            <div class="flex flex-col gap-3 items-center sm:flex-row">
                {{-- شريط البحث --}}
                <div class="relative w-full sm:w-auto">
                    <input type="text" x-model="searchQuery" placeholder="ابحث برقم السند، أو هاتف العميل..."
                        class="py-2.5 pr-4 pl-10 w-full text-sm font-bold bg-white rounded-xl border shadow-sm transition-all outline-none border-slate-200 md:w-80 focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-700 placeholder-slate-400">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-[20px]">search</span>
                </div>

                {{-- زر الإضافة --}}
                <a href="{{ route('shipment.outgoing.create') }}"
                    class="flex gap-2 justify-center items-center px-4 py-2.5 w-full text-white rounded-xl shadow-lg transition-transform sm:w-auto bg-primary hover:bg-primary-hover shadow-primary/30 active:scale-95">
                    <span class="text-[20px] material-symbols-outlined">add_box</span>
                    <span class="text-sm font-bold">إضافة طرد</span>
                </a>
            </div>
        </div>

        {{-- ================= شريط الفلترة حسب الحالة ================= --}}
        {{-- <div class="flex overflow-x-auto gap-2 p-2 mb-6 bg-white rounded-2xl border shadow-sm border-slate-100 custom-scrollbar">
            <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => null]) }}"
                class="shrink-0 px-5 py-2.5 rounded-xl text-sm font-bold transition-all border 
                {{ !request('status') ? 'bg-slate-800 text-white border-slate-800 shadow-md' : 'bg-transparent text-slate-500 border-transparent hover:bg-slate-50 hover:border-slate-100' }}">
                الكل
            </a>

            <a href="{{ request()->fullUrlWithQuery(['status' => 'pending', 'page' => null]) }}"
                class="shrink-0 px-5 py-2.5 rounded-xl text-sm font-bold transition-all border 
                {{ request('status') == 'pending' ? 'bg-amber-500 text-white border-amber-500 shadow-md shadow-amber-500/20' : 'bg-transparent text-amber-600 border-transparent hover:bg-amber-50 hover:border-amber-100' }}">
                قيد التجهيز
            </a>

            <a href="{{ request()->fullUrlWithQuery(['status' => 'in_transit', 'page' => null]) }}"
                class="shrink-0 px-5 py-2.5 rounded-xl text-sm font-bold transition-all border 
                {{ request('status') == 'in_transit' ? 'bg-blue-500 text-white border-blue-500 shadow-md shadow-blue-500/20' : 'bg-transparent text-blue-600 border-transparent hover:bg-blue-50 hover:border-blue-100' }}">
                في الطريق
            </a>

            <a href="{{ request()->fullUrlWithQuery(['status' => 'delivered', 'page' => null]) }}"
                class="shrink-0 px-5 py-2.5 rounded-xl text-sm font-bold transition-all border 
                {{ request('status') == 'delivered' ? 'bg-emerald-500 text-white border-emerald-500 shadow-md shadow-emerald-500/20' : 'bg-transparent text-emerald-600 border-transparent hover:bg-emerald-50 hover:border-emerald-100' }}">
                تم التسليم
            </a>

            <a href="{{ request()->fullUrlWithQuery(['status' => 'returned', 'page' => null]) }}"
                class="shrink-0 px-5 py-2.5 rounded-xl text-sm font-bold transition-all border 
                {{ request('status') == 'returned' ? 'bg-rose-500 text-white border-rose-500 shadow-md shadow-rose-500/20' : 'bg-transparent text-rose-600 border-transparent hover:bg-rose-50 hover:border-rose-100' }}">
                مرتجع
            </a>

            <a href="{{ request()->fullUrlWithQuery(['status' => 'cancelled', 'page' => null]) }}"
                class="shrink-0 px-5 py-2.5 rounded-xl text-sm font-bold transition-all border 
                {{ request('status') == 'cancelled' ? 'bg-slate-500 text-white border-slate-500 shadow-md shadow-slate-500/20' : 'bg-transparent text-slate-600 border-transparent hover:bg-slate-50 hover:border-slate-100' }}">
                ملغي
            </a>
        </div> --}}

        {{-- ================= جدول الطرود (Desktop Table) ================= --}}
        <div class="bg-white rounded-2xl border shadow-lg border-slate-200/60">
            <div class="overflow-x-auto min-h-[350px]">
                <table class="w-full text-sm text-right whitespace-nowrap">
                    <thead class="text-xs font-black uppercase border-b text-slate-500 bg-slate-50/80 border-slate-200/60">
                        <tr>
                            <th class="px-6 py-4">رقم السند / التاريخ</th>
                            <th class="px-6 py-4">المرسل</th>
                            <th class="px-6 py-4">المستلم والوجهة</th>
                            <th class="px-6 py-4">المحتوى</th>
                            <th class="px-6 py-4">المبلغ</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($shipments as $shipment)
                            <tr x-show="searchQuery === '' || '{{ $shipment->bond_number }}'.includes(searchQuery) || '{{ $shipment->receiverCustomer?->phone }}'.includes(searchQuery) || '{{ $shipment->senderCustomer?->phone }}'.includes(searchQuery)"
                                class="transition-colors duration-200 hover:bg-slate-50/50">

                                {{-- 1. السند والتاريخ --}}
                                <td class="px-6 py-4">
                                    <div class="flex gap-3 items-center">
                                        <div
                                            class="flex justify-center items-center w-10 h-10 rounded-xl border bg-slate-50 border-slate-100">
                                            <span
                                                class="material-symbols-outlined text-slate-400 text-[20px]">package_2</span>
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-800">{{ $shipment->bond_number }}</div>
                                            <div
                                                class="flex gap-1 items-center mt-0.5 text-[11px] font-bold text-slate-400">
                                                <span class="material-symbols-outlined text-[12px]">schedule</span>
                                                {{ $shipment->created_at->format('Y/m/d - H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- 2. المرسل --}}
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 truncate max-w-[150px]">
                                        {{ $shipment->senderCustomer?->name ?? 'عميل نقدي' }}
                                    </div>
                                    <div class="text-[11px] font-bold text-slate-400 mt-0.5 dir-ltr text-right">
                                        <x-phone-number :value="$shipment->senderCustomer?->phone ?? '---'"
                                            class="text-[11px] font-bold text-gray-500 dark:text-bodydark" />
                                    </div>
                                </td>

                                {{-- 3. المستلم والوجهة --}}
                                <td class="px-6 py-4">
                                    <div class="font-black text-slate-800 truncate max-w-[150px]">
                                        {{ $shipment->receiverCustomer?->name ?? 'عميل نقدي' }}
                                    </div>
                                    <div class="flex gap-1 items-center mt-0.5 text-[11px] font-bold text-slate-500">
                                        <span class="text-primary truncate max-w-[150px]">
                                            @if ($shipment->receiverOfficeBranch)
                                                {{ $shipment->receiverOfficeBranch->office->name ?? 'مكتب خارجي' }} -
                                                {{ $shipment->receiverOfficeBranch->name }}
                                            @elseif($shipment->receiverBranch)
                                                @if ($shipment->senderBranch?->app_id == $shipment->receiverBranch->app_id)
                                                    <span class="text-emerald-500">مكتبنا</span> -
                                                    {{ $shipment->receiverBranch->name }}
                                                @else
                                                    {{ $shipment->receiverBranch->app->name ?? 'مكتب موثوق' }} -
                                                    {{ $shipment->receiverBranch->name }}
                                                @endif
                                            @else
                                                غير محدد
                                            @endif
                                        </span>
                                    </div>
                                    <div class="text-[10px] font-bold text-slate-400 mt-0.5 dir-ltr text-right">
                                        <x-phone-number :value="$shipment->receiverCustomer?->phone ?? '---'"
                                            class="text-[11px] font-bold text-gray-500 dark:text-bodydark" />
                                    </div>
                                </td>

                                {{-- 4. المحتوى --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1.5">
                                        <span
                                            class="inline-flex w-fit text-[11px] font-black text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                                            @if ($shipment->package_type == 'carton')
                                                كرتون
                                            @elseif($shipment->package_type == 'bag')
                                                كيس
                                            @elseif($shipment->package_type == 'envelope')
                                                مغلف
                                            @else
                                                أخرى
                                            @endif
                                            @if ($shipment->weight > 0)
                                                <span class="mr-1 text-slate-500">({{ $shipment->weight }} كجم)</span>
                                            @endif
                                        </span>
                                        @if ($shipment->no_gallons_honey > 0 || $shipment->no_honey_jars > 0)
                                            <div class="text-[11px] font-bold text-amber-600 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[14px]">local_drink</span>
                                                @if ($shipment->no_gallons_honey > 0)
                                                    {{ $shipment->no_gallons_honey }} دباب
                                                @endif
                                                @if ($shipment->no_gallons_honey > 0 && $shipment->no_honey_jars > 0)
                                                    +
                                                @endif
                                                @if ($shipment->no_honey_jars > 0)
                                                    {{ $shipment->no_honey_jars }} قوارير
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- 5. المطلوب --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-black {{ $shipment->payment_method == 'prepaid' ? 'text-emerald-500' : 'text-amber-500' }}">
                                            {{ number_format($shipment->total_amount, 0) }} ريال
                                        </span>
                                        <span class="text-[11px] font-bold text-slate-500 mt-0.5">
                                            @if ($shipment->payment_method == 'prepaid')
                                                مدفوع مقدماً
                                            @elseif($shipment->payment_method == 'cod')
                                                الدفع عند الاستلام
                                            @elseif($shipment->payment_method == 'partial_payment')
                                                دفع جزئي <span class="text-rose-500">(المتبقي:
                                                    {{ number_format($shipment->total_amount - $shipment->partial_amount, 0) }})</span>
                                            @else
                                                آجل
                                            @endif
                                        </span>
                                    </div>
                                </td>

                                {{-- 6. الحالة الذكية --}}
                                <td class="px-6 py-4 text-center">
                                    <x-shipment-status :status="$shipment->status" />
                                </td>

                                {{-- 7. الإجراءات (النقاط الثلاث) --}}
                                <td class="px-6 py-4 text-center">
                                    <div x-data="{ openMenu: false }" class="inline-block relative text-right">
                                        <button type="button" @click="openMenu = !openMenu" @click.away="openMenu = false"
                                            class="flex justify-center items-center w-8 h-8 rounded-full transition-colors text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/20">
                                            <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                        </button>

                                        <div x-show="openMenu" x-transition.opacity.duration.200ms x-cloak
                                            class="absolute top-full left-0 mt-2 w-48 bg-white rounded-2xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.15)] border border-slate-100 z-50 overflow-hidden py-1.5 text-right">

                                            <a href="{{ route('shipment.outgoing.show', $shipment->id) }}"
                                                class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold transition-colors text-slate-600 hover:bg-slate-50 hover:text-primary">
                                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                                                التفاصيل
                                            </a>

                                            @if (auth()->user()->type === 'admin' || $shipment->status === 'pending')
                                                <a href="{{ route('shipment.outgoing.edit', $shipment->id) }}"
                                                    class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold transition-colors text-slate-600 hover:bg-slate-50 hover:text-primary">
                                                    <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                                    تعديل البيانات
                                                </a>
                                            @endif

                                            @if (!in_array($shipment->status, ['returned', 'cancelled']))
                                                <a href="{{ route('receipt.generate', ['type' => 'sender', 'id' => $shipment->uuid]) }}"
                                                    target="_blank"
                                                    class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold transition-colors text-slate-600 hover:bg-slate-50 hover:text-primary">
                                                    <span class="material-symbols-outlined text-[18px]">print</span>
                                                    طباعة السند
                                                </a>

                                                <div class="mx-3 my-1 h-px bg-slate-100"></div>

                                                @if ($shipment->senderCustomer && $shipment->senderCustomer->phone)
                                                    <a href="{{ $shipment->sender_whatsapp_link }}" target="_blank"
                                                        class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold transition-colors text-slate-600 hover:bg-emerald-50 hover:text-emerald-700">
                                                        <svg class="w-[16px] h-[16px] fill-[#25D366]" viewBox="0 0 24 24"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                                        </svg>
                                                        إرسال للمرسل
                                                    </a>
                                                @endif

                                                @if ($shipment->receiverCustomer && $shipment->receiverCustomer->phone)
                                                    <a href="{{ $shipment->receiver_whatsapp_link }}" target="_blank"
                                                        class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold transition-colors text-slate-600 hover:bg-emerald-50 hover:text-emerald-700">
                                                        <svg class="w-[16px] h-[16px] fill-[#25D366]" viewBox="0 0 24 24"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                                        </svg>
                                                        إرسال للمستلم
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="flex flex-col justify-center items-center">
                                        <div class="relative mb-4">
                                            <div class="absolute inset-0 rounded-full blur-xl bg-primary/10"></div>
                                            <div
                                                class="flex relative z-10 justify-center items-center w-16 h-16 rounded-2xl border shadow-sm bg-slate-50 border-slate-100">
                                                <span
                                                    class="material-symbols-outlined text-[32px] text-slate-300">inbox</span>
                                            </div>
                                        </div>
                                        <h3 class="text-base font-black text-slate-700 font-headline">لا توجد طرود مرسلة
                                        </h3>
                                        <p class="mt-1 text-xs font-bold text-slate-400">لم نعثر على أي طرود تطابق بحثك
                                            حالياً.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- الترقيم --}}
           @if (method_exists($shipments, 'hasPages') && $shipments->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $shipments->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
