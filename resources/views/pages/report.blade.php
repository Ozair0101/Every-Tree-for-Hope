@extends('layouts.layout')
@section('title', 'Report Page')
@section('content')
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">
            <div class="flex flex-1 justify-center py-5 sm:px-10 lg:px-20 xl:px-40">
                <div class="layout-content-container flex flex-col w-full max-w-5xl flex-1">
                    <!-- TopNavBar -->
                    <main class="flex-grow px-4 py-8 sm:py-12">
                        <!-- PageHeading -->
                        <div class="flex flex-wrap justify-between gap-3 p-4">
                            <h1
                                class="text-[#111714] dark:text-[#f6f8f7] text-4xl sm:text-5xl font-black leading-tight tracking-[-0.033em] w-full text-center">
                                Our Impact Report</h1>
                        </div>
                        <!-- BodyText -->
                        <p
                            class="text-center text-base font-normal leading-normal pb-6 pt-2 px-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 max-w-3xl mx-auto">
                            This table shows the cumulative results of community efforts, events, and donations. Thank
                            you for helping us green our neighborhoods.
                        </p>
                        <!-- Chips -->
                        <div class="flex flex-wrap items-center justify-center gap-3 p-3 overflow-x-hidden">
                            <button
                                class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-primary/20 dark:bg-primary/30 px-4">
                                <p class="text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal">Filter
                                    by Year</p>
                                <span class="material-symbols-outlined text-[#111714] dark:text-[#f6f8f7]"
                                    style="font-size: 20px;">keyboard_arrow_down</span>
                            </button>
                            <button
                                class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-primary/20 dark:bg-primary/30 px-4">
                                <p class="text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal">Filter
                                    by Region</p>
                                <span class="material-symbols-outlined text-[#111714] dark:text-[#f6f8f7]"
                                    style="font-size: 20px;">keyboard_arrow_down</span>
                            </button>
                        </div>
                        <!-- Table -->
                        <div class="px-4 py-6 @container">
                            <div
                                class="overflow-hidden rounded-xl border border-primary/20 dark:border-primary/30 bg-white dark:bg-background-dark shadow-sm">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead class="hidden md:table-header-group bg-primary/20 dark:bg-primary/30">
                                            <tr>
                                                <th class="px-6 py-4 text-left text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal"
                                                    scope="col">Event/Initiative</th>
                                                <th class="px-6 py-4 text-left text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal"
                                                    scope="col">Location</th>
                                                <th class="px-6 py-4 text-left text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal"
                                                    scope="col">Date</th>
                                                <th class="px-6 py-4 text-left text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal text-right"
                                                    scope="col">Trees Planted</th>
                                                <th class="px-6 py-4 text-left text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal text-right"
                                                    scope="col">Volunteers</th>
                                                <th class="px-6 py-4 text-left text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal"
                                                    scope="col">Sponsor/Partner</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-primary/20 dark:divide-primary/30">
                                            <tr
                                                class="block md:table-row p-4 border-b border-primary/20 dark:border-primary/30 md:border-none">
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714] dark:text-[#f6f8f7] text-sm font-medium md:font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Event/Initiative">Greenwood Park Planting</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Location">Central Park, Springfield</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Date">Oct 22, 2023</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal md:text-right before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Trees Planted">150</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal md:text-right before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Volunteers">45</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Sponsor/Partner">City Parks Dept.</td>
                                            </tr>
                                            <tr
                                                class="block md:table-row p-4 border-b border-primary/20 dark:border-primary/30 md:border-none">
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714] dark:text-[#f6f8f7] text-sm font-medium md:font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Event/Initiative">Oakridge Community Day</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Location">Oakridge, Meadowville</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Date">Sep 15, 2023</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal md:text-right before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Trees Planted">85</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal md:text-right before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Volunteers">30</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Sponsor/Partner">GreenCo</td>
                                            </tr>
                                            <tr
                                                class="block md:table-row p-4 border-b border-primary/20 dark:border-primary/30 md:border-none">
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714] dark:text-[#f6f8f7] text-sm font-medium md:font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Event/Initiative">Riverbank Restoration</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Location">River's Edge, Brookside</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Date">Aug 05, 2023</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal md:text-right before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Trees Planted">220</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal md:text-right before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Volunteers">60</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Sponsor/Partner">Nature's Keepers</td>
                                            </tr>
                                            <tr
                                                class="block md:table-row p-4 border-b border-primary/20 dark:border-primary/30 md:border-none">
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714] dark:text-[#f6f8f7] text-sm font-medium md:font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Event/Initiative">Maple Street Beautification</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Location">Maple Street, Springfield</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Date">Jul 11, 2023</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal md:text-right before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Trees Planted">50</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal md:text-right before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Volunteers">25</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Sponsor/Partner">Local Homeowners Assn.</td>
                                            </tr>
                                            <tr class="block md:table-row p-4">
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714] dark:text-[#f6f8f7] text-sm font-medium md:font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Event/Initiative">Annual Earth Day Event</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Location">City Center, Meadowville</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Date">Apr 22, 2023</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal md:text-right before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Trees Planted">500</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal md:text-right before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Volunteers">120</td>
                                                <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                    data-label="Sponsor/Partner">EcoSolutions Inc.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Pagination and CTA -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-6 px-4 py-8">
                            <div class="flex items-center gap-2">
                                <button
                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-primary/20 dark:border-primary/30 hover:bg-primary/20 dark:hover:bg-primary/30 transition-colors">
                                    <span class="material-symbols-outlined text-[#111714] dark:text-[#f6f8f7]"
                                        style="font-size: 20px;">chevron_left</span>
                                </button>
                                <button
                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-primary/20 dark:border-primary/30 bg-primary/20 dark:bg-primary/30 text-sm font-bold text-primary dark:text-primary">1</button>
                                <button
                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-primary/20 dark:border-primary/30 text-sm font-medium text-[#111714]/80 dark:text-[#f6f8f7]/80 hover:bg-primary/20 dark:hover:bg-primary/30 transition-colors">2</button>
                                <button
                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-primary/20 dark:border-primary/30 text-sm font-medium text-[#111714]/80 dark:text-[#f6f8f7]/80 hover:bg-primary/20 dark:hover:bg-primary/30 transition-colors">3</button>
                                <button
                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-primary/20 dark:border-primary/30 hover:bg-primary/20 dark:hover:bg-primary/30 transition-colors">
                                    <span class="material-symbols-outlined text-[#111714] dark:text-[#f6f8f7]"
                                        style="font-size: 20px;">chevron_right</span>
                                </button>
                            </div>
                            <button
                                class="flex w-full sm:w-auto min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-opacity-90 transition-opacity">
                                <span>Join an Event Near You</span>
                                <span class="material-symbols-outlined">arrow_forward</span>
                            </button>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
@endsection
