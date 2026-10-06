<x-app-layout title="Delivery Staff" header="Manage Delivery Staff">

    <div class="mb-4 bg-maroon-50 border border-maroon-200 text-maroon-700 text-xs font-medium px-4 py-3 rounded-lg">
        Delivery staff are not permanent employees. Only accounts created here can log in as delivery
        staff — there is no public sign-up. Deactivate an account instead of deleting it if they might
        come back; delete permanently removes their login.
    </div>

    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.delivery-staff.create') }}" class="inline-flex items-center gap-2 bg-maroon-600 hover:bg-maroon-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Add Delivery Staff
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-gray-500 border-b border-gray-100">
                        <th class="py-3 px-4 font-medium">Name</th>
                        <th class="py-3 px-4 font-medium">Contact</th>
                        <th class="py-3 px-4 font-medium">Active deliveries</th>
                        <th class="py-3 px-4 font-medium">Completed</th>
                        <th class="py-3 px-4 font-medium">Status</th>
                        <th class="py-3 px-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $person)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-3 px-4 text-gray-800 font-medium">{{ $person->name }}</td>
                        <td class="py-3 px-4 text-gray-500">
                            <div>{{ $person->email }}</div>
                            <div class="text-xs text-gray-400">{{ $person->phone }}</div>
                        </td>
                        <td class="py-3 px-4">
                            @if($person->active_deliveries_count > 0)
                            <x-badge color="yellow" :label="$person->active_deliveries_count . ' in transit'" />
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-gray-600">{{ $person->completed_deliveries_count }}</td>
                        <td class="py-3 px-4">
                            <x-badge :color="$person->is_active ? 'green' : 'gray'" :label="$person->is_active ? 'Active' : 'Deactivated'" />
                        </td>
                        <td class="py-3 px-4 text-right space-x-3">
                            <a href="{{ route('admin.delivery-staff.edit', $person) }}" class="text-maroon-600 text-xs font-medium hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.delivery-staff.destroy', $person) }}"
                                class="inline" onsubmit="return confirm('Permanently delete {{ $person->name }}\'s account? This cannot be undone.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-status-danger text-xs font-medium hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400">No delivery staff accounts yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>