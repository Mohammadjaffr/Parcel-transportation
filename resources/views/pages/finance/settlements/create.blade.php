@extends('layouts.app')
@section('title', 'تسوية مالية')
@section('Breadcrumb', 'تسوية مالية')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-1">
            تسوية مالية للفرع: {{ auth()->user()->branch->name }}
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            يمكنك إرسال تسوية فقط للفروع التي على فرعك مبالغ مستحقة لها.
        </p>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <form action="{{ route('finance.settlements.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- الفرع الدافع (ثابت) -->
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">الفرع الدافع</label>
                <input type="text" disabled
                       value="{{ auth()->user()->branch->name }}"
                       class="w-full mt-1 rounded-lg border px-4 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-600">
                <input type="hidden" name="from_branch_id" value="{{ $currentBranchId }}">
            </div>

            <!-- الفروع المستحقة -->
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">الفرع المستلم</label>
                <select name="to_branch_id"
                        class="h-11 w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 dark:bg-gray-900 dark:text-white">

                    <option disabled selected>اختر الفرع المستلم</option>

                    @foreach($branchesOwed as $otherBranchId => $row)
                        <option value="{{ $otherBranchId }}">
                            {{ $row['branch']->name }} - المبلغ المستحق: {{ number_format(abs($row['net']), 2) }} ر.ي
                        </option>
                    @endforeach

                </select>

                @error('to_branch_id')
                    <p class="text-xs text-error-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- مبلغ التسوية -->
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    مبلغ التسوية
                </label>
                <input type="number" step="0.01" min="0.01" name="amount"
                       class="h-11 w-full rounded-lg border px-4 py-2.5 text-sm dark:bg-gray-900 dark:text-white"
                       placeholder="0.00">

                @error('amount')
                    <p class="text-xs text-error-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- الوصف -->
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">الوصف (اختياري)</label>
                <textarea name="description" rows="3"
                          class="w-full rounded-lg border px-4 py-2.5 text-sm dark:bg-gray-900 dark:text-white"
                          placeholder="مثال: تسوية مستحقات بين فرع القطن والمكلا">
                </textarea>
            </div>

            <button type="submit"
                    class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                حفظ التسوية
            </button>
        </form>
    </div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const selectBranch = document.querySelector("select[name='to_branch_id']");
    const amountInput = document.querySelector("input[name='amount']");
    const owed = @json($branchesOwed);

    selectBranch.addEventListener("change", function () {
        const id = this.value;

        if (owed[id]) {
            const net = Math.abs(owed[id].net);

            amountInput.value = net;                 // يعبئ الحقل تلقائيًا
            amountInput.max = net;                   // يمنع إدخال مبلغ أكبر
            amountInput.placeholder = "الحد الأقصى: " + net + " ر.ي";
        }
    });
});
</script>

</div>
@endsection
