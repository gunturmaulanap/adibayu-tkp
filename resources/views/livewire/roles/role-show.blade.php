<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Show Role') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('this page is for show role') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>
    <div>
        <div class="p-3">
            <div class="flex items-center justify-between mb-6">
                <div class="flex gap-2">
                    <a href="{{ route('roles.index') }}"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-700 rounded-lg hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300">Back</a>
                    <a href="{{ route('roles.edit', $role->id) }}"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">Edit</a>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center gap-6">
                    {{-- <div class="flex-shrink-0">
                        @if (!empty($role->avatar))
                            <img src="{{ strpos($role->avatar, 'http') === 0 ? $role->avatar : asset('storage/' . $role->avatar) }}"
                                alt="{{ $role->name }}" class="h-20 w-20 rounded-full object-cover" />
                        @else
                            <div
                                class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center text-xl font-semibold text-gray-600">
                                {{ strtoupper(substr($role->name, 0, 1)) }}</div>
                        @endif
                    </div> --}}

                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">{{ $role->name }}</h2>

                        @if (isset($role->created_at))
                            <p class="text-xs text-gray-400 mt-2">Joined: {{ $role->created_at->format('d M Y') }}</p>
                        @endif
                        <h3 class="text-lg font-medium text-gray-800 py-2 mt-3">Permissions:</h3>
                        @if ($role->permissions)
                            @foreach ($role->permissions as $permission)
                                <flux:badge>
                                    {{ $permission->name }}
                                </flux:badge>
                            @endforeach
                        @endif
                    </div>
                    <div class="px-4 mt-6 ">

                    </div>

                </div>

            </div>

        </div>
    </div>
</div>
</div>
