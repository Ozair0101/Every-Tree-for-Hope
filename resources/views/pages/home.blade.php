@extends('layouts.layout')
@section('title', 'Home Page')
@section('content')
    <main class="flex-grow">
        <section class="relative min-h-[60vh] flex items-center justify-center text-center bg-cover bg-center"
            style='background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.5)), url("https://lh3.googleusercontent.com/aida-public/AB6AXuBPiqQawcuU36f49wZzGQZRZmIWZKUfAam2zCgsnj-mhDdeZH4q3oL2CdoLfKUAJd3F_xk1nLzKrWzTm5jN2G_-bH4hD_1B5aPjjUBfI_kDth_bsbDkYlFDCkIO0U7FZ5u27Ly7Wj0vCHV2H0UopeQzaymxX0OcSxX5f-vmUZPTTPdwd2P4uGHItDHDqp076RLIyejK77kmdoHiO2EEZv8JuLkmArw2mQRySYzt0f-20GTKP-ecyqfLe71oZXl0sZ5E8hjs9z6p-Dk");'>
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-4xl md:text-6xl font-black text-white text-shadow tracking-tight">Plant a Tree, Grow
                    Hope</h1>
                <p class="mt-4 text-lg text-white/90 text-shadow max-w-2xl mx-auto">Join us in our mission to plant
                    trees and create greener, healthier communities for everyone.</p>
                <div class="mt-8 flex flex-wrap justify-center gap-4">
                    <button
                        class="px-6 py-3 text-base font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">Join
                        Us</button>
                    <button
                        class="px-6 py-3 text-base font-bold text-gray-900 bg-white/90 dark:bg-background-light/90 rounded-lg hover:bg-white dark:hover:bg-background-light transition-colors">Donate</button>
                </div>
            </div>
        </section>
        <section class="py-16 sm:py-24">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 text-center">
                    <div class="bg-white dark:bg-background-dark p-8 rounded-xl shadow-sm">
                        <p class="text-4xl font-bold text-primary">15,000+</p>
                        <p class="mt-2 text-base font-medium text-gray-600 dark:text-gray-400">Trees Planted</p>
                    </div>
                    <div class="bg-white dark:bg-background-dark p-8 rounded-xl shadow-sm">
                        <p class="text-4xl font-bold text-primary">5,000+</p>
                        <p class="mt-2 text-base font-medium text-gray-600 dark:text-gray-400">Volunteers</p>
                    </div>
                    <div class="bg-white dark:bg-background-dark p-8 rounded-xl shadow-sm">
                        <p class="text-4xl font-bold text-primary">100+</p>
                        <p class="mt-2 text-base font-medium text-gray-600 dark:text-gray-400">Events Hosted</p>
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
