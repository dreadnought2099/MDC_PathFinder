@extends('layouts.app')

@section('content')
    <div class="container mt-4 mx-auto max-w-6xl overflow-y-auto h-[80vh]">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-900 mb-6 text-center sticky top-0 z-48 px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-1 dark:text-gray-100">
                <img-reveal>
                    <span class="trigger-text text-primary">Path</span>
                    <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/gif/girl-map.gif"
                        alt="GIF" class="reveal-img">
                </img-reveal>
                Management
            </h1>
            <p class="text-sm sm:text-base md:text-lg text-gray-600 dark:text-gray-300 mb-3">
                Manage paths between offices
            </p>

            <div class="py-2 sm:py-4">
                <x-filter-header :route="route('path.index')" placeholder="paths" :fields="[
                    'from_room' => 'From Office',
                    'to_room' => 'To Office',
                    'created_at' => 'Date Created',
                    'updated_at' => 'Date Modified',
                    'images_count' => 'Images Uploaded',
                ]" :currentSort="$sort" :currentDirection="$direction"
                    :currentSearch="$search" />
            </div>

            <div id="records-table">
                @include('pages.admin.paths.partials.path-table', ['paths' => $paths])
            </div>
        </div>
    </div>
@endsection
