@extends('layouts.layout')
@section('title', 'Home Page')
@section('content')
    <main class="flex-grow">
        <section class="relative bg-white py-32 md:py-48 px-6 overflow-hidden" id="mission-vision">
            <span
                class="material-symbols-outlined absolute top-20 left-[10%] text-primary/20 text-5xl animate-pulse">eco</span>
            <span
                class="material-symbols-outlined absolute top-1/2 right-[5%] text-vibrant-lime/20 text-4xl animate-pulse">spa</span>
            <span
                class="material-symbols-outlined absolute bottom-20 left-[15%] text-primary/10 text-6xl animate-pulse">psychology_alt</span>
            <div class="max-w-7xl mx-auto relative">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    <div class="lg:col-span-7 relative min-h-[500px] md:min-h-[700px] flex items-center">
                        <div
                            class="absolute top-0 right-0 w-[85%] h-[400px] md:h-[550px] rounded-3xl overflow-hidden shadow-2xl group transition-transform duration-1000 hover:scale-[1.02]">
                            <img alt="Wide forest vision" class="w-full h-full object-cover"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuD5PZeVaTDepGXwxR10OE6Feq5BCgdn9QWsVmA4IbXGKfKSs73Z3RJmcJr94tWX6qn3cwUdQkyUzzyb1rcDVCCH8xmAwSGxDZm1r3_nO-wCov2_Zkigwdde52aed5g0M6LOMOF_6QRpACjAY2NxqbwfbGtUy1D4R7vCjhXxFrvsbaSJgSVhB7z3LRSnxWBUdv5fpb6X7rSLf-Uy-cjJiEH_2VbWXYagPry5eiNyF7wx9DeU9PZTGBenuYElX7QHOixLCBDOHMz49PM" />
                            <div class="absolute inset-0 bg-deep-green/10"></div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 w-[75%] h-[350px] md:h-[450px] rounded-3xl overflow-hidden shadow-2xl border-[8px] border-white group transition-transform duration-1000 hover:scale-[1.05] z-10">
                            <img alt="Sprout mission" class="w-full h-full object-cover"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuD5PZeVaTDepGXwxR10OE6Feq5BCgdn9QWsVmA4IbXGKfKSs73Z3RJmcJr94tWX6qn3cwUdQkyUzzyb1rcDVCCH8xmAwSGxDZm1r3_nO-wCov2_Zkigwdde52aed5g0M6LOMOF_6QRpACjAY2NxqbwfbGtUy1D4R7vCjhXxFrvsbaSJgSVhB7z3LRSnxWBUdv5fpb6X7rSLf-Uy-cjJiEH_2VbWXYagPry5eiNyF7wx9DeU9PZTGBenuYElX7QHOixLCBDOHMz49PM" />
                            <div class="absolute inset-0 bg-primary/10"></div>
                            <div
                                class="absolute top-0 left-0 w-full h-full flex flex-col justify-end p-8 bg-gradient-to-t from-white/90 via-white/60 to-transparent">
                                <div class="flex items-center gap-4 mb-3">
                                    <span class="material-symbols-outlined text-primary">favorite</span>
                                    <h3 class="font-serif text-3xl font-bold text-deep-green">Mission</h3>
                                </div>
                                <p class="text-charcoal/80 font-light leading-relaxed">
                                    Cultivating a greener tomorrow by empowering global communities to restore
                                    biodiversity through science.
                                </p>
                            </div>
                        </div>
                        <div
                            class="absolute -bottom-6 -left-6 w-32 h-32 border-l-2 border-b-2 border-vibrant-lime/40 rounded-bl-3xl -z-10">
                        </div>
                    </div>
                    <div class="lg:col-span-5 space-y-12 lg:pl-12">
                        <div class="space-y-4">
                            <h4
                                class="text-primary font-bold tracking-[0.3em] text-xs uppercase">
                                Our Core Purpose
                            </h4>
                            <div class="relative">
                                <h2
                                    class="text-5xl md:text-7xl font-serif font-light text-deep-green leading-tight">
                                    Growing <br />
                                    <span class="font-bold italic">Intentionally.</span>
                                </h2>
                                <div class="w-24 h-[1px] bg-vibrant-lime mt-8"></div>
                            </div>
                        </div>
                        <div class="space-y-10">
                            <div class="group">
                                <div class="flex items-center gap-6 mb-4">
                                    <span
                                        class="text-vibrant-lime/40 text-4xl font-serif italic group-hover:text-vibrant-lime transition-colors">01.</span>
                                    <h5 class="text-xl font-bold text-deep-green uppercase tracking-wider">The
                                        Mission</h5>
                                </div>
                                <p class="text-charcoal/60 text-lg font-light leading-relaxed pl-16">
                                    To plant with purpose. We don't just put seeds in the ground; we restore entire
                                    ecosystems by selecting native species that support local fauna and repair
                                    depleted soil.
                                </p>
                            </div>
                            <div class="group">
                                <div class="flex items-center gap-6 mb-4">
                                    <span
                                        class="text-vibrant-lime/40 text-4xl font-serif italic group-hover:text-vibrant-lime transition-colors">02.</span>
                                    <h5 class="text-xl font-bold text-deep-green uppercase tracking-wider">The
                                        Vision</h5>
                                </div>
                                <p class="text-charcoal/60 text-lg font-light leading-relaxed pl-16">
                                    We envision a 2050 where global temperatures have stabilized and
                                    "nature-positive" is the mandatory framework for every city and corporation on
                                    Earth.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-16 sm:py-24 bg-background-light/60 dark:bg-background-dark/60">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="relative overflow-hidden rounded-3xl border border-gray-200/70 dark:border-gray-800 bg-white/80 dark:bg-background-dark/80 backdrop-blur-sm shadow-sm">
                    <div
                        class="pointer-events-none absolute -top-24 -right-24 h-64 w-64 rounded-full bg-primary/15 blur-3xl">
                    </div>
                    <div
                        class="pointer-events-none absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl">
                    </div>

                    <div class="relative p-8 sm:p-12">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                            <div class="max-w-3xl">
                                <p
                                    class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-sm font-semibold text-primary">
                                    Our Mission
                                </p>
                                <h2 class="mt-4 text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white">
                                    Turning barren land into green hope
                                </h2>
                                <p class="mt-4 text-lg text-gray-700 dark:text-gray-300">
                                    We are a grassroots environmental movement committed to planting 6 drought-tolerant
                                    trees every week in the hills surrounding Kabul, Afghanistan. Our mission is simple:
                                </p>
                                <div class="mt-6 rounded-2xl bg-background-light dark:bg-background-dark p-6 border border-gray-200/70 dark:border-gray-800">
                                    <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                                        Turn barren land into green hope—one tree at a time.
                                    </p>
                                </div>
                                <p class="mt-6 text-lg text-gray-700 dark:text-gray-300">
                                    As young civil society members, we believe that planting trees is not just about the
                                    environment—it’s about healing communities, inspiring action, and building a more
                                    livable future for all Afghans.
                                </p>
                            </div>

                            <div class="relative">
                                <div class="absolute -inset-2 rounded-3xl bg-gradient-to-br from-primary/20 via-emerald-500/10 to-transparent blur-2xl"></div>
                                <div class="relative overflow-hidden rounded-3xl border border-gray-200/70 dark:border-gray-800 bg-white/60 dark:bg-background-dark/60 shadow-sm">
                                    <div class="aspect-[4/3]">
                                        <img
                                            alt="Planting drought-tolerant trees in the hills surrounding Kabul"
                                            class="h-full w-full object-cover"
                                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDpCpXne7VxAqOhiNQsRPM10zkTmQFSIg9ltGYZGSm3e9zV9-x3KSbEsczDjc98q1XokZ72XjiIlQacMvvwJeX0KKFYmcNKgMznUPFaVBi6vpY_Om2F7eSoPwOA_WTe3_CgzJju-3jgvczry5MZkpIYEKHbbW6qrx0CzzTFYz1kiXFAhFfBRt_fGCGO9HZrfnBKX8XEbbEWuCopaPX9QoYCpFhQAcZZHMyxOC6d2XPaQAo_lqvUV8_OLEMI9N5NMGw7PuL_NLFbIhA" />
                                    </div>
                                    <div class="p-5 sm:p-6">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Kabul, Afghanistan</p>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Small weekly action that grows into lasting change.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="rounded-2xl bg-white dark:bg-background-dark p-6 border border-gray-200/70 dark:border-gray-800 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v18m9-9H3" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">6 trees every week</h3>
                                </div>
                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Consistent, measurable action—week after week.</p>
                            </div>
                            <div class="rounded-2xl bg-white dark:bg-background-dark p-6 border border-gray-200/70 dark:border-gray-800 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Kabul’s hills</h3>
                                </div>
                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Restoring the land around our communities.</p>
                            </div>
                            <div class="rounded-2xl bg-white dark:bg-background-dark p-6 border border-gray-200/70 dark:border-gray-800 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10V6m0 12v2" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Drought-tolerant</h3>
                                </div>
                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Choosing trees that can thrive with limited water.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-16 sm:py-24 bg-background-light dark:bg-background-dark">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white">Upcoming Events</h2>
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="group">
                        <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
                            <img alt="Community tree planting"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBZNKASIkMfOzuoPISZOPjn9KZR_eTepveiPFUVx3Ftwpb55oG6wYLLn-YmkKpDKv9o26hU9-lQl0DcvZYTG9GFsKGkMJT8RJS0EMJEp2iJ_GkmuV_cWvVZOzxqKABWdKPnD4KgmXtfWhTkD5dwyHc2sq7wR37qcGZIXNQBn1S7QPdQCEXuEOzDXG_1YOBiZxJ_1bHqYowT8iFCKHb9-OExggqXQJSYsEppnzj6ChmIiu2tNhNNsFw6oQ-D6TXv5JPYn27Wb4-CFGo" />
                        </div>
                        <div class="mt-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Community Tree Planting
                                Day</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Join us for a day of planting
                                trees and making our community greener.</p>
                        </div>
                    </div>
                    <div class="group">
                        <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
                            <img alt="Volunteer initiative"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBtcj8iaYNM43rodzlenF3jzToUF02dSM8Ldq1r6wIcZr4AF-ylBxZI4UnKVb9XWxKz3GPTopThKmDgsXoTFeJV-29M1Nn1z6-5NaTmtKxRxFisYh4YmCQV6s4va6_NV_jk5vCfvlKsDFD5-o_b86t8f1ZgCanIC8tSjfIGiljZaSQBmAYcZLPKXn2rQNQqAdhXa__o5bUIAWt6itSI4LWObgTiYtOXQun0IJTH1Rv9IMK0uiA-O3ZTTIL1G9VFDK8dz50l-Hw5o7A" />
                        </div>
                        <div class="mt-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Volunteer Tree Planting
                                Initiative</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Help us plant trees in local
                                parks and neighborhoods.</p>
                        </div>
                    </div>
                    <div class="group">
                        <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
                            <img alt="Tree workshop"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDFQUBIEpdO8-dIilIZI8OWSMWW0T1vqN5B80TWejVvCG-QcaMFjOPM3oQtNUQzhjeOuoenCYo8VQF4pY09Xh3y9AOb7WJbBU5Qe1c0BN6KQK8GeznXHkZvlYNNaJFuYi_ZbIw4bsFL-W_qvx0nBEQ7lP6TF8U1L42TCt4Pv7gWQMrGjVqTnlM-WPmNOeKeWhhmwVM8svyLLPV9Zq3hNca5C3qOx4Xfo2QixUgsBpvikkN4jDhCLSu_ed-GG93EJBOol60ga8kSk5s" />
                        </div>
                        <div class="mt-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tree Planting Workshop
                            </h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Learn how to plant and care for
                                trees in your own backyard.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-16 sm:py-24">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Volunteer With Us</h2>
                    <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600 dark:text-gray-400">Make a difference.
                        Join our team of dedicated volunteers and help us create a greener future.</p>
                    <div class="mt-8">
                        <button
                            class="px-8 py-3 text-base font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">Sign
                            Up Today</button>
                    </div>
                </div>
                <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
                    <div class="group">
                        <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
                            <img alt="People planting trees"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDpCpXne7VxAqOhiNQsRPM10zkTmQFSIg9ltGYZGSm3e9zV9-x3KSbEsczDjc98q1XokZ72XjiIlQacMvvwJeX0KKFYmcNKgMznUPFaVBi6vpY_Om2F7eSoPwOA_WTe3_CgzJju-3jgvczry5MZkpIYEKHbbW6qrx0CzzTFYz1kiXFAhFfBRt_fGCGO9HZrfnBKX8XEbbEWuCopaPX9QoYCpFhQAcZZHMyxOC6d2XPaQAo_lqvUV8_OLEMI9N5NMGw7PuL_NLFbIhA" />
                        </div>
                        <div class="mt-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tree Planting</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Plant trees in local parks and
                                neighborhoods.</p>
                        </div>
                    </div>
                    <div class="group">
                        <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
                            <img alt="Volunteers cleaning a park"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBfWW_et3jY1p_HuDc6Io1-ooeH5g3kr3hGV52DFckvnw9rqa92oB0G7q8zAttaELNsul1HjQVjhG7WUd-JsNR0GObMMhy39gAcgCjCNjBSisJJZ7CPwP2nPq5ZJ8IqtsA8iwOUgnss6MxXKQHRy-xyEusBBWeXZ9H3_vbz7f2hG3tB2PCNtZssti99ZQMVWEEscJ9qRTG0-t3eRv4WV_670i3UgFMN3Jt5-7Nz2snTrDVIfzfKD_3a3s_jFafROph2pfX8hHX-o50" />
                        </div>
                        <div class="mt-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Community Cleanup</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Help us clean up parks and
                                green spaces.</p>
                        </div>
                    </div>
                    <div class="group">
                        <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
                            <img alt="Workshop session"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuA5JWq0nc_3VSY9qKBJY8G78urekqJmymrrTu1uRPDOnOICwCLg1-4Rob56xRXf_H7pg_wKvIbU5YGAz9Mzwms3r04kRNR0uzXPedY-U94Wbh-dJCKdFfPLCI12goB08jr3jmXZBJVeobieTANatH1Cjkvh8veAX-jR0cWL7go7X1Q8x_ckDtH81j2I_ibhD189meDVzcTHXjeHDfj5HkdWHRBgHcrN_a6xmiQh3VdzkONUyc8_LaomcjG6prmcfhVkb3mVsdDU5vU" />
                        </div>
                        <div class="mt-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Educational Workshops
                            </h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Lead or assist in educational
                                workshops.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection


