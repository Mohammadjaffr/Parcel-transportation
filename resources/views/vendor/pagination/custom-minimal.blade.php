@if ($paginator->hasPages())
    <div class="flex justify-between items-center px-6 py-4 bg-white border-t border-gray-100">
        <!-- عداد النتائج -->
        <div class="text-xs font-medium text-gray-500">
            عرض <span class="font-bold text-gray-800">{{ $paginator->firstItem() }}</span>
            إلى <span class="font-bold text-gray-800">{{ $paginator->lastItem() }}</span>
            من إجمالي <span class="font-bold text-gray-800">{{ $paginator->total() }}</span> نتيجة
        </div>

        <!-- أزرار التنقل -->
        <div class="flex gap-2 items-center">
            {{-- زر السابق --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-xs font-semibold text-gray-400 bg-gray-50 rounded-lg border border-gray-200 opacity-50 cursor-not-allowed select-none">
                    السابق
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   class="px-3 py-1.5 text-xs font-semibold text-gray-600 bg-gray-50 rounded-lg border border-gray-200 transition hover:bg-gray-100 hover:text-gray-900">
                    السابق
                </a>
            @endif

            {{-- عداد الصفحة الحالية --}}
            <span class="px-3 py-1.5 text-xs font-bold text-orange-600 bg-orange-50 rounded-lg border border-orange-200 select-none">
                صفحة {{ $paginator->currentPage() }} من {{ $paginator->lastPage() }}
            </span>

            {{-- زر التالي --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   class="px-3 py-1.5 text-xs font-semibold text-gray-600 bg-gray-50 rounded-lg border border-gray-200 transition hover:bg-gray-100 hover:text-gray-900">
                    التالي
                </a>
            @else
                <span class="px-3 py-1.5 text-xs font-semibold text-gray-400 bg-gray-50 rounded-lg border border-gray-200 opacity-50 cursor-not-allowed select-none">
                    التالي
                </span>
            @endif
        </div>
    </div>
@endif