<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($pathImages as $image)
        <div class="image-card bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 border border-primary"
            data-image-id="{{ $image->id }}">
            <input type="hidden" name="images[{{ $image->id }}][id]" value="{{ $image->id }}">

            <!-- Image - Larger height -->
            <div class="relative mb-4">
                <img src="{{ asset('storage/' . $image->image_file) }}" alt="Image {{ $image->image_order }}"
                    class="w-full h-48 sm:h-56 object-cover rounded-lg">

                <!-- Order Badge -->
                <div
                    class="absolute top-2 left-2 bg-primary text-white px-2.5 py-1 rounded text-sm font-medium shadow-md">
                    <span class="order-badge-value">{{ $image->image_order }}</span>
                </div>

                <!-- Delete Checkbox -->
                <div class="absolute top-2 right-2">
                    <label
                        class="flex items-center bg-white dark:bg-gray-800 rounded px-2.5 py-1 text-sm cursor-pointer text-red-600 shadow-md hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <input type="checkbox" name="images[{{ $image->id }}][delete]" value="1"
                            class="delete-checkbox text-red-600 mr-1.5 w-4 h-4" data-image-id="{{ $image->id }}">
                        Delete
                    </label>
                </div>
            </div>

            <!-- Controls -->
            <div class="space-y-3">
                <!-- Order -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Display Order
                    </label>
                    <input type="number" name="images[{{ $image->id }}][image_order]"
                        value="{{ $image->image_order }}" min="1"
                        class="order-input w-full px-3 py-2 text-sm border border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                        data-image-id="{{ $image->id }}">
                </div>

                <!-- Replace File -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Replace Image
                    </label>
                    <div class="relative">
                        <input type="file" name="images[{{ $image->id }}][image_file]" accept="image/*"
                            class="image-file-input w-full text-sm border border-primary rounded-lg px-3 py-2 
                                    file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 
                                    file:bg-[#157ee1] file:hover:bg-[#1268c4] file:text-white file:text-sm file:cursor-pointer
                                    focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary cursor-pointer"
                            data-image-id="{{ $image->id }}">

                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                            Max 10MB. Compressed to 2000px.
                        </p>

                        <!-- Clear button -->
                        <button type="button"
                            class="clear-file-btn hidden absolute right-2 top-1/2 -translate-y-1/2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-colors duration-200 shadow-md"
                            data-image-id="{{ $image->id }}" title="Clear selected file">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <!-- Error message -->
                    <p class="file-size-error hidden text-red-600 text-xs mt-1" data-image-id="{{ $image->id }}">
                    </p>

                    <!-- File name display -->
                    <p class="file-name-display hidden text-gray-600 dark:text-gray-400 text-xs mt-1 font-medium"
                        data-image-id="{{ $image->id }}"></p>
                </div>
            </div>
        </div>
    @endforeach
</div>