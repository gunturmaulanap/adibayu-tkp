@if ($paginator && $paginator->lastPage() > 1)
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-600">
            Showing <span class="font-medium">{{ $paginator->firstItem() }}</span> to <span
                class="font-medium">{{ $paginator->lastItem() }}</span> of <span
                class="font-medium">{{ $paginator->total() }}</span> results
        </div>

        <div>
            {{ $paginator->links() }}
        </div>
    </div>
@endif
