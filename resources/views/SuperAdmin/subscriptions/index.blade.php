@extends('layouts.app')
@section('title', 'إدارة الاشتراكات | المشرف العام')

@section('content')
<div x-data="subscriptionsManager()" class="space-y-6 md:space-y-8 font-body">

    {{-- الإشعارات (Toast) --}}
    <div x-cloak x-show="toast.show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-[-1rem]" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-[-1rem]" 
        class="fixed top-6 left-1/2 z-[9999] flex -translate-x-1/2 items-center gap-3 rounded-2xl px-6 py-3.5 text-sm font-bold text-white shadow-xl" 
        :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'">
        <span class="material-symbols-outlined text-[20px]" x-text="toast.type === 'success' ? 'check_circle' : 'error'"></span>
        <span x-text="toast.message"></span>
    </div>

    {{-- ترويسة الصفحة --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="material-symbols-outlined text-amber-500 text-[32px]">workspace_premium</span>
                <h1 class="text-3xl font-black text-slate-900 font-headline dark:text-white">إدارة الاشتراكات</h1>
            </div>
            <p class="text-slate-500 dark:text-slate-400 font-medium text-sm pr-11">متابعة وإدارة جميع اشتراكات المكاتب والشركات في المنصة</p>
        </div>
    </div>

    {{-- بطاقات الإحصائيات --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <!-- الإيرادات -->
        <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-[0_8px_30px_rgba(0,0,0,0.04)] border border-slate-100 dark:bg-slate-800 dark:border-slate-700 transition-transform hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-500 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 shadow-inner shrink-0">
                    <span class="material-symbols-outlined text-[28px]">account_balance_wallet</span>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400 mb-1">إجمالي الإيرادات</p>
                    <p class="text-2xl lg:text-3xl font-black text-slate-900 font-headline dark:text-white truncate">
                        {{ number_format($totalRevenue, 0) }} <span class="text-sm lg:text-base text-slate-400 font-bold">ر.ي</span>
                    </p>
                </div>
            </div>
            <div class="absolute -left-6 -top-6 h-24 w-24 rounded-full bg-amber-500/5 blur-2xl pointer-events-none"></div>
        </div>

        <!-- النشطة -->
        <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-[0_8px_30px_rgba(0,0,0,0.04)] border border-slate-100 dark:bg-slate-800 dark:border-slate-700 transition-transform hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 shadow-inner shrink-0">
                    <span class="material-symbols-outlined text-[28px]">verified_user</span>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400 mb-1">اشتراكات نشطة</p>
                    <p class="text-2xl lg:text-3xl font-black text-emerald-600 font-headline dark:text-emerald-400">{{ $activeCount }}</p>
                </div>
            </div>
        </div>

        <!-- المنتهية -->
        <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-[0_8px_30px_rgba(0,0,0,0.04)] border border-slate-100 dark:bg-slate-800 dark:border-slate-700 transition-transform hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-500 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20 shadow-inner shrink-0">
                    <span class="material-symbols-outlined text-[28px]">history_toggle_off</span>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400 mb-1">اشتراكات منتهية</p>
                    <p class="text-2xl lg:text-3xl font-black text-rose-600 font-headline dark:text-rose-400">{{ $expiredCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- أدوات الفلترة والبحث --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center rounded-2xl bg-white p-4 shadow-[0_8px_30px_rgba(0,0,0,0.04)] border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
        
        <!-- الفلتر (على اليمين) -->
        <div class="flex items-center gap-3 shrink-0 w-full sm:w-auto">
            <div class="relative group w-full sm:w-56">
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400 pointer-events-none">filter_list</span>
                <select x-model="statusFilter" class="appearance-none w-full rounded-xl border-0 bg-slate-50 py-3.5 pr-10 pl-10 text-sm font-bold text-slate-700 ring-1 ring-inset ring-slate-200 focus:bg-white focus:ring-2 focus:ring-amber-500 transition-all dark:bg-slate-900 dark:text-white dark:ring-slate-700 cursor-pointer">
                    <option value="all">جميع الحالات</option>
                    <option value="active">النشطة فقط</option>
                    <option value="expired">المنتهية فقط</option>
                    <option value="cancelled">الملغية فقط</option>
                </select>
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400 pointer-events-none">expand_more</span>
            </div>
        </div>

        <!-- البحث (على اليسار) -->
        <div class="relative flex-1 w-full group">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[22px] text-slate-400 group-focus-within:text-amber-500 transition-colors">search</span>
            <input type="text" x-model="search" placeholder="ابحث باسم المكتب أو الباقة..."
                   class="w-full rounded-xl border-0 bg-slate-50 py-3.5 pr-4 pl-12 text-sm font-bold text-slate-700 ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-amber-500 transition-all dark:bg-slate-900 dark:text-white dark:ring-slate-700" />
        </div>

    </div>

    {{-- جدول البيانات --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-[0_8px_30px_rgba(0,0,0,0.04)] border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-right min-w-[900px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 dark:bg-slate-900/50 dark:border-slate-700">
                        <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">المكتب / الشركة</th>
                        <th class="px-6 py-4 text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">نوع الباقة</th>
                        <th class="px-6 py-4 text-center text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">المبلغ المدفوع</th>
                        <th class="px-6 py-4 text-center text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">البداية</th>
                        <th class="px-6 py-4 text-center text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">الانتهاء</th>
                        <th class="px-6 py-4 text-center text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">الحالة</th>
                        <th class="px-6 py-4 text-center text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                @forelse($subscriptions as $sub)
                <tr x-show="filterRow('{{ addslashes($sub->app?->name ?? '') }}', '{{ addslashes($sub->package?->name ?? '') }}', '{{ $sub->status }}')"
                    class="transition-colors hover:bg-slate-50/60 dark:hover:bg-slate-700/20 group" id="sub-{{ $sub->id }}">
                    
                    <!-- المكتب -->
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-white font-headline font-black text-lg shadow-sm" style="background:{{ $sub->app?->color ?? '#f59e0b' }}">
                                {{ mb_substr($sub->app?->name ?? 'م',0,1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $sub->app?->name ?? '—' }}</span>
                                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                                    <span class="material-symbols-outlined text-[14px]">pin</span> ID: {{ $sub->id }}
                                </span>
                            </div>
                        </div>
                    </td>

                    <!-- الباقة -->
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                            <span class="material-symbols-outlined text-[16px] text-amber-500">local_mall</span>
                            {{ $sub->package?->name ?? '—' }}
                        </span>
                    </td>

                    <!-- المبلغ -->
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-black font-headline text-slate-900 dark:text-white">{{ number_format($sub->price_paid, 0) }}</span>
                        <span class="text-xs font-bold text-slate-500">ر.ي</span>
                    </td>

                    <!-- تاريخ البداية -->
                    <td class="px-6 py-4 text-center text-sm font-bold text-slate-600 dark:text-slate-400">
                        {{ $sub->starts_at?->format('Y/m/d') ?? '—' }}
                    </td>

                    <!-- تاريخ النهاية -->
                    <td class="px-6 py-4 text-center text-sm font-bold {{ $sub->ends_at?->isPast() ? 'text-rose-500' : 'text-slate-600 dark:text-slate-400' }}">
                        {{ $sub->ends_at?->format('Y/m/d') ?? '—' }}
                    </td>

                    <!-- الحالة -->
                    <td class="px-6 py-4 text-center">
                        @if($sub->status === 'active' && $sub->ends_at?->isFuture())
                            <div class="inline-flex flex-col items-center justify-center">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200/50 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    نشط
                                </span>
                                {{-- 💡 تم حل مشكلة الأرقام العشرية هنا باستخدام intval() --}}
                                <span class="text-[10px] font-bold text-slate-500 mt-1">متبقي {{ intval(now()->diffInDays($sub->ends_at)) }} يوم</span>
                            </div>
                        @elseif($sub->status === 'cancelled')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 border border-slate-200/50 dark:bg-slate-700 dark:text-slate-300 dark:border-slate-600">
                                <span class="material-symbols-outlined text-[14px]">block</span> ملغي
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 border border-rose-200/50 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20">
                                <span class="material-symbols-outlined text-[14px]">warning</span> منتهي
                            </span>
                        @endif
                    </td>

                    <!-- الإجراءات -->
                    <td class="px-6 py-4 text-center">
                        <button @click="openRenew({{ $sub->id }}, '{{ $sub->status }}')" 
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-amber-50 px-4 py-2 text-xs font-bold text-amber-600 transition-all duration-200 hover:bg-amber-500 hover:text-white hover:shadow-md focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:bg-amber-500/10 dark:text-amber-400 dark:hover:bg-amber-500 dark:hover:text-white w-full sm:w-auto">
                            تحديث <span class="material-symbols-outlined text-[18px]">update</span>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="h-20 w-20 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-[40px] text-slate-300 dark:text-slate-600">inventory_2</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1">لا توجد اشتراكات</h3>
                            <p class="text-xs font-medium text-slate-500">لم يتم العثور على أي بيانات مطابقة لبحثك.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 bg-slate-50/50 px-6 py-4 dark:border-slate-700 dark:bg-slate-800/50">
            {{ $subscriptions->links() }}
        </div>
    </div>

    {{-- نافذة التجديد المنبثقة (Modal) --}}
    <div x-cloak x-show="renewModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-0">
        <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="renewModal = false"></div>
        
        <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="relative w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-900/5 dark:bg-slate-800 dark:ring-white/10">
            
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 dark:border-slate-700 dark:bg-slate-800/50 flex items-center justify-between">
                <div class="flex items-center gap-2 text-amber-500">
                    <span class="material-symbols-outlined text-[24px]">published_with_changes</span>
                    <h3 class="text-lg font-black text-slate-900 font-headline dark:text-white">تحديث الاشتراك</h3>
                </div>
                <button type="button" @click="renewModal = false" class="text-slate-400 hover:text-slate-600 transition-colors bg-white rounded-full p-1 shadow-sm border border-slate-100 dark:bg-slate-700 dark:border-slate-600">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form @submit.prevent="submitRenew()" class="p-6">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">الحالة الجديدة</label>
                        <div class="relative">
                            <select x-model="renewForm.status" class="appearance-none w-full rounded-xl border-0 bg-slate-50 py-3.5 pr-10 pl-4 text-sm font-bold text-slate-700 ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-amber-500 dark:bg-slate-900 dark:text-white dark:ring-slate-700">
                                <option value="active">نشط (فعال)</option>
                                <option value="expired">منتهي</option>
                                <option value="cancelled">ملغي</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">toggle_on</span>
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <div x-show="renewForm.status === 'active'" x-transition>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">مدة التمديد <span class="text-xs text-slate-400 font-medium">(بالأيام)</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">calendar_add_on</span>
                            <input type="number" x-model="renewForm.extend_days" min="1" placeholder="مثال: 30" 
                                   class="w-full rounded-xl border-0 bg-slate-50 py-3.5 pr-10 pl-4 text-sm font-bold text-slate-700 ring-1 ring-inset ring-slate-200 focus:ring-2 focus:ring-amber-500 dark:bg-slate-900 dark:text-white dark:ring-slate-700 text-left dir-ltr" />
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3">
                    <button type="button" @click="renewModal = false" class="rounded-xl bg-white px-5 py-3 text-sm font-bold text-slate-700 ring-1 ring-inset ring-slate-200 hover:bg-slate-50 transition-colors dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-600 dark:hover:bg-slate-700">
                        إلغاء الأمر
                    </button>
                    <button type="submit" :disabled="renewLoading" class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-amber-500/30 transition-all hover:bg-amber-600 hover:shadow-amber-500/40 disabled:opacity-50 disabled:shadow-none min-w-[120px]">
                        <span x-show="!renewLoading">حفظ التغييرات</span>
                        <span x-show="renewLoading" class="h-5 w-5 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function subscriptionsManager() {
    return {
        search: '', statusFilter: 'all',
        renewModal: false, renewLoading: false, renewSubId: null,
        renewForm: { status: 'active', extend_days: 30 },
        toast: { show: false, message: '', type: 'success' },
        showToast(msg, type='success') { 
            this.toast = { show: true, message: msg, type }; 
            setTimeout(() => this.toast.show = false, 3500); 
        },
        filterRow(appName, pkgName, status) {
            const q = this.search.toLowerCase();
            const matchSearch = !q || appName.toLowerCase().includes(q) || pkgName.toLowerCase().includes(q);
            const matchStatus = this.statusFilter === 'all' || this.statusFilter === status;
            return matchSearch && matchStatus;
        },
        openRenew(id, status) {
            this.renewSubId = id;
            this.renewForm.status = status;
            this.renewForm.extend_days = 30;
            this.renewModal = true;
        },
        async submitRenew() {
            this.renewLoading = true;
            try {
                const res = await fetch(`/superadmin/subscriptions/${this.renewSubId}/update-status`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.renewForm)
                });
                const data = await res.json();
                if (data.status === 'success') { 
                    this.showToast(data.message); 
                    this.renewModal = false; 
                    setTimeout(() => location.reload(), 1000); 
                }
                else { this.showToast('حدث خطأ', 'error'); }
            } catch (e) { 
                console.error('submitRenew error:', e); 
                this.showToast('حدث خطأ في الاتصال', 'error'); 
            }
            this.renewLoading = false;
        }
    };
}
</script>
@endsection