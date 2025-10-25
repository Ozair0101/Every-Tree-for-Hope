<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Our Impact Report - Every Tree for Hope</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#19b357",
                        "background-light": "#f6f8f7",
                        "background-dark": "#112118",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            body {
                @apply font-display bg-background-light dark:bg-background-dark text-[#111714] dark:text-[#f6f8f7];
            }
        }
    </style>
</head>

<body class="font-display bg-background-light dark:bg-background-dark">
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">
            <div class="flex flex-1 justify-center py-5 sm:px-10 lg:px-20 xl:px-40">
                <div class="layout-content-container flex flex-col w-full max-w-5xl flex-1">
                    <!-- TopNavBar -->
                    <header
                        class="flex items-center justify-between whitespace-nowrap border-b border-solid border-primary/20 dark:border-primary/30 px-4 sm:px-10 py-3">
                        <div class="flex items-center gap-4 text-primary">
                            <div class="size-5">
                                <svg fill="currentColor" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z">
                                    </path>
                                </svg>
                            </div>
                            <h2
                                class="text-[#111714] dark:text-[#f6f8f7] text-lg font-bold leading-tight tracking-[-0.015em]">
                                Every Tree for Hope</h2>
                        </div>
                        <div class="hidden md:flex flex-1 justify-end gap-8">
                            <div class="flex items-center gap-9">
                                <a class="text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal hover:text-primary transition-colors"
                                    href="#">Home</a>
                                <a class="text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal hover:text-primary transition-colors"
                                    href="#">Events</a>
                                <a class="text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal hover:text-primary transition-colors"
                                    href="#">Donate</a>
                                <a class="text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal hover:text-primary transition-colors"
                                    href="#">Learn</a>
                                <a class="text-primary dark:text-primary text-sm font-bold leading-normal"
                                    href="#">Report</a>
                            </div>
                            <button
                                class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-opacity-90 transition-opacity">
                                <span class="truncate">Get Involved</span>
                            </button>
                        </div>
                        <div class="md:hidden">
                            <button class="p-2 rounded-lg hover:bg-primary/20 transition-colors">
                                <span class="material-symbols-outlined text-[#111714] dark:text-[#f6f8f7]">menu</span>
                            </button>
                        </div>
                    </header>
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
                    <!-- Footer -->
                    <footer
                        class="mt-auto border-t border-primary/20 dark:border-primary/30 px-4 sm:px-10 py-8 text-[#111714]/60 dark:text-[#f6f8f7]/60">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                            <div class="flex flex-col gap-4 items-center md:items-start">
                                <div class="flex items-center gap-3 text-primary">
                                    <div class="size-5">
                                        <svg fill="currentColor" viewbox="0 0 48 48"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h2 class="text-[#111714] dark:text-[#f6f8f7] text-lg font-bold">Every Tree for
                                        Hope</h2>
                                </div>
                                <p class="text-sm text-center md:text-left">Planting a greener tomorrow, one tree at a
                                    time.</p>
                            </div>
                            <div class="text-center md:text-left">
                                <h3 class="font-bold text-[#111714] dark:text-[#f6f8f7] mb-3">Quick Links</h3>
                                <ul class="space-y-2 text-sm">
                                    <li><a class="hover:text-primary transition-colors" href="#">Home</a></li>
                                    <li><a class="hover:text-primary transition-colors" href="#">About Us</a>
                                    </li>
                                    <li><a class="hover:text-primary transition-colors" href="#">Events</a></li>
                                    <li><a class="hover:text-primary transition-colors" href="#">Contact</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="text-center md:text-left">
                                <h3 class="font-bold text-[#111714] dark:text-[#f6f8f7] mb-3">Get Involved</h3>
                                <ul class="space-y-2 text-sm">
                                    <li><a class="hover:text-primary transition-colors" href="#">Donate</a></li>
                                    <li><a class="hover:text-primary transition-colors" href="#">Volunteer</a>
                                    </li>
                                    <li><a class="hover:text-primary transition-colors" href="#">Partner with
                                            Us</a></li>
                                    <li><a class="hover:text-primary transition-colors" href="#">Start a
                                            Chapter</a></li>
                                </ul>
                            </div>
                            <div class="text-center md:text-left">
                                <h3 class="font-bold text-[#111714] dark:text-[#f6f8f7] mb-3">Follow Us</h3>
                                <div class="flex justify-center md:justify-start gap-4">
                                    <a class="hover:text-primary transition-colors" data-alt="Facebook icon"
                                        href="#">FB</a>
                                    <a class="hover:text-primary transition-colors" data-alt="Twitter icon"
                                        href="#">TW</a>
                                    <a class="hover:text-primary transition-colors" data-alt="Instagram icon"
                                        href="#">IG</a>
                                </div>
                            </div>
                        </div>
                        <div class="text-center text-xs mt-10 pt-6 border-t border-primary/20 dark:border-primary/30">
                            <p>© 2024 Every Tree for Hope. All Rights Reserved.</p>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
