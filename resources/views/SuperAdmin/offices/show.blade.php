@extends('layouts.app')
@section('title', $app->name . ' | تفاصيل المكتب')

@section('content')
<div x-data="tenantProfile()" class="space-y-6">

    {{-- Toast --}}
    <div x-cloak x-show="toast.show" x-transition class="fixed top-6 left-1/2 z-[9999] -translate-x-1/2 rounded-xl px-6 py-3 text-sm font-bold text-white shadow-lg" :class="toast.type==='success'?'bg-emerald-500':'bg-red-500'">
        <span x-text="toast.message"></span>
    </div>

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
        <a href="{{ route('superadmin.dashboard') }}" class="hover:text-primary">لوحة التحكم</a>
        <span class="material-symbols-outlined text-[14px]">chevron_left</span>
        <a href="{{ route('superadmin.offices.index') }}" class="hover:text-primary">المكاتب</a>
        <span class="material-symbols-outlined text-[14px]">chevron_left</span>
        <span class="font-bold text-gray-900 dark:text-white">{{ $app->name }}</span>
    </div>

    {{-- Profile Card --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
        <div class="h-2" style="background:{{ $app->color ?? '#6366f1' }}"></div>
        <div class="p-6">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl text-white text-2xl font-black shadow-lg" style="background:{{ $app->color ?? '#6366f1' }}">
                        {{ mb_substr($app->name ?? 'م', 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-gray-900 dark:text-white">{{ $app->name ?? 'بدون اسم' }}</h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ID: {{ $app->id }} · تاريخ التسجيل: {{ $app->created_at->format('Y/m/d') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($app->is_active)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> نشط
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-4 py-2 text-sm font-bold text-red-700 dark:bg-red-500/10 dark:text-red-400">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span> معطل
                        </span>
                    @endif
                </div>
            </div>

            {{-- Stats Row --}}
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900/50">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">الفروع</p>
                    <p class="mt-1 text-2xl font-black text-gray-900 dark:text-white">{{ $app->branches_count }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900/50">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">المستخدمين</p>
                    <p class="mt-1 text-2xl font-black text-gray-900 dark:text-white">{{ $app->users_count }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900/50">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">الباقة الحالية</p>
                    <p class="mt-1 text-lg font-black text-gray-900 dark:text-white">{{ $app->currentSubscription?->package?->name ?? '—' }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900/50">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">انتهاء الاشتراك</p>
                    <p class="mt-1 text-lg font-black {{ $app->currentSubscription?->ends_at?->isPast() ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                        {{ $app->currentSubscription?->ends_at?->format('Y/m/d') ?? '—' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Services Management --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
        <div class="border-b border-gray-100 px-6 py-5 dark:border-gray-700">
            <h2 class="text-lg font-black text-gray-900 dark:text-white">إدارة الخدمات</h2>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">تفعيل أو تعطيل الخدمات (الموديولات) لهذا المكتب</p>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
            @forelse($services as $service)
                @php
                    $isActive = $app->services->where('id', $service->id)->first()?->pivot?->is_active ?? false;
                @endphp
            <div class="flex items-center justify-between px-6 py-4 transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-700/20">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/5 text-primary dark:bg-primary/10">
                        <span class="material-symbols-outlined text-[22px]">extension</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $service->name }}</p>
                        <p class="text-xs text-gray-400">{{ $service->description ?? $service->slug }}</p>
                    </div>
                </div>
                <label class="relative inline-flex h-6 w-11 cursor-pointer items-center rounded-full transition-colors duration-200"
                       :class="serviceStates[{{ $service->id }}] ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600'">
                    <input type="checkbox" class="peer sr-only"
                           :checked="serviceStates[{{ $service->id }}]"
                           @change="toggleFeature({{ $service->id }}, $event.target.checked)" />
                    <span class="absolute h-4 w-4 rounded-full bg-white shadow transition-all duration-200"
                          :class="serviceStates[{{ $service->id }}] ? 'right-1' : 'left-1'"></span>
                </label>
            </div>
            @empty
            <div class="px-6 py-12 text-center">
                <span class="material-symbols-outlined text-[48px] text-gray-300">extension_off</span>
                <p class="mt-2 text-sm text-gray-400">لا توجد خدمات متاحة في النظام</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function tenantProfile() {
    return {
        appId: {{ $app->id }},
        toast: { show: false, message: '', type: 'success' },
        serviceStates: {
            @foreach($services as $service)
                @php $isActive = $app->services->where('id', $service->id)->first()?->pivot?->is_active ?? false; @endphp
                {{ $service->id }}: {{ $isActive ? 'true' : 'false' }},
            @endforeach
        },
        showToast(msg, type = 'success') {
            this.toast = { show: true, message: msg, type };
            setTimeout(() => this.toast.show = false, 3000);
        },
        async toggleFeature(serviceId, isActive) {
            try {
                const res = await fetch(`/superadmin/offices/${this.appId}/toggle-service`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ service_id: serviceId, is_active: isActive })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    this.serviceStates[serviceId] = isActive;
                    this.showToast(data.message);
                } else {
                    this.serviceStates[serviceId] = !isActive;
                    this.showToast('حدث خطأ', 'error');
                }
            } catch (e) {
                console.error('toggleFeature error:', e);
                this.serviceStates[serviceId] = !isActive;
                this.showToast('حدث خطأ أثناء تحديث الخدمة', 'error');
            }
        }
    };
}
</script>
@endsection
