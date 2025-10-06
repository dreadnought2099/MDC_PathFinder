@extends('layouts.app')

@section('content')
    <x-floating-actions />
    <div class="container mx-auto px-4 py-8 max-w-6xl dark:text-gray-300">
        <!-- Header -->
        <div class="mb-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-300">
                    <span class="text-primary">Edit</span> Path Images
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ $path->fromRoom->name }} → {{ $path->toRoom->name }}
                </p>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('path-image.update-multiple', $path) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <input type="hidden" name="path_id" value="{{ $path->id }}">

            <!-- Images Grid -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 mb-8">
                @foreach ($pathImages as $index => $image)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border border-primary">
                        <input type="hidden" name="images[{{ $index }}][id]" value="{{ $image->id }}">

                        <!-- Image -->
                        <div class="relative mb-4">
                            <img src="{{ asset('storage/' . $image->image_file) }}" alt="Image {{ $image->image_order }}"
                                class="w-full h-48 object-cover rounded">

                            <!-- Order Badge -->
                            <div class="absolute top-2 left-2 bg-primary text-white px-2 py-1 rounded text-sm font-medium">
                                {{ $image->image_order }}
                            </div>

                            <!-- Delete Checkbox -->
                            <div class="absolute top-2 right-2">
                                <label class="flex items-center bg-white rounded px-2 py-1 text-sm cursor-pointer">
                                    <input type="checkbox" name="images[{{ $index }}][delete]" value="1"
                                        class="text-red-600 mr-1 custom-time-input">
                                    Delete
                                </label>
                            </div>
                        </div>

                        <!-- Controls -->
                        <div class="space-y-3">
                            <!-- Order -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Order
                                </label>
                                <input type="number" name="images[{{ $index }}][image_order]"
                                    value="{{ $image->image_order }}" min="1"
                                    class="w-full px-3 py-2 border border-primary rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>

                            <!-- Replace File -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Replace Image
                                </label>
                                <input type="file" name="images[{{ $image->id }}][image_file]" accept="image/*"
                                    class="w-full text-sm border border-primary rounded px-3 py-2 
                                      file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 
                                      file:bg-[#157ee1] fle:hover:bg-white file:text-white file:text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Actions -->
            <div
                class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sticky bottom-0 p-4 bg-white dark:bg-gray-800 rounded-md border border-primary">

                <button type="button" onclick="toggleAllDeletes()"
                    class="px-4 py-2 text-sm border-2 border-secondary text-white bg-secondary hover:text-secondary hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-secondary shadow-secondary-hover w-full sm:w-auto">
                    Toggle All Deletes
                </button>

                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <a href="{{ route('path.show', $path) }}"
                        class="px-4 py-2 text-sm border-2 border-gray-400 text-white bg-gray-400 hover:text-gray-500 hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-gray-300 shadow-cancel-hover w-full sm:w-auto text-center">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 text-sm border-2 border-primary text-white bg-primary hover:text-primary hover:bg-white rounded-md transition-all duration-300 cursor-pointer dark:hover:bg-gray-800 dark:hover:text-primary shadow-primary-hover w-full sm:w-auto">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Toggle all delete checkboxes
        function toggleAllDeletes() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name*="[delete]"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);

            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
        }

        // Confirm before submitting if deletions are selected
        document.querySelector('form').addEventListener('submit', function(e) {
            const deleteCheckboxes = document.querySelectorAll('input[type="checkbox"][name*="[delete]"]:checked');

            if (deleteCheckboxes.length > 0) {
                if (!confirm(`Delete ${deleteCheckboxes.length} image(s)? This cannot be undone.`)) {
                    e.preventDefault();
                }
            }
        });
    </script>
@endsection
