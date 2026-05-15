@extends('SuperAdmin.layouts.app')

@section('title', $app->name . ' | تفاصيل المكتب')

@section('content')
<div x-data="tenantProfile()" class="space-y-6">

    {{-- Processing Dialog --}}
    <div x-cloak
         x-show="isProcessing"
         x-transition.opacity
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-950/50 backdrop-blur-sm">

        <div x-show="isProcessing"
             x-transition.scale.origin.center
             class="overflow-hidden w-full max-w-sm bg-white rounded-3xl border shadow-2xl border-slate-100">

            <div class="p-6 text-center">
                <div class="flex relative justify-center items-center mx-auto mb-5 w-16 h-16">
                    <div class="absolute inset-0 rounded-full border-4 border-primary/15"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-transparent animate-spin border-t-primary"></div>

                    <div class="flex justify-center items-center w-10 h-10 rounded-2xl bg-primary/10 text-primary">
                        <span class="material-symbols-outlined text-[24px]">sync</span>
                    </div>
                </div>

                <h3 class="text-lg font-black text-slate-900 font-headline">
                    يرجى الانتظار
                </h3>

                <p class="mt-2 text-sm font-bold text-slate-500" x-text="processingMessage">
                    جاري تنفيذ العملية...
                </p>

                <div class="p-3 mt-5 text-xs font-bold leading-6 rounded-2xl bg-slate-50 text-slate-500">
                    لا تغلق الصفحة ولا تضغط مرة أخرى حتى تكتمل العملية.
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div x-cloak
         x-show="toast.show"
         x-transition
         class="fixed top-6 left-1/2 z-[9999] flex -translate-x-1/2 items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold text-white shadow-lg"
         :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-red-500'">
        <span class="material-symbols-outlined" x-text="toast.type === 'success' ? 'check_circle' : 'error'"></span>
        <span x-text="toast.message"></span>
    </div>

    {{-- Breadcrumb --}}
    <div class="flex flex-wrap gap-2 items-center text-sm font-medium text-slate-500">
        <a href="{{ route('superadmin.dashboard') }}" class="transition-colors hover:text-primary">
            الرئيسية
        </a>

        <span class="material-symbols-outlined text-[16px]">chevron_left</span>

        <a href="{{ route('superadmin.offices.index') }}" class="transition-colors hover:text-primary">
            إدارة المكاتب
        </a>

        <span class="material-symbols-outlined text-[16px]">chevron_left</span>

        <span class="font-bold text-slate-900">
            {{ $app->name }}
        </span>
    </div>

    {{-- Profile Card --}}
    <div class="overflow-hidden bg-white rounded-2xl border shadow-sm border-slate-100">
        <div class="h-2" style="background:{{ $app->color ?? '#f79009' }}"></div>

        <div class="p-4 sm:p-6">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex gap-4 items-center">
                    <div class="flex justify-center items-center w-14 h-14 text-2xl font-black text-white rounded-2xl shadow-md sm:w-16 sm:h-16 shrink-0"
                         style="background:{{ $app->color ?? '#f79009' }}">
                        {{ mb_substr($app->name ?? 'م', 0, 1) }}
                    </div>

                    <div>
                        <h1 class="text-xl font-black text-slate-900 font-headline">
                            {{ $app->name ?? 'بدون اسم' }}
                        </h1>

                        <p class="mt-1 text-xs sm:text-sm text-slate-500">
                            ID: {{ $app->id }}
                            &middot;
                            تاريخ التسجيل: {{ $app->created_at->format('Y/m/d') }}
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 items-center">
                    @if ($app->is_active)
                        <span class="inline-flex gap-1.5 items-center px-4 py-2 text-sm font-bold text-emerald-700 bg-emerald-50 rounded-full border border-emerald-100">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                            نشط
                        </span>
                    @else
                        <span class="inline-flex gap-1.5 items-center px-4 py-2 text-sm font-bold text-red-700 bg-red-50 rounded-full border border-red-100">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            معطل
                        </span>
                    @endif
                </div>
            </div>

            {{-- Stats Row --}}
            <div class="grid grid-cols-2 gap-4 mt-6 lg:grid-cols-4">
                <div class="p-4 rounded-xl border border-transparent transition-colors bg-slate-50 hover:border-slate-200">
                    <p class="text-xs font-bold text-slate-500">الفروع</p>
                    <p class="mt-1 text-2xl font-black text-slate-900 font-headline">
                        {{ $app->branches_count }}
                    </p>
                </div>

                <div class="p-4 rounded-xl border border-transparent transition-colors bg-slate-50 hover:border-slate-200">
                    <p class="text-xs font-bold text-slate-500">المستخدمين</p>
                    <p class="mt-1 text-2xl font-black text-slate-900 font-headline">
                        {{ $app->users_count }}
                    </p>
                </div>

                <div class="p-4 rounded-xl border border-transparent transition-colors bg-slate-50 hover:border-slate-200">
                    <p class="text-xs font-bold text-slate-500">الباقة الحالية</p>
                    <p class="mt-1 text-lg font-black truncate text-slate-900 font-headline">
                        {{ $app->currentSubscription?->package?->name ?? '—' }}
                    </p>
                </div>

                <div class="p-4 rounded-xl border border-transparent transition-colors bg-slate-50 hover:border-slate-200">
                    <p class="text-xs font-bold text-slate-500">انتهاء الاشتراك</p>
                    <p class="mt-1 text-lg font-black font-headline {{ $app->currentSubscription?->ends_at?->isPast() ? 'text-red-600' : 'text-slate-900' }}">
                        {{ $app->currentSubscription?->ends_at?->format('Y/m/d') ?? '—' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Services Management --}}
    <div class="overflow-hidden bg-white rounded-2xl border shadow-sm border-slate-100">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-lg font-black text-slate-900 font-headline">
                إدارة الخدمات الملحقة
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                تفعيل أو تعطيل الموديولات الخاصة بهذا المكتب
            </p>
        </div>

        <div class="flex flex-col gap-3 px-6 py-4 bg-white border-b border-slate-100 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-sm font-black text-slate-900">
                    صلاحيات الخدمات
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    السوبر أدمن يستطيع تفعيل أو تعطيل كل خدمات المكتب مباشرة
                </p>
            </div>

            <div class="flex gap-2">
                <button type="button"
                        @click="toggleAllServices(true)"
                        :disabled="isProcessing"
                        class="inline-flex gap-2 justify-center items-center px-4 py-2 text-xs font-bold text-white bg-emerald-600 rounded-xl transition hover:bg-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span class="material-symbols-outlined text-[16px]">done_all</span>
                    تفعيل الكل
                </button>

                <button type="button"
                        @click="toggleAllServices(false)"
                        :disabled="isProcessing"
                        class="inline-flex gap-2 justify-center items-center px-4 py-2 text-xs font-bold text-red-600 bg-red-50 rounded-xl transition hover:bg-red-600 hover:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                    <span class="material-symbols-outlined text-[16px]">block</span>
                    تعطيل الكل
                </button>
            </div>
        </div>

        <div class="divide-y divide-slate-50">
            @forelse($services as $service)
                @php
                    $isActive = $app->services->where('id', $service->id)->first()?->pivot?->is_active ?? false;
                @endphp

                <div class="flex justify-between items-center px-4 py-4 transition-colors sm:px-6 hover:bg-slate-50/60">
                    <div class="flex gap-3 items-center">
                        <div class="flex justify-center items-center w-10 h-10 rounded-xl text-primary bg-primary/10 shrink-0">
                            <span class="material-symbols-outlined text-[22px]">extension</span>
                        </div>

                        <div>
                            <p class="text-sm font-bold text-slate-900">
                                {{ $service->name }}
                            </p>

                            <p class="text-xs text-slate-500 line-clamp-1">
                                {{ $service->description ?? $service->slug }}
                            </p>
                        </div>
                    </div>

                    <label class="inline-flex relative items-center w-11 h-6 rounded-full transition-colors duration-200 shrink-0"
                           :class="[
                               serviceStates[{{ $service->id }}] ? 'bg-emerald-500' : 'bg-slate-300',
                               isProcessing ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'
                           ]">

                        <input type="checkbox"
                               class="sr-only peer"
                               :disabled="isProcessing"
                               :checked="serviceStates[{{ $service->id }}]"
                               @change="toggleFeature({{ $service->id }}, $event.target.checked)" />

                        <span class="absolute w-4 h-4 bg-white rounded-full shadow transition-all duration-200"
                              :class="serviceStates[{{ $service->id }}] ? 'right-1' : 'left-1'"></span>
                    </label>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <div class="flex justify-center items-center mx-auto mb-4 w-16 h-16 rounded-full bg-slate-50">
                        <span class="material-symbols-outlined text-[32px] text-slate-400">
                            extension_off
                        </span>
                    </div>

                    <p class="text-sm font-bold text-slate-500">
                        لا توجد خدمات إضافية متاحة في النظام
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function tenantProfile() {
    return {
        appId: {{ $app->id }},

        isProcessing: false,
        processingMessage: 'جاري تنفيذ العملية...',

        toast: {
            show: false,
            message: '',
            type: 'success'
        },

        serviceStates: {
            @foreach($services as $service)
                @php
                    $isActive = $app->services->where('id', $service->id)->first()?->pivot?->is_active ?? false;
                @endphp
                {{ $service->id }}: {{ $isActive ? 'true' : 'false' }},
            @endforeach
        },

        showToast(msg, type = 'success') {
            this.toast = {
                show: true,
                message: msg,
                type: type
            };

            setTimeout(() => {
                this.toast.show = false;
            }, 3000);
        },

        startProcessing(message = 'جاري تنفيذ العملية...') {
            this.processingMessage = message;
            this.isProcessing = true;
        },

        stopProcessing() {
            this.isProcessing = false;
            this.processingMessage = 'جاري تنفيذ العملية...';
        },

        async toggleAllServices(isActive) {
            if (this.isProcessing) return;

            const ids = Object.keys(this.serviceStates);

            if (ids.length === 0) {
                this.showToast('لا توجد خدمات لتحديثها', 'error');
                return;
            }

            const oldStates = { ...this.serviceStates };

            this.startProcessing(
                isActive
                    ? 'جاري تفعيل جميع الخدمات...'
                    : 'جاري تعطيل جميع الخدمات...'
            );

            try {
                Object.keys(this.serviceStates).forEach((serviceId) => {
                    this.serviceStates[serviceId] = isActive;
                });

                const res = await fetch(`/superadmin/offices/${this.appId}/toggle-all-services`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        is_active: isActive
                    })
                });

                const data = await res.json();

                if (!res.ok || data.status !== 'success') {
                    this.serviceStates = oldStates;
                    this.showToast(data.message || 'تعذر تحديث جميع الخدمات', 'error');
                    return;
                }

                this.showToast(
                    data.message || (isActive ? 'تم تفعيل جميع الخدمات' : 'تم تعطيل جميع الخدمات')
                );

            } catch (e) {
                console.error('toggleAllServices error:', e);
                this.serviceStates = oldStates;
                this.showToast('حدث خطأ أثناء الاتصال بالخادم', 'error');
            } finally {
                this.stopProcessing();
            }
        },

        async toggleFeature(serviceId, isActive, showMessage = true) {
            if (this.isProcessing) return;

            const oldValue = this.serviceStates[serviceId];

            this.startProcessing(
                isActive
                    ? 'جاري تفعيل الخدمة...'
                    : 'جاري تعطيل الخدمة...'
            );

            this.serviceStates[serviceId] = isActive;

            try {
                const res = await fetch(`/superadmin/offices/${this.appId}/toggle-service`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        service_id: serviceId,
                        is_active: isActive
                    })
                });

                const data = await res.json();

                if (!res.ok || data.status !== 'success') {
                    this.serviceStates[serviceId] = oldValue;
                    this.showToast(data.message || 'حدث خطأ أثناء تحديث الخدمة', 'error');
                    return;
                }

                this.serviceStates[serviceId] = isActive;

                if (showMessage) {
                    this.showToast(data.message || 'تم تحديث الخدمة بنجاح');
                }

            } catch (e) {
                console.error('toggleFeature error:', e);
                this.serviceStates[serviceId] = oldValue;
                this.showToast('حدث خطأ أثناء الاتصال بالخادم', 'error');
            } finally {
                this.stopProcessing();
            }
        }
    };
}
</script>
@endsection