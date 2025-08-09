@props(['items', 'routePrefix', 'title', 'emptyMessage'])

<div class="mb-12">
    <h2 class="text-2xl font-semibold text-center mb-6"><span class="text-primary">{{ $title }}</span></h2>

    @if ($items->isEmpty())
        <div class="text-center text-gray-600">{{ $emptyMessage }}</div>
    @else
        <div class="bg-white rounded-lg shadow-md p-6 overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100 text-sm">
                    <tr>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Deleted At</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $item->name }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $item->deleted_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-2 space-x-2">

                                <!-- Restore Form & Button -->
                                <form action="{{ route("{$routePrefix}.restore", $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="button" onclick="showModal('restoreModal-{{ $routePrefix }}-{{ $item->id }}')"
                                        class="text-primary hover-underline cursor-pointer">
                                        Restore
                                    </button>
                                </form>

                                <!-- Restore Modal -->
                                <div id="restoreModal-{{ $routePrefix }}-{{ $item->id }}"
                                    class="modal hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center"
                                    onclick="closeModal(event, this)">
                                    <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full"
                                        onclick="event.stopPropagation()">
                                        <h2 class="text-lg mb-4"><span class="text-primary">Restore</span> {{ $title }}</h2>
                                        <p class="mb-4 text-gray-700">Are you sure you want to restore
                                            <span class="text-primary">{{ $item->name }}</span>?
                                        </p>
                                        <form action="{{ route("{$routePrefix}.restore", $item->id) }}" method="POST">
                                            @csrf
                                            <div class="flex justify-end gap-2">
                                                <button type="button"
                                                    onclick="hideModal('restoreModal-{{ $routePrefix }}-{{ $item->id }}')"
                                                    class="px-4 py-2 bg-gray-300 hover:text-white hover:bg-gray-400 rounded transition-all duration-300 cursor-pointer">
                                                    Cancel
                                                </button>
                                                <button type="submit"
                                                    class="px-4 py-2 bg-primary text-white rounded hover:bg-white hover:text-primary border hover:border-primary transition-all duration-300 cursor-pointer">
                                                    Restore
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Permanently Delete Button -->
                                <button type="button" onclick="showModal('deleteModal-{{ $routePrefix }}-{{ $item->id }}')"
                                    class="text-secondary hover-underline-delete cursor-pointer">
                                    Delete Permanently
                                </button>

                                <!-- Delete Modal -->
                                <div id="deleteModal-{{ $routePrefix }}-{{ $item->id }}"
                                    class="modal hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center"
                                    onclick="closeModal(event, this)">
                                    <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full"
                                        onclick="event.stopPropagation()">
                                        <h2 class="text-lg mb-4">Confirm<span class="text-secondary"> Permanent Deletion</span></h2>
                                        <p class="mb-4 text-gray-700">Are you sure you want to permanently delete
                                            <span class="text-secondary">{{ $item->name }}</span>? This action cannot be undone.</p>
                                        <form action="{{ route("{$routePrefix}.forceDelete", $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="flex justify-end gap-2">
                                                <button type="button"
                                                    onclick="hideModal('deleteModal-{{ $routePrefix }}-{{ $item->id }}')"
                                                    class="px-4 py-2 bg-gray-300 hover:text-white hover:bg-gray-400 rounded transition-all duration-300 cursor-pointer">
                                                    Cancel
                                                </button>
                                                <button type="submit"
                                                    class="px-4 py-2 bg-secondary text-white rounded hover:bg-white hover:text-secondary border-2 border-secondary transition-all duration-300 cursor-pointer">
                                                    Delete
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
