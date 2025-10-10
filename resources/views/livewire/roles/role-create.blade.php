<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Role') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Create your all items') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>
    <div>
        <div class="p-3">
            <a href="{{ route('roles.index') }}"
                class="mb-4 px-4 py-2 text-sm font-medium text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300">
                Back
            </a>
            <div class="w-150 mt-6">
                <form wire:submit="submit" class="mt-3 space-y-6">
                    <flux:input wire:model="name" label="Name" placeholder="Name" />
                    <flux:checkbox.group wire:model="permissions" label="Permissions">
                        @foreach ($allPermissions as $permission)
                            <flux:checkbox label="{{ $permission->name }}" value="{{ $permission->name }}" />
                        @endforeach
                    </flux:checkbox.group>

                    <flux:button type="submit" variant="primary">Submit</flux:button>
                </form>
            </div>

        </div>
    </div>
</div>
</div>
