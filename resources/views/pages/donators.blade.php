@extends('layouts.layout')
@section('title', 'Donators Page')
@section('content')
    <main class="px-4 sm:px-6 md:px-10 lg:px-20 xl:px-40 flex flex-1 justify-center py-5 sm:py-10">
        <div class="layout-content-container flex flex-col max-w-5xl flex-1 gap-8">
            <!-- HeroSection Component -->
            <section class="@container">
                <div class="@[480px]:p-0">
                    <div class="flex min-h-[400px] sm:min-h-[480px] flex-col gap-6 bg-cover bg-center bg-no-repeat @[480px]:gap-8 @[480px]:rounded-xl items-center justify-center p-4 text-center"
                        data-alt="Lush green forest canopy seen from above"
                        style='background-image: linear-gradient(rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.5) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuDxyqmV5h-u5XpT3vfqXh8OmXdXoHk-zVkxk9f6rJ-V6tRlg8ltF-x6IFvhu8sWpStRLqm97Uw-heYmNWEyyVyBIiantKORJKBeMfD_QRtTLNFGCw2USlTuqyoWW_581KIbs-xLS40IHYFZffl8W5m52pRvVG1rd5WDryOdAtyfPiFvXU7NsJPEOkirMEpsr88A0nRKmdSHxuwpE-f9zsYtS-dpTJmkuVk_4KE9yeaKj4TDjjl50hUlm8Stf0KuHfEFjCcFzT0QrQE");'>
                        <div class="flex flex-col gap-2">
                            <h1
                                class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em]">
                                A Forest of Gratitude</h1>
                            <h2
                                class="text-white text-sm font-normal leading-normal @[480px]:text-base @[480px]:font-normal @[480px]:leading-normal max-w-xl mx-auto">
                                Thank you to our supporters. Your contributions are helping us build a greener
                                planet for everyone, one tree at a time.</h2>
                        </div>
                        <button
                            class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 @[480px]:h-12 @[480px]:px-5 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] @[480px]:text-base @[480px]:font-bold @[480px]:leading-normal @[480px]:tracking-[0.015em] hover:bg-primary/90 transition-colors">
                            <span class="truncate">Become a Donor</span>
                        </button>
                    </div>
                </div>
            </section>
            <!-- Chips (Filters) Component -->
            <section class="flex flex-col sm:flex-row gap-3 p-3 flex-wrap items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-400 pr-2">Filter by:</span>
                <div class="flex gap-2 flex-wrap">
                    <button
                        class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-primary/20 dark:bg-primary/30 pl-4 pr-2 text-primary dark:text-white">
                        <p class="text-sm font-medium leading-normal">All Tiers</p>
                        <span class="material-symbols-outlined text-xl">expand_more</span>
                    </button>
                    <button
                        class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 pl-4 pr-2 transition-colors">
                        <p class="text-gray-800 dark:text-gray-300 text-sm font-medium leading-normal">Seedling
                        </p>
                    </button>
                    <button
                        class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 pl-4 pr-2 transition-colors">
                        <p class="text-gray-800 dark:text-gray-300 text-sm font-medium leading-normal">Sapling
                        </p>
                    </button>
                    <button
                        class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 pl-4 pr-2 transition-colors">
                        <p class="text-gray-800 dark:text-gray-300 text-sm font-medium leading-normal">Oak</p>
                    </button>
                </div>
                <div class="flex-grow"></div>
                <button
                    class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 pl-4 pr-2 transition-colors">
                    <p class="text-gray-800 dark:text-gray-300 text-sm font-medium leading-normal">Sort by: Most
                        Recent</p>
                    <span class="material-symbols-outlined text-xl text-gray-600 dark:text-gray-400">expand_more</span>
                </button>
            </section>
            <!-- ImageGrid (Donor Grid) Component -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                <div
                    class="flex flex-col gap-3 text-center p-4 border rounded-xl border-gray-200/80 dark:border-gray-700/80 bg-white dark:bg-background-dark">
                    <div class="px-4">
                        <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-full"
                            data-alt="Abstract portrait of Jane Doe"
                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBGH2sC1WZPNU5oX_ffHtA_BOuZYn0S2BgHUjn2nuEbPAbtb5weqHu9mbr5iSOQdXkJ_toAJN1MmtgjiuZEMzLIWBgpAStECjjXdUlICGhkjWG-UdPHfNKFdQW5t_OE4UTwSjXRj_NsnXZ8HGjNImyaAk9wQ54GpOOu79rmy-MQ9u-mlNZs8lxXIXtynVz176BPK53YK_vomhfYDpVzNGaQviO3SeKZi9gRs6pL6DjOLfNpPqNWSwuU5s9oyBFuFGBAugiUH6t93No");'>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-900 dark:text-white text-base font-medium leading-normal">Jane Doe
                        </p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-normal leading-normal italic">
                            "For a brighter future."</p>
                        <p class="text-primary text-sm font-semibold leading-normal mt-1">Oak Supporter</p>
                    </div>
                </div>
                <div
                    class="flex flex-col gap-3 text-center p-4 border rounded-xl border-gray-200/80 dark:border-gray-700/80 bg-white dark:bg-background-dark">
                    <div class="px-4">
                        <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center"
                            data-alt="Anonymous supporter icon">
                            <span class="material-symbols-outlined text-5xl text-gray-400 dark:text-gray-500">park</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-900 dark:text-white text-base font-medium leading-normal">Anonymous
                            Supporter</p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-normal leading-normal">Joined
                            October 2023</p>
                        <p class="text-primary/80 text-sm font-semibold leading-normal mt-1">Sapling Friend</p>
                    </div>
                </div>
                <div
                    class="flex flex-col gap-3 text-center p-4 border rounded-xl border-gray-200/80 dark:border-gray-700/80 bg-white dark:bg-background-dark">
                    <div class="px-4">
                        <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-full"
                            data-alt="Abstract portrait of Michael Smith"
                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBbVqjYLqX1wnBsJjugamLC5ho4qyXk5v_gsTCcrXxD2cK0br61ngaTGbD_-6zy8W-qU10pL-9j14WmE5SxA8vgMHd8kP0Ph73IjN_kM1soFiVPgwcvO8ltVwWpq6-yAW-_q61DHXTtR6j1jBJM-keUFFHSyELh6CLS4AqT8T8whjNHh4dtbRwPzxfCegDlWsmn9m0RSPix9eqTLq_oK1K9lkeoSdVLVYBScSxPkeiyLSBdLlww02FqsdyhAK6Jq1yZCYxsb1cgWSc");'>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-900 dark:text-white text-base font-medium leading-normal">Michael
                            Smith</p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-normal leading-normal italic">
                            "In memory of my grandfather."</p>
                        <p class="text-primary text-sm font-semibold leading-normal mt-1">Oak Supporter</p>
                    </div>
                </div>
                <div
                    class="flex flex-col gap-3 text-center p-4 border rounded-xl border-gray-200/80 dark:border-gray-700/80 bg-white dark:bg-background-dark">
                    <div class="px-4">
                        <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-full"
                            data-alt="Abstract portrait of Emily Johnson"
                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBMJ3AjLjKjpHba8HmfxeHXR1OxDpBS174jjsOLeAP1Hujfxyk4X3yBwZjUBfJ6e2P0ULK5HIXEsUYu9YGaaqT3hsAFvOV9BH9WOKqvrL1N3iOHwF1okBzoTgcE4mBjGBWJkBEnFtF0HDGQUF9iVphNndXFWJOHA-zZCmoGoew2xMIFsBmAxe8XP285dcJGRS-XCtA14nKEteqpb5q-oSJV1Us5XNa79yH805SE66BlURZ0aMZwUELiD0c_rA1OdO58u62Yb0wGh_8");'>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-900 dark:text-white text-base font-medium leading-normal">Emily
                            Johnson</p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-normal leading-normal">Joined
                            October 2023</p>
                        <p class="text-primary/70 text-sm font-semibold leading-normal mt-1">Seedling
                            Contributor</p>
                    </div>
                </div>
                <div
                    class="flex flex-col gap-3 text-center p-4 border rounded-xl border-gray-200/80 dark:border-gray-700/80 bg-white dark:bg-background-dark">
                    <div class="px-4">
                        <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-full"
                            data-alt="Abstract portrait of David Chen"
                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuB98pW0dg5vJHXsyZRk-11lQDnP7gAa0AVjsf1fsSwNmiK5ic2QE1bAs_6td5urq8UVDV8U4gIwjG4lxgC9o3uG-6qPsjHAqEpA9Dd9f3JWZuxI69J0Tj-IBlVO220OEBPGHaPqFzNvLdEMvvf1cQuYnXwHviGbBR37U7yDUZn4UibIikodgEjyYGqSgD4mZUOKXO46qM3wqDMPc7ZsBY-BFeKby0tJt73g9FRyCSNZsoDgX4Xrly6Wlnz9Ej6Hn8g4X8IbcPT0SAs");'>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-900 dark:text-white text-base font-medium leading-normal">David
                            Chen</p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-normal leading-normal italic">
                            "Let's grow together!"</p>
                        <p class="text-primary/80 text-sm font-semibold leading-normal mt-1">Sapling Friend</p>
                    </div>
                </div>
                <div
                    class="flex flex-col gap-3 text-center p-4 border rounded-xl border-gray-200/80 dark:border-gray-700/80 bg-white dark:bg-background-dark">
                    <div class="px-4">
                        <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-full"
                            data-alt="Abstract portrait of Maria Garcia"
                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCLHte8FiMz1XzGqSTKciDhfYRnEdZqeNqI3pkAo6G7Rl0xzeoAllb9w-pQl1UiVeKrSZ7JwClKxpENKRJDRayvx2zJMkFVsr3_dfjdUXADfmFVeWWMrJNasyQNpZhU-3x87uKHTNUfc-o9z85qrasOW0d-KDCqdKNjaJiz64e0aQrnbVbe4l354UCnlI_XIYWrhAEZk6d-u1Q8lLofeS1BTa5oBJQhr77YeWODo_T-0X5JUwRYQrIXWWrkC57Lk8BQdUP3FVlyUb8");'>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-900 dark:text-white text-base font-medium leading-normal">Maria
                            Garcia</p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-normal leading-normal">Joined
                            September 2023</p>
                        <p class="text-primary/70 text-sm font-semibold leading-normal mt-1">Seedling
                            Contributor</p>
                    </div>
                </div>
                <div
                    class="flex flex-col gap-3 text-center p-4 border rounded-xl border-gray-200/80 dark:border-gray-700/80 bg-white dark:bg-background-dark">
                    <div class="px-4">
                        <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-full"
                            data-alt="Abstract portrait of Chris Lee"
                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBzU-GEI-Y5_UOp80bFmc3BSEcyQOY4J_S3qLO2JN64LHbAOjTmLS9GR9iIZBLT_Hi4ajlLUMYMwCZb_NcoacxI8aLrTmqch-MIIEVsJ6HqWmxcRehH4W_WVpSIIurwDFKpoHZQyLLXF-xa3s0vvraPm8uVyQcUJIMUw8uaEz0EoJPO1Fdl7P_Hmwz5AkmP7wYpbqFUjOEb6-k1VLMOV3zWhlHQPgcewbKDxSoKPAYRTQAMt8TCh4d9Pdjt73-lRc39rMsrO0mzIcE");'>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-900 dark:text-white text-base font-medium leading-normal">Chris Lee
                        </p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-normal leading-normal">Joined
                            September 2023</p>
                        <p class="text-primary/70 text-sm font-semibold leading-normal mt-1">Seedling
                            Contributor</p>
                    </div>
                </div>
                <div
                    class="flex flex-col gap-3 text-center p-4 border rounded-xl border-gray-200/80 dark:border-gray-700/80 bg-white dark:bg-background-dark">
                    <div class="px-4">
                        <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-full"
                            data-alt="Abstract portrait of Olivia Taylor"
                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCvXRNLZ2zLEyHcMCV7LFtbwrxGB-NpRYq1iBxa7DFbxmwtevUmGdtpgGELDpau_-nj4ah0bf9LR1yZLdbg4U9_71xLm7G6KOGVpfEsHERCcf49NYV8MbHms_th3yoj1r0-pkm8Hq2jHvV14rE-rBd1tx5nwBULjl6LyVygeNrCd1Bwve5V8AYv5Yxs-uaw4ecYRFStWv_2_KpJCyz6bS7zh5AhE3OA0TgtllU1rExqltcDWdOwevBdlp-_UDQvICjljEQjj8fYRgM");'>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-900 dark:text-white text-base font-medium leading-normal">Olivia
                            Taylor</p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-normal leading-normal italic">
                            "For the next generation."</p>
                        <p class="text-primary/80 text-sm font-semibold leading-normal mt-1">Sapling Friend</p>
                    </div>
                </div>
            </section>
            <!-- Pagination Component -->
            <nav aria-label="Pagination" class="flex items-center justify-center p-4">
                <a class="flex size-10 items-center justify-center rounded-lg hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors"
                    href="#">
                    <span class="material-symbols-outlined text-gray-700 dark:text-gray-400">chevron_left</span>
                    <span class="sr-only">Previous</span>
                </a>
                <a aria-current="page"
                    class="text-sm font-bold leading-normal tracking-[0.015em] flex size-10 items-center justify-center text-white bg-primary rounded-lg mx-1"
                    href="#">1</a>
                <a class="text-sm font-normal leading-normal flex size-10 items-center justify-center text-gray-800 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-800 rounded-lg mx-1 transition-colors"
                    href="#">2</a>
                <a class="text-sm font-normal leading-normal flex size-10 items-center justify-center text-gray-800 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-800 rounded-lg mx-1 transition-colors"
                    href="#">3</a>
                <span
                    class="text-sm font-normal leading-normal flex size-10 items-center justify-center text-gray-500 dark:text-gray-400 rounded-lg mx-1">...</span>
                <a class="text-sm font-normal leading-normal flex size-10 items-center justify-center text-gray-800 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-800 rounded-lg mx-1 transition-colors"
                    href="#">10</a>
                <a class="flex size-10 items-center justify-center rounded-lg hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors"
                    href="#">
                    <span class="material-symbols-outlined text-gray-700 dark:text-gray-400">chevron_right</span>
                    <span class="sr-only">Next</span>
                </a>
            </nav>
            <!-- Summary & CTA Section -->
            <section
                class="text-center bg-white dark:bg-background-dark border border-gray-200/80 dark:border-gray-700/80 rounded-xl p-8 sm:p-12 my-10">
                <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Ready to Make a
                    Difference?</h3>
                <p class="mt-2 text-base text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Together, our
                    donors have already planted over <span class="font-bold text-primary">12,500</span> trees!
                    Join our growing community and help us create a greener, healthier planet for all.</p>
                <button
                    class="mt-6 flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] mx-auto hover:bg-primary/90 transition-colors">
                    <span class="truncate">Join Our Cause</span>
                </button>
            </section>
        </div>
    </main>
@endsection
