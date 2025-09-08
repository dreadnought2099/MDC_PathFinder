@extends('layouts.app')

@section('content')
    <div class="container mt-4 mx-auto max-w-6xl overflow-y-auto h-[80vh]">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-900 mb-8 text-center sticky top-0 z-48">
            <h1 class="text-3xl font-bold text-gray-800 mb-2 dark:text-gray-100">
                <span class="text-primary">Path</span> Management
            </h1>
            <p class="text-gray-600 dark:text-gray-300">Manage paths between offices</p>
            <div class="mt-4 flex justify-center">
                {{ $paths->appends(request()->query())->links('pagination::tailwind') }}
            </div>
            <div class="py-4">
                <x-sort-by :route="route('path.index')" :fields="[
                    'id' => 'ID',
                    'from_room_id' => 'From Room',
                    'to_room_id' => 'To Room',
                    'created_at' => 'Created At',
                ]" :current-sort="$sort" :current-direction="$direction" />
            </div>
        </div>



        <!-- Floating Actions -->
        <div class="mb-6">
            <x-floating-actions />
        </div>

        <!-- Paths Table -->
        <div class="bg-white rounded-xl shadow-sm border-2 border-primary overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full dark:bg-gray-800">
                    <thead>
                        <tr
                            class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:from-gray-800 dark:to-gray-700 dark:border-gray-600">
                            <th
                                class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                ID</th>
                            <th
                                class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                From Room</th>
                            <th
                                class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                To Room</th>
                            <th
                                class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                Path Direction</th>
                            <th
                                class="px-6 py-4 text-left text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                Created At</th>
                            <th
                                class="px-6 py-4 text-right text-sm text-gray-700 uppercase tracking-wide dark:text-gray-300">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($paths as $path)
                            @if ($path->fromRoom && $path->toRoom)
                                <tr
                                    class="hover:bg-gray-50 transition-colors duration-200 dark:bg-gray-700 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-blue-100 text-primary rounded-full text-sm">
                                            {{ $path->id }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-blue-100 text-primary rounded-full text-sm">
                                            {{ $path->fromRoom->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                                            {{ $path->toRoom->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <i class="fas fa-arrow-right text-gray-400 mr-1"></i>
                                        <small class="text-gray-500">
                                            {{ $path->fromRoom->name }} → {{ $path->toRoom->name }}
                                        </small>
                                    </td>
                                    <td class="px-6 py-4">
                                        <small class="text-gray-500">
                                            {{ $path->created_at ? $path->created_at->format('M d, Y H:i') : 'N/A' }}
                                        </small>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end space-x-3">
                                            <a href="{{ route('path.show', $path->id) }}"
                                                class="hover-underline text-primary hover:scale-105 transform transition duration-200">View</a>
                                            <a href="{{ route('path-image.create', $path->id) }}"
                                                class="hover-underline-tertiary text-tertiary hover:scale-105 transform transition duration-200">Add
                                                Path Images</a>
                                            <a href="{{ route('path-image.edit', $path->id) }}"
                                                class="hover-underline-edit text-edit hover:scale-105 transform transition duration-200">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">No paths found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
