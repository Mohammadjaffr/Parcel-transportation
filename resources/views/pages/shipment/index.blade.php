@extends('layouts.app')

@section('title', 'إدارة الطرود')

@section('content')
<div class="flex flex-col gap-6 px-2">
    <div class="mb-2">
        <h1 class="text-2xl font-bold font-headline text-on-surface">إدارة الطرود</h1>
        <p class="text-sm text-on-surface-variant">اختر نوع الطرود التي تريد عرضها</p>
    </div>

    <div class="grid grid-cols-1 gap-4">
        
        <a href="{{ route('shipment.outgoing.index') }}" class="flex gap-5 items-center p-6 rounded-3xl border shadow-sm transition-all bg-surface-container-lowest border-slate-100 active:scale-95 group">
            <div class="flex justify-center items-center w-14 h-14 rounded-2xl transition-colors bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white">
                <span class="text-3xl material-symbols-outlined">unarchive</span>
            </div>
            <div class="flex flex-col">
                <span class="text-lg font-bold font-headline text-on-surface">الطرود المرسلة</span>
                <span class="text-xs text-on-surface-variant">متابعة الطرود الصادرة من مكتبك</span>
            </div>
            <span class="mr-auto transition-transform material-symbols-outlined text-slate-300 group-hover:-translate-x-1">chevron_left</span>
        </a>

        <a href="{{ route('receipts.index') }}" class="flex gap-5 items-center p-6 rounded-3xl border shadow-sm transition-all bg-surface-container-lowest border-slate-100 active:scale-95 group">
            <div class="flex justify-center items-center w-14 h-14 rounded-2xl transition-colors bg-secondary-container/10 text-secondary group-hover:bg-secondary-container group-hover:text-white">
                <span class="text-3xl material-symbols-outlined">archive</span>
            </div>
            <div class="flex flex-col">
                <span class="text-lg font-bold font-headline text-on-surface">الطرود المستلمة</span>
                <span class="text-xs text-on-surface-variant">إدارة الطرود الواردة إلى مكتبك</span>
            </div>
            <span class="mr-auto transition-transform material-symbols-outlined text-slate-300 group-hover:-translate-x-1">chevron_left</span>
        </a>

    </div>
</div>
@endsection