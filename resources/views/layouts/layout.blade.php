<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Every Tree for Hope</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&amp;family=Playfair+Display:ital,wght@0,700;1,900&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#22c55e",
                        "deep-green": "#064e3b",
                        "vibrant-lime": "#84cc16",
                        "sage-green": "#86a693",
                        "gold-accent": "#d4af37",
                        charcoal: "#374151",
                        "background-light": "#fdfdfd",
                        "background-dark": "#112118",
                    },
                    fontFamily: {
                        sans: ["Manrope", "sans-serif"],
                        serif: ["Playfair Display", "serif"],
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
        body {
            font-family: 'Manrope', sans-serif;
        }

        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
        }

        .hero-white-overlay {
            background: linear-gradient(to right, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.8) 40%, rgba(255, 255, 255, 0.2) 100%);
        }

        @media (max-width: 768px) {
            .hero-white-overlay {
                background: linear-gradient(to bottom, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.7) 100%);
            }
        }

        .glass-panel {
            background-color: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
        }

        .floating {
            animation: float 8s ease-in-out infinite;
        }

        @keyframes float {
            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-15px) rotate(5deg);
            }
        }

        .parallax-leaf {
            pointer-events: none;
            z-index: 1;
        }

        .gold-line-art {
            background: linear-gradient(90deg, transparent, #d4af37 50%, transparent);
            height: 1px;
            width: 100%;
            opacity: 0.3;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-sans text-charcoal dark:text-gray-200 overflow-x-hidden">
    <div class="flex flex-col min-h-screen">
        @if (Route::currentRouteName() === 'home')
            <div class="relative min-h-screen w-full flex flex-col overflow-hidden">
                <div class="absolute inset-0 z-0">
                    <div class="w-full h-full bg-cover bg-center"
                        style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCp83JCfKCRYgHZuZA8dzBJ3Ih5yeidFw9eQGrfMH89CUBjfUnGXPlxXvfb0DmVToP0iwQwPaK0W5XabUIrIKFxbSwRZd0LqiyyCermKdbgrB2uPKlC48Z7W_on-lUkUOnlJd62ok3dkmH7JXdQxf63fiy8lTJ7mxggLO5Vjo_p7uKwbi43Dl3GWm8aiiPqcYvOfRpQgKV513yPQakVJ-1w2d86ue27OPY3p8N9H5sr5Mg5oew18KgTO2Ehhj7cSGj-2a6ht7DOZOs');">
                    </div>
                    <div class="absolute inset-0 hero-white-overlay"></div>
                </div>
                <header class="relative z-20 flex items-center justify-between px-6 py-8 md:px-12 lg:px-24">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group cursor-pointer">
                        <div class="bg-deep-green p-1.5 rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-2xl font-bold">nature</span>
                        </div>
                        <h2 class="text-deep-green text-xl font-extrabold tracking-tight">EverGreen</h2>
                    </a>
                    <nav class="hidden md:flex items-center gap-10">
                        <a class="text-charcoal hover:text-primary transition-colors text-sm font-bold tracking-wide"
                            href="{{ route('about') }}">Impact</a>
                        <a class="text-charcoal hover:text-primary transition-colors text-sm font-bold tracking-wide"
                            href="{{ route('works') }}">Projects</a>
                        <a class="text-charcoal hover:text-primary transition-colors text-sm font-bold tracking-wide"
                            href="{{ route('gallery') }}">Our Mission</a>
                        <a class="text-charcoal hover:text-primary transition-colors text-sm font-bold tracking-wide"
                            href="{{ route('report') }}">Transparency</a>
                    </nav>
                    <div class="flex items-center gap-4">
                        <button
                            class="hidden sm:flex items-center justify-center px-5 py-2.5 rounded-lg bg-transparent hover:bg-charcoal/5 text-charcoal border border-charcoal/20 transition-all text-sm font-bold">
                            Join Us
                        </button>
                        <button
                            class="flex items-center justify-center px-6 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white transition-all text-sm font-extrabold shadow-lg shadow-primary/20">
                            Donate
                        </button>
                    </div>
                </header>

                <main
                    class="relative z-10 flex flex-1 flex-col justify-center px-6 md:px-12 lg:px-24 pb-20">
                    <div class="max-w-3xl space-y-8">
                        <div
                            class="inline-flex items-center gap-2 bg-deep-green/5 border border-deep-green/10 px-4 py-2 rounded-full">
                            <span class="flex h-2 w-2 rounded-full bg-primary animate-pulse"></span>
                            <p
                                class="text-deep-green text-[10px] font-bold uppercase tracking-[0.2em]">
                                Global Reforestation Mission
                            </p>
                        </div>
                        <h1
                            class="text-deep-green text-6xl md:text-7xl lg:text-8xl font-serif font-bold leading-[1.05] tracking-tight">
                            Rooting for <br />
                            <span class="text-vibrant-lime italic font-black">the Future</span>
                        </h1>
                        <p
                            class="text-charcoal/80 text-lg md:text-xl max-w-xl font-medium leading-relaxed">
                            Join our global community in restoring the world's forests one seedling at a time.
                            Transparent, tracked, and impactful climate action starting today.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <button
                                class="flex min-w-[220px] h-14 items-center justify-center rounded-lg bg-primary hover:bg-primary/90 text-white text-lg font-extrabold transition-all shadow-xl shadow-primary/20 group">
                                Plant a Tree Today
                                <span
                                    class="material-symbols-outlined ml-2 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </button>
                            <button
                                class="flex min-w-[220px] h-14 items-center justify-center rounded-lg border border-charcoal/30 hover:bg-charcoal/5 text-charcoal text-lg font-bold transition-all">
                                View Our Projects
                            </button>
                        </div>
                    </div>
                </main>

                <div
                    class="relative z-10 w-full px-6 md:px-12 lg:px-24 pb-12 flex flex-col md:flex-row items-end justify-between gap-8">
                    <div class="grid grid-cols-2 md:flex gap-12 md:gap-20">
                        <div class="flex flex-col">
                            <p class="text-deep-green text-4xl font-extrabold tabular-nums">1,240,500</p>
                            <p
                                class="text-charcoal/50 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">
                                Trees Planted
                            </p>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-deep-green text-4xl font-extrabold tabular-nums">45,000</p>
                            <p
                                class="text-charcoal/50 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">
                                Tons CO2 Offset
                            </p>
                        </div>
                    </div>
                    <div
                        class="hidden md:flex flex-col items-center gap-3 absolute left-1/2 -translate-x-1/2 bottom-12">
                        <p
                            class="text-charcoal/30 text-[10px] font-bold uppercase tracking-[0.3em]">
                            Explore
                        </p>
                        <div class="w-[1px] h-14 bg-gradient-to-b from-deep-green/30 to-transparent">
                        </div>
                    </div>
                    <div class="hidden lg:flex flex-col items-end">
                        <p
                            class="text-charcoal/40 text-[10px] font-bold mb-5 uppercase tracking-[0.15em]">
                            Trusted by Environmental Leaders
                        </p>
                        <div class="flex gap-8 opacity-40 grayscale contrast-125">
                            <span class="material-symbols-outlined text-deep-green text-3xl">eco</span>
                            <span class="material-symbols-outlined text-deep-green text-3xl">public</span>
                            <span class="material-symbols-outlined text-deep-green text-3xl">water_drop</span>
                        </div>
                    </div>
                </div>
            </div>

            @yield('content')
            @stack('scripts')
        @else
            <header
                class="sticky top-0 z-50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm border-b border-gray-200 dark:border-gray-800">
                <nav class="container mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
                    <a class="flex items-center gap-3" href="{{ route('home') }}">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 48 48"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z"
                                fill="currentColor"></path>
                        </svg>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Every Tree for Hope</h2>
                    </a>
                    <div class="hidden md:flex items-center gap-8">
                        <a class="text-sm font-medium hover:text-primary transition-colors"
                            href="{{ route('about') }}">About</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors"
                            href="{{ route('works') }}">Our
                            Works</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors"
                            href="{{ route('gallery') }}">Gallery</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors"
                            href="{{ route('donators') }}">Donators</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors"
                            href="{{ route('report') }}">Report</a>
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

            @yield('content')
            @stack('scripts')
        @endif

        <footer class="bg-background-light dark:bg-background-dark border-t border-gray-200 dark:border-gray-800">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                    <div class="flex flex-wrap justify-center md:justify-start gap-6">
                        <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary transition-colors"
                            href="{{ route('about') }}">About</a>
                        <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary transition-colors"
                            href="{{ route('works') }}">Our Works</a>
                        <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary transition-colors"
                            href="{{ route('gallery') }}">Gallery</a>
                        <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary transition-colors"
                            href="{{ route('donators') }}">Donators</a>
                        <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary transition-colors"
                            href="{{ route('report') }}">Report</a>
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
