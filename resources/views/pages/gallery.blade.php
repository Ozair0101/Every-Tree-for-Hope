@extends('layouts.app')

@section('content')
<div class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none">
    <h1 class="mb-1 font-medium">Gallery</h1>
    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">
        Take a look at our work in action through photos from our events and projects.
    </p>
    <p class="mb-4 text-[#706f6c] dark:text-[#A1A09A]">
        See the impact we're making in communities around the world.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-8">
        <div class="aspect-square bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
            <div class="w-full h-full flex items-center justify-center text-gray-500 dark:text-gray-400">
                Tree Planting Event
            </div>
        </div>
        <div class="aspect-square bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
            <div class="w-full h-full flex items-center justify-center text-gray-500 dark:text-gray-400">
                Community Garden
            </div>
        </div>
        <div class="aspect-square bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
            <div class="w-full h-full flex items-center justify-center text-gray-500 dark:text-gray-400">
                Volunteer Group
            </div>
        </div>
        <div class="aspect-square bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
            <div class="w-full h-full flex items-center justify-center text-gray-500 dark:text-gray-400">
                Before & After
            </div>
        </div>
        <div class="aspect-square bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
            <div class="w-full h-full flex items-center justify-center text-gray-500 dark:text-gray-400">
                Children Learning
            </div>
        </div>
        <div class="aspect-square bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
            <div class="w-full h-full flex items-center justify-center text-gray-500 dark:text-gray-400">
                Harvest Time
            </div>
        </div>
    </div>

    <div class="mt-8">
        <a href="{{ route('donators') }}" class="inline-block px-5 py-1.5 dark:bg-[#eeeeec] dark:border-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white dark:hover:border-white hover:bg-black hover:border-black bg-[#1b1b18] rounded-sm border border-black text-white text-sm leading-normal">
            Meet Our Donors
        </a>
    </div>
</div>
<div class="bg-[#fff2f2] dark:bg-[#1D0002] relative lg:-ml-px -mb-px lg:mb-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg aspect-[335/376] lg:aspect-auto w-full lg:w-[438px] shrink-0 overflow-hidden">
    <div class="absolute inset-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"></div>
</div>
@endsection
