@extends('SuperAdmin.layouts.app')
@section('title', 'إدارة الاشتراكات | المشرف العام')

@section('content')
<div x-data="subscriptionsManager()" class="space-y-6">

    {{-- Toast --}}
    <div x-cloak x-show="toast.show" x-transition 
         class="fixed top-6 left-1/2 z-[9999] flex -translate-x-1/2 items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold text-white shadow-lg" 
         :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-red-500'">
        <span class="material-symbols-outlined" x-text="toast.type === 'success' ? 'check_circle' : 'error'"></span>
        <span x-text="toast.message"></span>
    </div>

    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex gap-3 items-center">
            <div class="flex justify-center items-center w-12 h-12 text-white bg-violet-500 rounded-xl shadow-sm shadow-violet-500/20">
                <span class="material-symbols-outlined text-[28px]">card_membership</span>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 font-headline">إدارة الاشتراكات</h1>
                <p class="mt-1 text-sm font-medium text-slate-500">متابعة وتجديد اشتراكات المكاتب</p>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 lg:gap-6">
        <div class="overflow-hidden relative p-6 bg-white rounded-2xl border shadow-sm transition-all border-slate-100 hover:shadow-md hover:-translate-y-1">
            <div class="flex gap-4 items-center">
                <div class="flex justify-center items-center w-14 h-14 text-amber-600 bg-amber-50 rounded-2xl border border-amber-100 shadow-inner shrink-0">
                    <span class="material-symbols-outlined text-[28px]">payments</span>
                </div>
                <div>
                    <p class="mb-1 text-sm font-bold text-slate-500">إجمالي الإيرادات</p>
                    <p class="text-2xl font-black truncate text-slate-900 font-headline lg:text-3xl">
                        {{ number_format($totalRevenue, 0) }} <span class="text-sm font-bold text-slate-400">ر.ي</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="overflow-hidden relative p-6 bg-white rounded-2xl border shadow-sm transition-all border-slate-100 hover:shadow-md hover:-translate-y-1">
            <div class="flex gap-4 items-center">
                <div class="flex justify-center items-center w-14 h-14 text-emerald-600 bg-emerald-50 rounded-2xl border border-emerald-100 shadow-inner shrink-0">
                    <span class="material-symbols-outlined text-[28px]">verified_user</span>
                </div>
                <div>
                    <p class="mb-1 text-sm font-bold text-slate-500">اشتراكات نشطة</p>
                    <p class="text-2xl font-black text-emerald-600 font-headline lg:text-3xl">{{ $activeCount }}</p>
                </div>
            </div>
        </div>

        <div class="overflow-hidden relative p-6 bg-white rounded-2xl border shadow-sm transition-all border-slate-100 hover:shadow-md hover:-translate-y-1">
            <div class="flex gap-4 items-center">
                <div class="flex justify-center items-center w-14 h-14 text-rose-600 bg-rose-50 rounded-2xl border border-rose-100 shadow-inner shrink-0">
                    <span class="material-symbols-outlined text-[28px]">history_toggle_off</span>
                </div>
                <div>
                    <p class="mb-1 text-sm font-bold text-slate-500">اشتراكات منتهية</p>
                    <p class="text-2xl font-black text-rose-600 font-headline lg:text-3xl">{{ $expiredCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col gap-3 p-4 bg-white rounded-2xl border shadow-sm sm:flex-row sm:items-center border-slate-100">
        <div class="relative flex-1 w-full group">
            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-[22px] text-slate-400 group-focus-within:text-primary transition-colors">search</span>
            <input type="text" x-model="search" placeholder="ابحث باسم المكتب أو الباقة..."
                   class="py-3 pr-12 pl-4 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition-all bg-slate-50 text-slate-700 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary" />
        </div>
        <div class="relative w-full sm:w-64 shrink-0">
            <select x-model="statusFilter" class="py-3 pr-4 pl-10 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition-all appearance-none cursor-pointer bg-slate-50 text-slate-700 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-primary">
                <option value="all">جميع الحالات</option>
                <option value="active">النشطة فقط</option>
                <option value="expired">المنتهية فقط</option>
                <option value="cancelled">الملغية فقط</option>
            </select>
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400 pointer-events-none">expand_more</span>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden bg-white rounded-2xl border shadow-sm border-slate-100">
        <div class="overflow-x-auto scrollbar-hide">
            <table class="w-full min-w-[900px] text-right">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="px-6 py-4 text-xs font-black tracking-wider uppercase text-slate-500">المكتب / الشركة</th>
                        <th class="px-6 py-4 text-xs font-black tracking-wider uppercase text-slate-500">الباقة</th>
                        <th class="px-6 py-4 text-xs font-black tracking-wider text-center uppercase text-slate-500">المبلغ المدفوع</th>
                        <th class="px-6 py-4 text-xs font-black tracking-wider text-center uppercase text-slate-500">البداية</th>
                        <th class="px-6 py-4 text-xs font-black tracking-wider text-center uppercase text-slate-500">الانتهاء</th>
                        <th class="px-6 py-4 text-xs font-black tracking-wider text-center uppercase text-slate-500">الحالة</th>
                        <th class="px-6 py-4 text-xs font-black tracking-wider text-center uppercase text-slate-500">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                @forelse($subscriptions as $sub)
                <tr x-show="filterRow('{{ addslashes($sub->app?->name ?? '') }}', '{{ addslashes($sub->package?->name ?? '') }}', '{{ $sub->status }}')"
                    class="transition-colors hover:bg-slate-50/60" id="sub-{{ $sub->id }}">
                    
                    <td class="px-6 py-4">
                        <div class="flex gap-3 items-center">
                            <div class="flex justify-center items-center w-10 h-10 text-lg font-black text-white rounded-xl shadow-sm shrink-0 font-headline" style="background:{{ $sub->app?->color ?? '#f59e0b' }}">
                                {{ mb_substr($sub->app?->name ?? 'م',0,1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-900">{{ $sub->app?->name ?? '—' }}</span>
                                <span class="flex gap-1 items-center mt-0.5 text-xs font-medium text-slate-500">
                                    ID: {{ $sub->id }}
                                </span>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        <span class="inline-flex gap-1.5 items-center px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 text-slate-700">
                            {{ $sub->package?->name ?? '—' }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-black text-slate-900 font-headline">{{ number_format($sub->price_paid, 0) }}</span>
                        <span class="text-xs font-bold text-slate-400">ر.ي</span>
                    </td>

                    <td class="px-6 py-4 text-sm font-bold text-center text-slate-600">
                        {{ $sub->starts_at?->format('Y/m/d') ?? '—' }}
                    </td>

                    <td class="px-6 py-4 text-sm font-bold text-center {{ $sub->ends_at?->isPast() ? 'text-red-500' : 'text-slate-600' }}">
                        {{ $sub->ends_at?->format('Y/m/d') ?? '—' }}
                    </td>

                    <td class="px-6 py-4 text-center">
                        @if($sub->status === 'active' && $sub->ends_at?->isFuture())
                            <div class="inline-flex flex-col justify-center items-center">
                                <span class="inline-flex gap-1.5 items-center px-3 py-1 text-xs font-bold text-emerald-700 bg-emerald-50 rounded-full border border-emerald-100">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> نشط
                                </span>
                                <span class="text-[10px] font-bold text-slate-500 mt-1">متبقي {{ intval(now()->diffInDays($sub->ends_at)) }} يوم</span>
                            </div>
                        @elseif($sub->status === 'cancelled')
                            <span class="inline-flex gap-1.5 items-center px-3 py-1 text-xs font-bold rounded-full border bg-slate-100 text-slate-600 border-slate-200">
                                <span class="material-symbols-outlined text-[14px]">block</span> ملغي
                            </span>
                        @else
                            <span class="inline-flex gap-1.5 items-center px-3 py-1 text-xs font-bold text-red-700 bg-red-50 rounded-full border border-red-100">
                                <span class="material-symbols-outlined text-[14px]">warning</span> منتهي
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-center">
                        <button @click="openRenew({{ $sub->id }}, '{{ $sub->status }}')" 
                                class="inline-flex gap-1.5 justify-center items-center px-3 py-2 text-xs font-bold rounded-lg transition-colors bg-primary/5 text-primary hover:bg-primary hover:text-white">
                            تحديث <span class="material-symbols-outlined text-[16px]">update</span>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="flex flex-col justify-center items-center">
                            <div class="flex justify-center items-center mb-3 w-14 h-14 rounded-full bg-slate-50">
                                <span class="material-symbols-outlined text-[32px] text-slate-400">inventory_2</span>
                            </div>
                            <h3 class="mb-1 text-sm font-bold text-slate-900">لا توجد اشتراكات</h3>
                            <p class="text-xs font-medium text-slate-500">لم يتم العثور على أي بيانات مطابقة لبحثك.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $subscriptions->links() }}
        </div>
    </div>

    {{-- Renew Modal --}}
    <div x-cloak x-show="renewModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
        <div x-show="renewModal" x-transition.opacity class="absolute inset-0 backdrop-blur-sm bg-slate-900/60" @click="renewModal = false"></div>
        <div x-show="renewModal" x-transition class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl">
            
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="flex gap-2 items-center text-primary">
                    <span class="material-symbols-outlined text-[24px]">published_with_changes</span>
                    <h3 class="text-lg font-black text-slate-900 font-headline">تحديث الاشتراك</h3>
                </div>
                <button type="button" @click="renewModal = false" class="flex justify-center items-center w-8 h-8 bg-white rounded-full border transition-colors text-slate-400 hover:text-slate-700 hover:bg-slate-50 border-slate-200">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form @submit.prevent="submitRenew()" class="p-6">
                <div class="space-y-5">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-slate-700">الحالة الجديدة</label>
                        <div class="relative">
                            <select x-model="renewForm.status" class="py-3 pr-4 pl-10 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition-all appearance-none bg-slate-50 text-slate-700 ring-slate-200 focus:ring-2 focus:ring-primary">
                                <option value="active">نشط (فعال)</option>
                                <option value="expired">منتهي</option>
                                <option value="cancelled">ملغي</option>
                            </select>
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <div x-show="renewForm.status === 'active'" x-transition>
                        <label class="block mb-2 text-sm font-bold text-slate-700">مدة التمديد <span class="text-xs font-medium text-slate-400">(بالأيام)</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">calendar_add_on</span>
                            <input type="number" x-model="renewForm.extend_days" min="1" placeholder="مثال: 30" 
                                   class="py-3 pr-12 pl-4 w-full text-sm font-bold rounded-xl border-0 ring-1 ring-inset transition-all bg-slate-50 text-slate-700 ring-slate-200 focus:ring-2 focus:ring-primary" />
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 justify-end items-center pt-6 mt-6 border-t border-slate-100">
                    <button type="button" @click="renewModal = false" class="px-5 py-3 text-sm font-bold bg-white rounded-xl border transition text-slate-700 border-slate-200 hover:bg-slate-50">
                        إلغاء الأمر
                    </button>
                    <button type="submit" :disabled="renewLoading" class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-bold text-white transition-all shadow-lg rounded-xl bg-primary shadow-primary/30 hover:bg-primary-hover disabled:opacity-70 min-w-[130px]">
                        <span x-show="!renewLoading">حفظ التغييرات</span>
                        <span x-show="renewLoading" class="w-5 h-5 rounded-full border-2 border-white animate-spin border-t-transparent"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
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