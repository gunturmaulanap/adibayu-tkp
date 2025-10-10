<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Item') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Create your all items') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>
    <div>
        <div class="p-3">
            <a href="{{ route('items.index') }}"
                class="mb-4 px-4 py-2 text-sm font-medium text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300">
                Back
            </a>
            <div class="w-150 mt-6">
                <form wire:submit="submit" class="mt-3 space-y-6">
                    <flux:input wire:model="code" label="Code" placeholder="Code" />
                    <flux:input wire:model="name" label="Name" placeholder="Name" />
                    <flux:input type="file" wire:model="image" label="Image" />
                    <div class="mt-2">
                        @if (isset($image) && method_exists($image, 'temporaryUrl'))
                            <img src="{{ $image->temporaryUrl() }}" alt="Preview"
                                class="h-24 w-24 object-cover rounded" loading="lazy" />
                        @else
                            <div
                                class="h-24 w-24 bg-gray-100 flex items-center justify-center text-sm text-gray-400 rounded">
                                No image</div>
                        @endif
                    </div>
                    <flux:input wire:model="price" label="Price" placeholder="Price" type="number" />
                    <flux:input wire:model="stock" label="Stock" placeholder="Stock" type="number" />
                    <flux:button type="submit" variant="primary">Submit</flux:button>

                </form>
            </div>

        </div>
    </div>
</div>
</div>
