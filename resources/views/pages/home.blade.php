<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Every Tree for Hope</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#19b357",
                        "background-light": "#f6f8f7",
                        "background-dark": "#112118",
                    },
                    fontFamily: {
                        display: ["Inter"],
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px",
                    },
                },
            },
        };
    </script>
    <style>
        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-200">
    <div class="flex flex-col min-h-screen">
        <header
            class="sticky top-0 z-50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm border-b border-gray-200 dark:border-gray-800">
            <nav class="container mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
                <a class="flex items-center gap-3" href="#">
                    <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 48 48"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z"
                            fill="currentColor"></path>
                    </svg>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Every Tree for Hope</h2>
                </a>
                <div class="hidden md:flex items-center gap-8">
                    <a class="text-sm font-medium hover:text-primary transition-colors" href="#">About</a>
                    <a class="text-sm font-medium hover:text-primary transition-colors" href="#">Events</a>
                    <a class="text-sm font-medium hover:text-primary transition-colors" href="#">Resources</a>
                    <a class="text-sm font-medium hover:text-primary transition-colors" href="#">Contact</a>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        class="px-4 py-2 text-sm font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">Donate</button>
                    <button
                        class="px-4 py-2 text-sm font-bold text-gray-900 dark:text-white bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">Join
                        Us</button>
                </div>
            </nav>
        </header>
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
        <footer class="bg-background-light dark:bg-background-dark border-t border-gray-200 dark:border-gray-800">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                    <div class="flex flex-wrap justify-center md:justify-start gap-6">
                        <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary transition-colors"
                            href="#">About</a>
                        <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary transition-colors"
                            href="#">Events</a>
                        <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary transition-colors"
                            href="#">Resources</a>
                        <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary transition-colors"
                            href="#">Contact</a>
                    </div>
                    <div class="flex justify-center gap-6">
                        <a class="text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors"
                            href="#">
                            <svg aria-hidden="true" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84">
                                </path>
                            </svg>
                        </a>
                        <a class="text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors"
                            href="#">
                            <svg aria-hidden="true" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path clip-rule="evenodd"
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                    fill-rule="evenodd"></path>
                            </svg>
                        </a>
                        <a class="text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors"
                            href="#">
                            <svg aria-hidden="true" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path clip-rule="evenodd"
                                    d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.024.06 1.378.06 3.808s-.012 2.784-.06 3.808c-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.024.048-1.378.06-3.808.06s-2.784-.013-3.808-.06c-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.048-1.024-.06-1.378-.06-3.808s.012-2.784.06-3.808c.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 016.08 2.525c.636-.247 1.363-.416 2.427-.465C9.53 2.013 9.884 2 12.315 2zM12 7a5 5 0 100 10 5 5 0 000-10zm0 8a3 3 0 110-6 3 3 0 010 6zm5.25-9.75a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5z"
                                    fill-rule="evenodd"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="mt-8 border-t border-gray-200 dark:border-gray-800 pt-8 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">© 2024 Every Tree for Hope. All rights
                        reserved.</p>
                </div>
            </div>
        </footer>
    </div>

</body>

</html>
