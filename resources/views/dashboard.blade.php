<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="relative mb-6 w-full">
            <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">{{ __('Overview penjualan dan statistik') }}</flux:subheading>
            <flux:separator variant="subtle" />
        </div>

        @livewire('dashboard')
    </div>
</x-layouts.app>
