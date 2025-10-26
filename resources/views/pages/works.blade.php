@extends('layouts.layout')
@section('title', 'Works Page')
@section('content')
    <main class="flex-grow">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
            <!-- Page Heading -->
            <div class="flex flex-wrap justify-between gap-4 p-4">
                <div class="flex flex-col gap-3">
                    <p class="text-[#1A535C] dark:text-white text-4xl font-black leading-tight tracking-[-0.033em]">
                        See Your Impact in Action</p>
                    <p class="text-[#648772] dark:text-gray-400 text-base font-normal leading-normal max-w-2xl">
                        Explore our ongoing and completed projects around the globe and see how your support is
                        helping to green our neighborhoods.</p>
                </div>
            </div>
            <!-- Stats -->
            <div class="flex flex-wrap gap-4 p-4">
                <div
                    class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-lg p-6 border border-[#E0E0E0] dark:border-gray-700 bg-white dark:bg-background-dark/50">
                    <p class="text-[#2B2B2B] dark:text-gray-300 text-base font-medium leading-normal">Total Trees
                        Planted Globally</p>
                    <p class="text-[#1A535C] dark:text-white tracking-light text-3xl font-bold leading-tight">
                        1,245,890</p>
                    <p class="text-[#4ECDC4] text-base font-medium leading-normal">+12.5% this month</p>
                </div>
                <div
                    class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-lg p-6 border border-[#E0E0E0] dark:border-gray-700 bg-white dark:bg-background-dark/50">
                    <p class="text-[#2B2B2B] dark:text-gray-300 text-base font-medium leading-normal">Total
                        Donations Raised</p>
                    <p class="text-[#1A535C] dark:text-white tracking-light text-3xl font-bold leading-tight">$3.4M
                    </p>
                    <p class="text-[#4ECDC4] text-base font-medium leading-normal">+8.2% this month</p>
                </div>
                <div
                    class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-lg p-6 border border-[#E0E0E0] dark:border-gray-700 bg-white dark:bg-background-dark/50">
                    <p class="text-[#2B2B2B] dark:text-gray-300 text-base font-medium leading-normal">Active
                        Volunteers</p>
                    <p class="text-[#1A535C] dark:text-white tracking-light text-3xl font-bold leading-tight">15,230
                    </p>
                    <p class="text-[#4ECDC4] text-base font-medium leading-normal">+5.1% this month</p>
                </div>
            </div>
            <!-- Main Content: Map and Projects -->
            <div class="flex flex-col lg:flex-row gap-6 mt-6 p-4">
                <!-- Project List Sidebar -->
                <aside class="w-full lg:w-1/3 lg:max-w-md flex flex-col gap-4">
                    <!-- Filter Chips -->
                    <div class="flex gap-3 flex-wrap">
                        <button
                            class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-white dark:bg-gray-700 dark:text-white px-4 border border-[#E0E0E0] dark:border-gray-600">
                            <p class="text-sm font-medium leading-normal">Status: All</p>
                            <span class="material-symbols-outlined text-xl"> expand_more </span>
                        </button>
                        <button
                            class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-white dark:bg-gray-700 dark:text-white px-4 border border-[#E0E0E0] dark:border-gray-600">
                            <p class="text-sm font-medium leading-normal">Region: All</p>
                            <span class="material-symbols-outlined text-xl"> expand_more </span>
                        </button>
                        <button
                            class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-white dark:bg-gray-700 dark:text-white px-4 border border-[#E0E0E0] dark:border-gray-600">
                            <p class="text-sm font-medium leading-normal">Type: All</p>
                            <span class="material-symbols-outlined text-xl"> expand_more </span>
                        </button>
                    </div>
                    <!-- Project Cards -->
                    <div class="flex flex-col gap-4 max-h-[600px] lg:max-h-[70vh] overflow-y-auto pr-2">
                        <div
                            class="p-4 rounded-xl border-2 border-primary bg-white dark:bg-gray-800 shadow-md cursor-pointer">
                            <div class="flex flex-col gap-3">
                                <img class="w-full h-40 object-cover rounded-lg"
                                    data-alt="Sunlight filtering through a lush green forest canopy."
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuDQ5Hrync-u_nS5klnVE26SI_t7TcXHqNp6g6588i707peVUkKo9s1BUd29c365aA3N7XS0oJoLxZcQtTPzffYrQ6gUn9gjbqzPBbxMvsAYhrYtIA2ljzhjZ6CNVLOmgmINS0vSTcSYnwPpyXWDfQ4yFRstzThkBs7fM_suDiBtDO8tngrCLfRke3MKq3i-ZHRbWHZGReRSaUkaeDvLTbFxjkXZ5FiHa9JwH9Ka7nMybeGrKC24mPA5QRQPYCXguLXoFUuqkMRWXWE" />
                                <h3 class="font-bold text-lg text-[#1A535C] dark:text-white">Amazon Rainforest
                                    Reforestation</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Manaus, Brazil</p>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                    <div class="bg-[#4ECDC4] h-2.5 rounded-full" style="width: 75%"></div>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="font-semibold text-[#1A535C] dark:text-gray-300">5,800 / 7,500
                                        Trees</span>
                                    <span class="font-bold text-gray-600 dark:text-gray-400">75% Complete</span>
                                </div>
                            </div>
                        </div>
                        <div
                            class="p-4 rounded-xl border border-[#E0E0E0] dark:border-gray-700 bg-white dark:bg-background-dark/50 hover:border-primary dark:hover:border-primary transition-colors cursor-pointer">
                            <div class="flex flex-col gap-3">
                                <img class="w-full h-40 object-cover rounded-lg"
                                    data-alt="Community volunteers planting saplings in an urban park."
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuD6wqgtYUXMoXIdsW32R3ePGvDV2cUO2L6uIo0RXrreZNE9tEuLrFb4oFq0T_vDYO95KZ8SrDD7VVlien9iuUo60DWknvDZ4NpA4lAeUCWJh2kHmRYHWy2x12-AHaaL0EWwZBDJFM6hYyzcQWwzDVktpxE8Qt0E3kEsdS85deuZXiO9pU4cX0cYSTBwcmXMKYGQBDU4B2Uvr24c27dvz4IIsoUpfVkuojdHuRGFr9lu0gYQ-ceGQGi49TVRosuNvXuhZSAQtfIEhAc" />
                                <h3 class="font-bold text-lg text-[#1A535C] dark:text-white">Oakwood Community
                                    Garden</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">California, USA</p>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                    <div class="bg-[#4ECDC4] h-2.5 rounded-full" style="width: 100%"></div>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="font-semibold text-[#1A535C] dark:text-gray-300">500 / 500
                                        Trees</span>
                                    <span class="font-bold text-gray-600 dark:text-gray-400">Completed</span>
                                </div>
                            </div>
                        </div>
                        <div
                            class="p-4 rounded-xl border border-[#E0E0E0] dark:border-gray-700 bg-white dark:bg-background-dark/50 hover:border-primary dark:hover:border-primary transition-colors cursor-pointer">
                            <div class="flex flex-col gap-3">
                                <img class="w-full h-40 object-cover rounded-lg"
                                    data-alt="Aerial view of a mangrove forest with winding rivers."
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuB7CF1r65OEwcMSI3RReuTIMJuCvg7rsTIDgAR-gs-OMajyXdHseAGfQFMt_XdrQSoH_izMOOthR7a7E-FBYpQXyd8GNc2qjk7YeN-JJfebM3XIV_x7UOw2irIHkpvz6tLUa5Ad2GNZaHF2C1yanus_tfXLjkJ5gGgdZ0hMb2ZmhD6Rw1HpNyURidKE7modaDEyHYq4kExJhLsaDncF-GL-2GN0221VhWUdzWGbzvCCcuOigS9ALQDAvdIF6mr1reiK03kqBFIHaY8" />
                                <h3 class="font-bold text-lg text-[#1A535C] dark:text-white">Sundarbans Mangrove
                                    Restoration</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">West Bengal, India</p>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                    <div class="bg-[#4ECDC4] h-2.5 rounded-full" style="width: 40%"></div>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="font-semibold text-[#1A535C] dark:text-gray-300">12,000 / 30,000
                                        Trees</span>
                                    <span class="font-bold text-gray-600 dark:text-gray-400">40% Complete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
                <!-- Map -->
                <main class="w-full lg:w-2/3 flex-1">
                    <div class="bg-cover bg-center flex min-h-[500px] flex-1 flex-col justify-between p-4 sm:p-6 rounded-xl overflow-hidden"
                        data-location="World" style="background-image: url('https://placeholder.pics/svg/300');">
                        <label class="flex flex-col min-w-40 sm:max-w-xs h-12">
                            <div class="flex w-full flex-1 items-stretch rounded-lg h-full shadow-lg">
                                <div
                                    class="text-[#648772] flex border-none bg-white items-center justify-center pl-4 rounded-l-lg border-r-0">
                                    <span class="material-symbols-outlined"> search </span>
                                </div>
                                <input
                                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#111714] focus:outline-0 focus:ring-0 border-none bg-white focus:border-none h-full placeholder:text-[#648772] px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal"
                                    placeholder="Search for a project" value="" />
                            </div>
                        </label>
                        <div class="flex flex-col items-end gap-3">
                            <div class="flex flex-col gap-0.5">
                                <button
                                    class="flex size-10 items-center justify-center rounded-t-lg bg-white shadow-[0_2px_4px_rgba(0,0,0,0.1)] text-[#111714]">
                                    <span class="material-symbols-outlined"> add </span>
                                </button>
                                <button
                                    class="flex size-10 items-center justify-center rounded-b-lg bg-white shadow-[0_2px_4px_rgba(0,0,0,0.1)] text-[#111714]">
                                    <span class="material-symbols-outlined"> remove </span>
                                </button>
                            </div>
                            <button
                                class="flex size-10 items-center justify-center rounded-lg bg-white shadow-[0_2px_4px_rgba(0,0,0,0.1)] text-[#111714]">
                                <span class="material-symbols-outlined"> my_location </span>
                            </button>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </main>
@endsection
