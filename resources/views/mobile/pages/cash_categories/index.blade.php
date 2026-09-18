@extends('mobile.layouts.app')

@section('title', 'فئات الصندوق')

@section('content')
<x-modals.success-modal />
<x-modals.error-modal />

<div class="flex relative flex-col gap-6 px-4 pb-24 min-h-screen bg-slate-50/50 font-body" dir="rtl" x-data="{
    editModalOpen: false,
    activeCategory: { id: '', name: '', type: '' },
    openEditModal(category) {
        this.activeCategory = Object.assign({}, category);
        this.editModalOpen = true;
    }
}">

    {{-- ================= 1. الرأس وزر الإضافة ================= --}}
    <div class="flex justify-between items-center mt-6">
        <div class="flex flex-col">
            <h1 class="text-3xl font-black font-headline text-slate-800">فئات الصندوق</h1>
            <p class="mt-1 text-sm font-medium text-slate-500">
                إجمالي <span class="font-bold text-primary">{{ \App\Models\CashCategory::count() }}</span> فئة مسجلة
            </p>
        </div>
        
        <button type="button" @click="$dispatch('open-create-category-modal')"
            class="w-12 h-12 bg-primary text-white rounded-[1rem] flex items-center justify-center shadow-[0_8px_20px_rgba(251,146,60,0.4)] active:scale-90 transition-transform shrink-0">
            <span class="material-symbols-outlined text-[26px]">add_box</span>
        </button>
    </div>

    {{-- ================= 2. فلاتر التبويب السريعة ================= --}}
    <div class="flex gap-2 overflow-x-auto pb-2 custom-scrollbar -mx-4 px-4 snap-x">
        <a href="{{ route('cash-categories.index') }}"
            class="snap-start whitespace-nowrap px-5 py-2.5 rounded-2xl text-xs font-bold transition-all shadow-sm {{ !request('type') ? 'bg-slate-800 text-white' : 'bg-white text-slate-600 border border-slate-100' }}">
            الكل
        </a>
        <a href="{{ route('cash-categories.index', ['type' => 'income']) }}"
            class="snap-start flex items-center gap-1.5 whitespace-nowrap px-5 py-2.5 rounded-2xl text-xs font-bold transition-all shadow-sm {{ request('type') === 'income' ? 'bg-emerald-500 text-white shadow-emerald-500/20' : 'bg-white text-slate-600 border border-slate-100' }}">
            <span class="material-symbols-outlined text-[16px] {{ request('type') === 'income' ? 'text-white' : 'text-emerald-500' }}">south_west</span>
            إيراد (قبض)
        </a>
        <a href="{{ route('cash-categories.index', ['type' => 'expense']) }}"
            class="snap-start flex items-center gap-1.5 whitespace-nowrap px-5 py-2.5 rounded-2xl text-xs font-bold transition-all shadow-sm {{ request('type') === 'expense' ? 'bg-rose-500 text-white shadow-rose-500/20' : 'bg-white text-slate-600 border border-slate-100' }}">
            <span class="material-symbols-outlined text-[16px] {{ request('type') === 'expense' ? 'text-white' : 'text-rose-500' }}">north_east</span>
            مصروف (صرف)
        </a>
    </div>

    {{-- ================= 3. بطاقات الفئات (Cards) ================= --}}
    <div class="space-y-4">
        @forelse ($categories as $category)
            <div x-data="{ openMenu: false }" :class="{ 'z-50': openMenu }" class="bg-white rounded-[20px] border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative group p-4">
                
                {{-- شريط جانبي يوضح النوع --}}
                <div class="absolute right-0 top-0 bottom-0 w-1.5 opacity-80 rounded-r-[20px] {{ $category->type === 'income' ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>

                <div class="flex justify-between items-start pl-2 pr-3">
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2 mb-1.5">
                            <h3 class="text-base font-black text-slate-800 font-headline">{{ $category->name }}</h3>
                        </div>
                        
                        <div class="flex items-center gap-2 mt-1">
                            @if ($category->type === 'income')
                                <span class="inline-flex gap-1 items-center px-2 py-0.5 text-[10px] font-bold text-emerald-600 bg-emerald-50 rounded-full border border-emerald-100">
                                    <span class="material-symbols-outlined text-[14px]">south_west</span>
                                    قبض (إيراد)
                                </span>
                            @else
                                <span class="inline-flex gap-1 items-center px-2 py-0.5 text-[10px] font-bold text-rose-600 bg-rose-50 rounded-full border border-rose-100">
                                    <span class="material-symbols-outlined text-[14px]">north_east</span>
                                    صرف (مصروف)
                                </span>
                            @endif

                            <form action="{{ route('cash-categories.update', $category) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="toggle_status" value="1">
                                <button type="submit" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold transition-colors {{ $category->is_active ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $category->is_active ? 'bg-blue-500' : 'bg-slate-400' }}"></span>
                                    {{ $category->is_active ? 'نشط' : 'معطل' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- الإجراءات --}}
                    <div class="relative" @click.away="openMenu = false">
                        <button type="button" @click="openMenu = !openMenu"
                            class="flex justify-center items-center w-8 h-8 rounded-full transition-colors text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                            <span class="material-symbols-outlined text-[20px]">more_vert</span>
                        </button>

                        <div x-show="openMenu" x-transition.opacity.duration.200ms x-cloak
                            class="absolute top-full left-0 mt-1 w-36 bg-white/95 backdrop-blur-md rounded-2xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.15)] border border-slate-100 z-50 overflow-hidden py-1.5">
                            
                            <button type="button" @click="openEditModal({ id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', type: '{{ $category->type }}' }); openMenu = false"
                                class="w-full flex gap-2 items-center px-4 py-2 text-xs font-bold transition-colors text-slate-600 hover:bg-blue-50 hover:text-blue-600 text-right">
                                <span class="material-symbols-outlined text-[16px]">edit</span>
                                تعديل
                            </button>

                            <form action="{{ route('cash-categories.destroy', $category) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفئة؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex gap-2 items-center px-4 py-2 text-xs font-bold transition-colors text-rose-600 hover:bg-rose-50 text-right">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-16 bg-white rounded-[24px] border-2 border-dashed border-slate-200/70 mt-4 shadow-sm">
                <div class="relative mb-4">
                    <div class="absolute inset-0 rounded-full blur-xl bg-primary/20"></div>
                    <div class="w-16 h-16 bg-gradient-to-br from-slate-50 to-slate-100 rounded-[18px] flex items-center justify-center border border-white shadow-sm relative z-10">
                        <span class="material-symbols-outlined text-[32px] text-slate-300">category</span>
                    </div>
                </div>
                <h3 class="text-sm font-black text-slate-700 font-headline">لا توجد فئات</h3>
                <p class="text-[11px] font-bold text-slate-400 mt-1">لم نعثر على أي فئات مسجلة حالياً.</p>
            </div>
        @endforelse

        @if ($categories->hasPages())
            <div class="mt-6">
                {{ $categories->links('vendor.pagination.mobile') }}
            </div>
        @endif
    </div>

    {{-- تضمين المودالات --}}
    @include('mobile.pages.cash_categories.modals.create-category-modal')
    @include('mobile.pages.cash_categories.modals.edit-category-modal')
</div>
@endsection
