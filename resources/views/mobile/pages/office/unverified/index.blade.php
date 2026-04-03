@extends('mobile.layouts.app')

@section('title', 'المكاتب غير الموثوقة')

@section('content')
<x-modals.success-modal />
    <x-modals.error-modal />
    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        searchQuery: '',
        isSubmitting: false,
    
        // بيانات تعديل مكتب
        editOfficeData: { id: '', name: '', phone: '', address: '', url: '' },
        deleteOfficeData: { id: '', name: '', url: '' },
    
        openCreateModal() {
            this.showCreateModal = true;
        },
    
        openEditModal(office) {
            this.editOfficeData = {
                id: office.id,
                name: office.name,
                phone: office.phone,
                address: office.address,
                url: '/offices/' + office.id
            };
            this.showEditModal = true;
        },

        openDeleteModal(office) {
            this.deleteOfficeData = {
                id: office.id,
                name: office.name,
                url: '/offices/' + office.id
            };
            this.showEditModal = false;
            this.showDeleteModal = true;
        },
        
        closeModals() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.showDeleteModal = false;
        }
    }" class="flex flex-col gap-6 relative min-h-screen pb-24">

        <div class="flex justify-between items-center px-2">
            <div>
                <h1 class="text-2xl font-black font-headline text-slate-800">المكاتب غير الموثوقة</h1>
                <p class="text-xs font-semibold text-slate-400">إجمالي المكاتب: <span class="font-bold text-primary">{{ $offices->total() }}</span></p>
            </div>
            <button @click="openCreateModal()"
                class="flex justify-center items-center w-12 h-12 text-white rounded-2xl shadow-xl transition-all bg-primary shadow-primary/20 active:scale-95">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">add_business</span>
            </button>
        </div>

        <div class="px-2">
            <div class="relative group">
                <span
                    class="absolute right-4 top-1/2 transition-colors -translate-y-1/2 material-symbols-outlined text-slate-400 group-focus-within:text-primary">search</span>
                <input type="text" x-model="searchQuery" placeholder="ابحث باسم المكتب، رقم الهاتف أو العنوان..."
                    class="pr-12 w-full h-14 text-sm bg-white rounded-[1.25rem] border-none ring-1 shadow-sm transition-all outline-none ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline text-slate-700">
            </div>
        </div>

        <div class="px-2 space-y-4">
            @forelse($offices as $office)
                <div x-show="searchQuery === '' || '{{ $office->name }}'.includes(searchQuery) || '{{ $office->address }}'.includes(searchQuery) || '{{ $office->phone }}'.includes(searchQuery)"
                    class="p-5 bg-white rounded-[1.75rem] border border-slate-50 shadow-[0_8px_30px_rgb(0,0,0,0.02)] relative overflow-hidden transition-all duration-300">

                    <div
                        class="absolute -left-2 top-4 px-4 py-1 rounded-r-full text-[10px] font-black uppercase tracking-widest bg-amber-50 text-amber-600 border border-amber-100/50">
                        مكتب غير موثوق
                    </div>

                    <div class="flex gap-4 items-center mt-2">
                        <div class="flex justify-center items-center w-14 h-14 text-amber-500 bg-amber-50 rounded-2xl border shadow-inner border-amber-100/50 shrink-0">
                            <span class="text-2xl material-symbols-outlined">storefront</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-bold truncate text-slate-800 font-headline">{{ $office->name }}</h3>
                            <p class="flex gap-1 items-center mt-1 text-xs text-slate-500">
                                <span class="material-symbols-outlined text-[14px] text-slate-400">location_on</span>
                                <span class="truncate">{{ $office->address ?: 'لم يتم تحديد العنوان' }}</span>
                            </p>
                            @if($office->phone)
                                <p class="flex gap-1 items-center mt-1 text-xs text-slate-500" dir="ltr">
                                    <span class="material-symbols-outlined text-[14px] text-slate-400">call</span>
                                    <span class="font-mono font-bold tracking-wider text-slate-600">{{ $office->phone }}</span>
                                </p>
                            @endif
                        </div>
                        <button @click="openEditModal({{ $office }})"
                            class="flex justify-center items-center w-10 h-10 rounded-xl transition-all shrink-0 bg-slate-50 text-slate-400 hover:bg-primary/10 hover:text-primary active:scale-90">
                            <span class="text-xl material-symbols-outlined">edit_square</span>
                        </button>
                    </div>
                </div>

                {{-- Delete logic is handled via openDeleteModal --}}

            @empty
                <div class="py-20 flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100 shadow-sm mx-2">
                    <div class="flex justify-center items-center mb-6 w-24 h-24 rounded-full bg-slate-50 text-slate-200">
                        <span class="text-6xl material-symbols-outlined">storefront</span>
                    </div>
                    <p class="text-lg font-bold font-headline text-slate-400">لا توجد مكاتب مضافة حالياً</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination info if available --}}
        @if ($offices->hasPages())
            <div class="px-2 pb-6">
                {{ $offices->links('vendor.pagination.mobile') }}
            </div>
        @endif

        {{-- Create Modal --}}
        <div x-show="showCreateModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">
            
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto" @click="showCreateModal = false"></div>
            
            <div class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto">
                <div @click="showCreateModal = false" class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90"></div>
                
                <div class="flex justify-between items-center px-2 mb-8">
                    <h3 class="text-xl font-black font-headline text-slate-800">إضافة مكتب جديد</h3>
                    <button type="button" @click="showCreateModal = false"
                        class="flex justify-center items-center w-10 h-10 rounded-xl transition-colors bg-slate-50 text-slate-400 hover:bg-slate-100">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route('offices.store') }}" method="POST" @submit="isSubmitting = true" class="px-2 space-y-5">
                    @csrf

                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">اسم المكتب <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">storefront</span>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 ring-slate-100 focus:bg-white focus:ring-2 focus:ring-primary/20 font-headline">
                        </div>
                        @error('name')
                            <p class="px-1 mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">العنوان</label>
                            <div class="relative">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">location_on</span>
                                <input type="text" name="address" value="{{ old('address') }}"
                                    class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 ring-slate-100 focus:bg-white focus:ring-2 focus:ring-primary/20 font-headline">
                            </div>
                            @error('address')
                                <p class="px-1 mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">رقم الهاتف</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" dir="ltr" placeholder="7xx xxx xxx"
                                class="px-4 w-full h-14 text-sm text-left rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 ring-slate-100 focus:bg-white focus:ring-2 focus:ring-primary/20 font-headline">
                            @error('phone')
                                <p class="px-1 mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" :disabled="isSubmitting"
                        class="flex gap-2 justify-center items-center mt-6 w-full h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-primary shadow-primary/30 active:scale-95 disabled:opacity-70 font-headline">
                        <span x-show="!isSubmitting" class="material-symbols-outlined">save</span>
                        <span x-text="isSubmitting ? 'جاري الحفظ...' : 'حفظ المكتب'"></span>
                        <span x-show="isSubmitting" class="animate-spin material-symbols-outlined">progress_activity</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Edit Modal --}}
        <div x-show="showEditModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">
            
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto" @click="showEditModal = false"></div>
            
            <div class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto">
                <div @click="showEditModal = false" class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90"></div>
                
                <div class="flex justify-between items-center px-2 mb-8">
                    <h3 class="text-xl font-black font-headline text-slate-800">تعديل بيانات المكتب</h3>
                    <button type="button" @click="showEditModal = false"
                        class="flex justify-center items-center w-10 h-10 rounded-xl transition-colors bg-slate-50 text-slate-400 hover:bg-slate-100">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form :action="editOfficeData.url" method="POST" @submit="isSubmitting = true" class="px-2 space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">اسم المكتب <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">storefront</span>
                            <input type="text" name="name" x-model="editOfficeData.name" required
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 ring-slate-100 focus:bg-white focus:ring-2 focus:ring-primary/20 font-headline">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">العنوان</label>
                            <div class="relative">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">location_on</span>
                                <input type="text" name="address" x-model="editOfficeData.address"
                                    class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 ring-slate-100 focus:bg-white focus:ring-2 focus:ring-primary/20 font-headline">
                            </div>
                        </div>
                        <div>
                            <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">رقم الهاتف</label>
                            <input type="tel" name="phone" x-model="editOfficeData.phone" dir="ltr"
                                class="px-4 w-full h-14 text-sm text-left rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 ring-slate-100 focus:bg-white focus:ring-2 focus:ring-primary/20 font-headline">
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit" :disabled="isSubmitting"
                            class="flex flex-1 gap-2 justify-center items-center h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-primary shadow-primary/30 active:scale-95 disabled:opacity-70 font-headline">
                            <span x-show="!isSubmitting" class="material-symbols-outlined">update</span>
                            <span x-text="isSubmitting ? 'جاري الحفظ...' : 'حفظ التعديلات'"></span>
                            <span x-show="isSubmitting" class="animate-spin material-symbols-outlined">progress_activity</span>
                        </button>
                        <button type="button" @click="openDeleteModal(editOfficeData)"
                            class="flex justify-center items-center w-14 h-14 text-red-500 bg-red-50 rounded-2xl transition-transform hover:bg-red-100 active:scale-90">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
