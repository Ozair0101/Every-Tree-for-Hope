<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Every Tree for Hope</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&amp;family=Playfair+Display:ital,wght@0,700;1,900&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
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

        .botanical-mask {
            mask-image: linear-gradient(to bottom, black, transparent);
            -webkit-mask-image: linear-gradient(to bottom, black, transparent);
        }

        .leaf-float {
            pointer-events: none;
            position: absolute;
            opacity: 0.15;
        }

        .input-premium {
            @apply w-full bg-white/40 border-b border-charcoal/10 px-0 py-4 focus:border-deep-green focus:ring-0 transition-colors text-charcoal font-serif placeholder:text-charcoal/30 placeholder:font-sans placeholder:text-sm;
        }

        .hill-organic-mask {
            clip-path: polygon(0% 15%, 15% 5%, 35% 20%, 55% 5%, 75% 25%, 100% 10%, 100% 100%, 0% 100%);
            border-radius: 40% 60% 70% 30% / 40% 40% 60% 60%;
        }

        .topographical-bg {
            background-image: url("data:image/svg+xml,%3Csvg width='800' height='800' viewBox='0 0 800 800' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 100 C 150 150, 300 50, 450 100 S 750 150, 800 100' stroke='%23374151' fill='transparent' stroke-opacity='0.05'/%3E%3Cpath d='M0 200 C 200 250, 400 150, 600 200 S 800 250, 800 200' stroke='%23374151' fill='transparent' stroke-opacity='0.03'/%3E%3Cpath d='M0 300 C 100 350, 300 250, 500 300 S 700 350, 800 300' stroke='%23374151' fill='transparent' stroke-opacity='0.04'/%3E%3Cpath d='M0 400 C 250 450, 500 350, 800 400' stroke='%23374151' fill='transparent' stroke-opacity='0.02'/%3E%3Cpath d='M0 500 C 150 550, 350 450, 550 500 S 750 550, 800 500' stroke='%23374151' fill='transparent' stroke-opacity='0.05'/%3E%3C/svg%3E");
            background-size: cover;
        }

        .off-white {
            background-color: #f9fafb;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body
    class="bg-background-light dark:bg-background-dark font-sans text-charcoal dark:text-gray-200 overflow-x-hidden {{ Route::currentRouteName() === 'contact' ? 'topographical-bg' : '' }}">
    <div class="flex flex-col min-h-screen">
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

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-8">
                    <a class="text-sm font-medium hover:text-primary transition-colors"
                        href="{{ route('about') }}">About</a>
                    <a class="text-sm font-medium hover:text-primary transition-colors"
                        href="{{ route('gallery') }}">Our Works</a>
                    <a class="text-sm font-medium hover:text-primary transition-colors"
                        href="{{ route('donators') }}">Donators</a>
                    <a class="text-sm font-medium hover:text-primary transition-colors"
                        href="{{ route('report') }}">Report</a>
                    <a class="text-sm font-medium hover:text-primary transition-colors"
                        href="{{ route('contact') }}">Contact</a>
                </div>

                <!-- Desktop Action Buttons -->
                <div class="hidden md:flex items-center gap-2">
                    <button
                        class="px-4 py-2 text-sm font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">Donate</button>
                    <button
                        class="px-4 py-2 text-sm font-bold text-gray-900 dark:text-white bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">Join
                        Us</button>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button"
                    class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="h-6 w-6 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </nav>

            <!-- Mobile Menu -->
            <div id="mobile-menu"
                class="hidden md:hidden bg-background-light/95 dark:bg-background-dark/95 backdrop-blur-sm border-t border-gray-200 dark:border-gray-800">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 space-y-3">
                    <a class="block text-sm font-medium hover:text-primary transition-colors py-2"
                        href="{{ route('about') }}">About</a>
                    <a class="block text-sm font-medium hover:text-primary transition-colors py-2"
                        href="{{ route('gallery') }}">Our
                        Works</a>
                    <a class="block text-sm font-medium hover:text-primary transition-colors py-2"
                        href="{{ route('donators') }}">Donators</a>
                    <a class="block text-sm font-medium hover:text-primary transition-colors py-2"
                        href="{{ route('report') }}">Report</a>
                    <a class="block text-sm font-medium hover:text-primary transition-colors py-2"
                        href="{{ route('contact') }}">Contact</a>
                    <div class="flex flex-col gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button
                            class="w-full px-4 py-2 text-sm font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">Donate</button>
                        <button
                            class="w-full px-4 py-2 text-sm font-bold text-gray-900 dark:text-white bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">Join
                            Us</button>
                    </div>
                </div>
            </div>
        </header>

        @yield('content')
        @stack('scripts')

        <footer class="relative z-10 bg-deep-green border-t border-deep-green/80 pt-20 pb-12 px-6 md:px-12 lg:px-24">
            <div class="absolute right-0 bottom-0 opacity-10 select-none pointer-events-none">
                <span class="material-symbols-outlined text-[20rem] text-white/20 leading-none">potted_plant</span>
            </div>
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-16 mb-20 relative z-10">
                <div class="space-y-6">
                    <div class="flex items-center gap-2">
                        <div class="bg-white/20 p-1.5 rounded-lg">
                            <span class="material-symbols-outlined text-white text-xl">nature</span>
                        </div>
                        <h3 class="text-white text-xl font-extrabold tracking-tight">EverGreen</h3>
                    </div>
                    <p class="text-white/80 leading-relaxed max-w-sm">
                        Dedicated to restoring Kabul's natural heritage, one tree at a time. We bridge the gap between
                        global support and local ecological restoration in Afghanistan.
                    </p>
                </div>
                <div class="space-y-6">
                    <h4 class="font-serif text-lg font-bold text-white">Contact Us</h4>
                    <ul class="space-y-4 text-sm text-white/80">
                        <li class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-gold-accent text-lg">mail</span>
                            <a class="hover:text-gold-accent transition-colors"
                                href="mailto:tamimalim209@gmail.com">tamimalim209@gmail.com</a>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-gold-accent text-lg">chat</span>
                            <a class="hover:text-gold-accent transition-colors" href="https://wa.me/93749290591">+93
                                749
                                290 591</a>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-gold-accent text-lg">location_on</span>
                            <span class="text-white/80">Kabul, Afghanistan<br /><span
                                    class="text-[10px] font-bold uppercase tracking-wider text-white/60">Visits by
                                    appointment only</span></span>
                        </li>
                    </ul>
                </div>
                <div class="space-y-6">
                    <h4 class="font-serif text-lg font-bold text-white">Quick Links</h4>
                    <ul class="space-y-3 text-sm font-bold uppercase tracking-widest text-white/80">
                        <li><a class="hover:text-gold-accent transition-colors" href="#">Impact</a></li>
                        <li><a class="hover:text-gold-accent transition-colors" href="#">Projects</a></li>
                        <li><a class="hover:text-gold-accent transition-colors text-gold-accent"
                                href="#">Donation</a>
                        </li>
                    </ul>
                    <div class="flex items-center gap-8">
                        <a class="text-white/60 hover:text-gold-accent transition-colors" href="#">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z">
                                </path>
                            </svg>
                        </a>
                        <a class="text-white/60 hover:text-gold-accent transition-colors" href="#">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z">
                                </path>
                            </svg>
                        </a>
                        <a class="text-white/60 hover:text-gold-accent transition-colors" href="#">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <div
                class="max-w-7xl mx-auto pt-8 border-t border-white/10 flex flex-col md:row justify-between items-center gap-8 relative z-10">
                <p class="text-xs font-medium text-white/60 uppercase tracking-widest">
                    © 2024 Kabul Reforestation Initiative. All rights reserved.
                </p>
            </div>
        </footer>
    </div>

    <!-- Mobile Menu JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');

                    // Toggle hamburger icon
                    const icon = mobileMenuButton.querySelector('svg');
                    if (mobileMenu.classList.contains('hidden')) {
                        // Show hamburger icon
                        icon.innerHTML =
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                    } else {
                        // Show close icon
                        icon.innerHTML =
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
                    }
                });

                // Close menu when clicking on links
                const mobileLinks = mobileMenu.querySelectorAll('a');
                mobileLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        mobileMenu.classList.add('hidden');
                        // Reset to hamburger icon
                        const icon = mobileMenuButton.querySelector('svg');
                        icon.innerHTML =
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                    });
                });

                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                        mobileMenu.classList.add('hidden');
                        // Reset to hamburger icon
                        const icon = mobileMenuButton.querySelector('svg');
                        icon.innerHTML =
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                    }
                });
            }
        });
    </script>

</body>

</html>
