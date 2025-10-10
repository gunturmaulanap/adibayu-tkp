<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Show item') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Show your item details') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>
    <div class="p-3">
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('items.index') }}"
                class="mb-4 px-4 py-2 text-sm font-medium text-white bg-gray-700 rounded-lg hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300">
                Back
            </a>

        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-shrink-0">
                    @if (!empty($item->image))
                        <img src="{{ strpos($item->image, 'http') === 0 ? $item->image : asset('storage/' . $item->image) }}"
                            alt="{{ $item->name }}" class="h-48 w-48 object-cover rounded" />
                    @else
                        <div
                            class="h-48 w-48 bg-gray-100 flex items-center justify-center text-sm text-gray-400 rounded">
                            No image</div>
                    @endif
                </div>

                <div class="flex-1">
                    <h2 class="text-2xl font-semibold text-gray-800">{{ $item->name }}</h2>
                    <p class="text-sm text-gray-500 mb-4">Code: <span
                            class="font-medium text-gray-700">{{ $item->code }}</span></p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="">
                            <p class="text-xs text-gray-500">Price</p>
                            <p class="text-lg font-medium text-gray-800">{{ $item->price_formatted }}</p>
                            </p>
                        </div>
                        <div class="">
                            <p class="text-xs text-gray-500">Stock</p>
                            <p class="text-lg font-medium text-gray-800">{{ $item->stock }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
