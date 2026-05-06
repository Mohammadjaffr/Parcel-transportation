@extends('layouts.app')
@section('title', 'إدارة الاشتراكات | المشرف العام')

@section('content')
<div x-data="subscriptionsManager()" class="space-y-6">

    {{-- Toast --}}
    <div x-cloak x-show="toast.show" x-transition class="fixed top-6 left-1/2 z-[9999] -translate-x-1/2 rounded-xl px-6 py-3 text-sm font-bold text-white shadow-lg" :class="toast.type==='success'?'bg-emerald-500':'bg-red-500'">
        <span x-text="toast.message"></span>
    </div>

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white">إدارة الاشتراكات</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">متابعة وإدارة جميع اشتراكات المنصة</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400">
                    <span class="material-symbols-outlined text-[24px]">payments</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">إجمالي الإيرادات</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($totalRevenue, 0) }} <span class="text-sm text-gray-400">ر.ي</span></p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <span class="material-symbols-outlined text-[24px]">check_circle</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">اشتراكات نشطة</p>
                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $activeCount }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400">
                    <span class="material-symbols-outlined text-[24px]">cancel</span>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">اشتراكات منتهية</p>
                    <p class="text-2xl font-black text-red-600 dark:text-red-400">{{ $expiredCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-[20px] text-gray-400">search</span>
            <input type="text" x-model="search" placeholder="ابحث باسم المكتب أو الباقة..."
                   class="w-full rounded-xl border-0 bg-gray-50 py-2.5 pr-10 pl-4 text-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-white dark:ring-gray-600" />
        </div>
        <select x-model="statusFilter" class="rounded-xl border-0 bg-gray-50 py-2.5 px-4 text-sm font-bold text-gray-700 ring-1 ring-gray-200 focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-white dark:ring-gray-600">
            <option value="all">جميع الحالات</option>
            <option value="active">نشط</option>
            <option value="expired">منتهي</option>
            <option value="cancelled">ملغي</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead><tr class="bg-gray-50/50 dark:bg-gray-900/30">
                    <th class="px-6 py-3 text-right text-xs font-black uppercase text-gray-500 dark:text-gray-400">المكتب</th>
                    <th class="px-6 py-3 text-right text-xs font-black uppercase text-gray-500 dark:text-gray-400">الباقة</th>
                    <th class="px-6 py-3 text-center text-xs font-black uppercase text-gray-500 dark:text-gray-400">المبلغ</th>
                    <th class="px-6 py-3 text-center text-xs font-black uppercase text-gray-500 dark:text-gray-400">الحالة</th>
                    <th class="px-6 py-3 text-center text-xs font-black uppercase text-gray-500 dark:text-gray-400">تاريخ الانتهاء</th>
                    <th class="px-6 py-3 text-center text-xs font-black uppercase text-gray-500 dark:text-gray-400">الإجراءات</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                @forelse($subscriptions as $sub)
                <tr x-show="filterRow('{{ addslashes($sub->app?->name ?? '') }}', '{{ addslashes($sub->package?->name ?? '') }}', '{{ $sub->status }}')"
                    class="transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-700/20" id="sub-{{ $sub->id }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg text-white text-xs font-black" style="background:{{ $sub->app?->color ?? '#6366f1' }}">{{ mb_substr($sub->app?->name ?? 'م',0,1) }}</div>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $sub->app?->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-700 dark:text-gray-300">{{ $sub->package?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-900 dark:text-white">{{ number_format($sub->price_paid, 0) }} ر.ي</td>
                    <td class="px-6 py-4 text-center">
                        @if($sub->status === 'active' && $sub->ends_at?->isFuture())
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>نشط</span>
                        @elseif($sub->status === 'cancelled')
                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-400"><span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>ملغي</span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700 dark:bg-red-500/10 dark:text-red-400"><span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>منتهي</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center text-sm {{ $sub->ends_at?->isPast() ? 'text-red-500 font-bold' : 'text-gray-500' }} dark:text-gray-400">{{ $sub->ends_at?->format('Y/m/d') ?? '—' }}</td>
                    <td class="px-6 py-4 text-center">
                        <button @click="openRenew({{ $sub->id }}, '{{ $sub->status }}')" class="inline-flex items-center gap-1 rounded-lg bg-primary/5 px-3 py-1.5 text-xs font-bold text-primary transition hover:bg-primary/10">
                            <span class="material-symbols-outlined text-[16px]">autorenew</span> تجديد
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center"><span class="material-symbols-outlined text-[48px] text-gray-300">card_membership</span><p class="mt-2 text-sm text-gray-400">لا توجد اشتراكات</p></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 px-6 py-4 dark:border-gray-700">{{ $subscriptions->links() }}</div>
    </div>

    {{-- Renew Modal --}}
    <div x-cloak x-show="renewModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
        <div x-transition.opacity class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="renewModal = false"></div>
        <div x-transition class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800">
            <h3 class="text-lg font-black text-gray-900 dark:text-white mb-4">تجديد / تحديث الاشتراك</h3>
            <form @submit.prevent="submitRenew()">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">الحالة الجديدة</label>
                        <select x-model="renewForm.status" class="w-full rounded-xl border-0 bg-gray-50 py-2.5 px-4 text-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-white dark:ring-gray-600">
                            <option value="active">نشط</option>
                            <option value="expired">منتهي</option>
                            <option value="cancelled">ملغي</option>
                        </select>
                    </div>
                    <div x-show="renewForm.status === 'active'">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">تمديد (بالأيام)</label>
                        <input type="number" x-model="renewForm.extend_days" min="1" placeholder="30" class="w-full rounded-xl border-0 bg-gray-50 py-2.5 px-4 text-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-white dark:ring-gray-600" />
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="renewModal = false" class="rounded-xl px-5 py-2.5 text-sm font-bold text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50 dark:text-gray-300 dark:ring-gray-600">إلغاء</button>
                    <button type="submit" :disabled="renewLoading" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:shadow-md disabled:opacity-50">
                        <span x-show="renewLoading" class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                        تحديث
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
        showToast(msg, type='success') { this.toast = { show: true, message: msg, type }; setTimeout(() => this.toast.show = false, 3000); },
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
                if (data.status === 'success') { this.showToast(data.message); this.renewModal = false; setTimeout(() => location.reload(), 1000); }
                else { this.showToast('حدث خطأ', 'error'); }
            } catch (e) { console.error('submitRenew error:', e); this.showToast('حدث خطأ في الاتصال', 'error'); }
            this.renewLoading = false;
        }
    };
}
</script>
@endsection
