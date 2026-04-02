@extends('mobile.layouts.app')

@section('title', 'المكاتب غير الموثوقة')

@section('content')
<div x-data="{
    showCreateModal: false,
    showEditModal: false,
    searchQuery: '',
    isSubmitting: false,
    errors: {},
    
    // بيانات إضافة مكتب جديد
    createOfficeData: { name: '', phone: '', city: '', customer_type: 'company' },
    
    // بيانات تعديل مكتب
    editOfficeData: { id: '', name: '', phone: '', city: '', customer_type: '', url: '' },

    openCreateModal() {
        this.errors = {};
        this.createOfficeData = { name: '', phone: '', city: '', customer_type: 'company' };
        this.showCreateModal = true;
    },

    openEditModal(office) {
        this.errors = {};
        this.editOfficeData = { 
            id: office.id, 
            name: office.name, 
            phone: office.phone, 
            city: office.city,
            customer_type: office.customer_type,
            url: '/offices/' + office.id 
        };
        this.showEditModal = true;
    },

    async submitForm(url, method, data) {
        this.isSubmitting = true;
        this.errors = {};
        try {
            const response = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (!response.ok) {
                this.errors = result.errors;
            } else {
                window.location.reload();
            }
        } catch (error) { alert('خطأ في الاتصال'); }
        finally { this.isSubmitting = false; }
    }
}" class="flex flex-col gap-6">

    <div class="flex justify-between items-center px-2">
        <div>
            <h1 class="text-2xl font-black font-headline text-slate-800">المكاتب اليدوية</h1>
            <p class="text-xs font-semibold text-slate-400">إجمالي المكاتب: {{ $offices->total() }}</p>
        </div>
        <button @click="openCreateModal()" class="flex justify-center items-center w-12 h-12 text-white rounded-2xl bg-secondary shadow-lg active:scale-95">
            <span class="material-symbols-outlined">add_business</span>
        </button>
    </div>

    <div class="px-2">
        <div class="relative">
            <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">search</span>
            <input type="text" x-model="searchQuery" placeholder="ابحث باسم المكتب أو المدينة..." 
                class="w-full h-14 pr-12 rounded-2xl border-none bg-white shadow-sm ring-1 ring-slate-100 outline-none focus:ring-2 focus:ring-secondary/20 transition-all font-headline text-sm">
        </div>
    </div>

    <div class="px-2 space-y-4">
        @forelse($offices as $office)
        <div x-show="searchQuery === '' || '{{ $office->name }}'.includes(searchQuery)" 
            class="p-5 bg-white rounded-[1.75rem] border border-slate-50 shadow-sm relative overflow-hidden">
            
            <div class="absolute -left-2 top-4 px-4 py-1 rounded-r-full text-[10px] font-black uppercase tracking-widest
                {{ $office->customer_type === 'individual' ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600' }}">
                {{ $office->customer_type === 'individual' ? 'B2C - فرد' : 'B2B - شركة' }}
            </div>

            <div class="flex gap-4 items-center mt-4">
                <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
                    <span class="material-symbols-outlined">{{ $office->customer_type === 'individual' ? 'person' : 'apartment' }}</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 font-headline">{{ $office->name }}</h3>
                    <p class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">location_on</span>
                        {{ $office->city }}
                    </p>
                </div>
                <button @click="openEditModal({{ $office }})" class="mr-auto w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">edit</span>
                </button>
            </div>
        </div>
        @empty
        <div class="py-20 text-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100">
            <p class="text-slate-400 font-headline">لا توجد مكاتب مضافة حالياً</p>
        </div>
        @endforelse
    </div>

    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[99999] flex items-end justify-center">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px]" @click="showCreateModal = false"></div>
        <div class="relative w-full bg-white rounded-t-[2.5rem] p-6 pb-12 max-w-xl shadow-2xl">
            <h3 class="text-xl font-black font-headline mb-6 text-slate-800">إضافة مكتب جديد</h3>
            <form @submit.prevent="submitForm('/offices/store-unverified', 'POST', createOfficeData)" class="space-y-4">
                
                <div class="flex gap-2 p-1 bg-slate-100 rounded-2xl">
                    <button type="button" @click="createOfficeData.customer_type = 'company'" 
                        :class="createOfficeData.customer_type === 'company' ? 'bg-white shadow-sm text-secondary' : 'text-slate-500'"
                        class="flex-1 py-3 rounded-xl text-sm font-bold transition-all font-headline">شركة (B2B)</button>
                    <button type="button" @click="createOfficeData.customer_type = 'individual'" 
                        :class="createOfficeData.customer_type === 'individual' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-500'"
                        class="flex-1 py-3 rounded-xl text-sm font-bold transition-all font-headline">فرد (B2C)</button>
                </div>

                <div>
                    <label class="block px-1 mb-2 text-sm font-bold text-slate-600">اسم المكتب / العميل</label>
                    <input type="text" x-model="createOfficeData.name" class="w-full h-14 px-4 bg-slate-50 rounded-2xl border-none ring-1 ring-slate-100 outline-none">
                    <template x-if="errors.name"><p class="text-rose-500 text-xs mt-1" x-text="errors.name[0]"></p></template>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600">المدينة</label>
                        <input type="text" x-model="createOfficeData.city" class="w-full h-14 px-4 bg-slate-50 rounded-2xl border-none ring-1 ring-slate-100 outline-none">
                    </div>
                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600">رقم الهاتف</label>
                        <input type="tel" x-model="createOfficeData.phone" class="w-full h-14 px-4 bg-slate-50 rounded-2xl border-none ring-1 ring-slate-100 outline-none">
                    </div>
                </div>

                <button type="submit" :disabled="isSubmitting" class="w-full h-14 bg-secondary text-white font-black rounded-2xl mt-4 active:scale-95">
                    <span x-show="!isSubmitting">حفظ المكتب</span>
                    <span x-show="isSubmitting" class="material-symbols-outlined animate-spin">progress_activity</span>
                </button>
            </form>
        </div>
    </div>

</div>
@endsection