<div x-show="showDeleteModal" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-full"
             class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">
            
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto" @click="closeModals()"></div>

            <div class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto text-center">
                <!-- Handle Bar -->
                <div @click="closeModals()" class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90"></div>

                <!-- أيقونة التحذير -->
                <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-red-50 text-red-500 rounded-[1.5rem]">
                    <span class="text-4xl material-symbols-outlined">delete_forever</span>
                </div>

                <h3 class="mb-3 text-2xl font-black font-headline text-slate-800">تأكيد الحذف</h3>
                
                <p class="mb-8 text-sm font-semibold leading-relaxed text-slate-500">
                    هل أنت متأكد من أنك تريد حذف المكتب <br>
                    <span class="text-base font-bold text-slate-800 font-headline" x-text="deleteOfficeData.name"></span>؟<br>
                    <span class="text-red-500/80">لا يمكن التراجع عن هذا الإجراء.</span>
                </p>

                <form :action="deleteOfficeData.url" method="POST" @submit="isSubmitting = true" class="flex gap-3 px-2">
                    @csrf
                    @method('DELETE')
                    
                    <button type="button" @click="closeModals()"
                        class="flex-1 py-4 text-sm font-bold rounded-2xl transition-all text-slate-600 bg-slate-100 hover:bg-slate-200 active:scale-95 font-headline">
                        تراجع
                    </button>
                    
                    <button type="submit" :disabled="isSubmitting"
                        class="flex flex-1 gap-2 justify-center items-center py-4 text-sm font-bold text-white bg-red-500 rounded-2xl shadow-lg transition-all hover:bg-red-600 shadow-red-500/30 active:scale-95 font-headline disabled:opacity-70">
                        <span x-show="!isSubmitting">نعم، احذف</span>
                        <span x-show="isSubmitting" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
