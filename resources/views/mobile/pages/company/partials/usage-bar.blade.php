@php
    // 1. فحص هل الباقة غير محدودة (الحد يساوي 0)
    $isUnlimited = ($limit == 0);

    // 2. فحص هل وصل الاستهلاك للحد الأقصى (ولا نطبق هذا الشرط على المفتوح)
    $isFull = (!$isUnlimited && $current >= $limit);
    
    // 3. تحديد اللون بناءً على الحالة
    if ($isFull) {
        $displayColor = 'bg-rose-500'; // أحمر
    } elseif ($isUnlimited) {
        $displayColor = 'bg-amber-500'; // أصفر / ذهبي
    } else {
        $displayColor = $color; // اللون الافتراضي الممرر من الصفحة
    }
@endphp

<div>
    <div class="flex justify-between items-end mb-2">
        <span class="text-xs font-bold {{ $isFull ? 'text-rose-600' : 'text-slate-600' }} font-headline flex items-center gap-1.5 transition-colors duration-300">
            
            {{-- تغيير لون الأيقونة حسب الحالة --}}
            <span class="material-symbols-outlined text-[16px] {{ $isFull ? 'text-rose-500 animate-pulse' : ($isUnlimited ? 'text-amber-500' : 'text-slate-400') }}">
                {{ $icon }}
            </span>
            {{ $label }}
        </span>
        
        <span class="text-xs font-black {{ $isFull ? 'text-rose-700' : 'text-slate-800' }}">
            {{ number_format($current) }} 
            <span class="text-slate-400 font-medium">من {{ $isUnlimited ? '∞' : number_format($limit) }}</span>
        </span>
    </div>

    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden flex ring-1 ring-slate-50">
        {{-- شريط التقدم باللون الديناميكي --}}
        <div class="{{ $displayColor }} h-full rounded-full transition-all duration-700 ease-in-out" 
             style="width: {{ $isUnlimited ? 100 : min($percent, 100) }}%">
        </div>
    </div>
</div>