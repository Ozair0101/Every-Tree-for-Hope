@extends('layouts.layout')
@section('title', 'Home Page')
@section('content')
    <div class="relative min-h-screen w-full flex flex-col overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="w-full h-full bg-cover bg-center"
                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCp83JCfKCRYgHZuZA8dzBJ3Ih5yeidFw9eQGrfMH89CUBjfUnGXPlxXvfb0DmVToP0iwQwPaK0W5XabUIrIKFxbSwRZd0LqiyyCermKdbgrB2uPKlC48Z7W_on-lUkUOnlJd62ok3dkmH7JXdQxf63fiy8lTJ7mxggLO5Vjo_p7uKwbi43Dl3GWm8aiiPqcYvOfRpQgKV513yPQakVJ-1w2d86ue27OPY3p8N9H5sr5Mg5oew18KgTO2Ehhj7cSGj-2a6ht7DOZOs');">
            </div>
            <div class="absolute inset-0 hero-white-overlay"></div>
        </div>
        <main class="relative z-10 flex flex-1 flex-col justify-center px-6 md:px-12 lg:px-24 pb-20">
            <div class="max-w-3xl space-y-8">
                <div
                    class="inline-flex items-center gap-2 bg-deep-green/5 border border-deep-green/10 px-4 py-2 rounded-full">
                    <span class="flex h-2 w-2 rounded-full bg-primary animate-pulse"></span>
                    <p class="text-deep-green text-[10px] font-bold uppercase tracking-[0.2em]">
                        Global Reforestation Mission
                    </p>
                </div>
                <h1
                    class="text-deep-green text-6xl md:text-7xl lg:text-8xl font-serif font-bold leading-[1.05] tracking-tight">
                    Rooting for <br />
                    <span class="text-vibrant-lime italic font-black">the Future</span>
                </h1>
                <p class="text-charcoal/80 text-lg md:text-xl max-w-xl font-medium leading-relaxed">
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
                    <p class="text-charcoal/50 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">
                        Trees Planted
                    </p>
                </div>
                <div class="flex flex-col">
                    <p class="text-deep-green text-4xl font-extrabold tabular-nums">45,000</p>
                    <p class="text-charcoal/50 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">
                        Tons CO2 Offset
                    </p>
                </div>
            </div>
            <div class="hidden md:flex flex-col items-center gap-3 absolute left-1/2 -translate-x-1/2 bottom-12">
                <p class="text-charcoal/30 text-[10px] font-bold uppercase tracking-[0.3em]">
                    Explore
                </p>
                <div class="w-[1px] h-14 bg-gradient-to-b from-deep-green/30 to-transparent">
                </div>
            </div>
            <div class="hidden lg:flex flex-col items-end">
                <p class="text-charcoal/40 text-[10px] font-bold mb-5 uppercase tracking-[0.15em]">
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
    <section class="relative bg-white pb-18 pt-28 px-6 botanical-bg overflow-hidden" id="media-stories">
        <div class="absolute top-0 right-0 w-96 h-96 opacity-[0.03] pointer-events-none">
            <span class="material-symbols-outlined text-[300px] text-deep-green">potted_plant</span>
        </div>
        <div class="absolute bottom-0 left-0 w-96 h-96 opacity-[0.03] pointer-events-none translate-y-20 -translate-x-20">
            <span class="material-symbols-outlined text-[350px] text-gold-accent">forest</span>
        </div>
        <div class="max-w-7xl mx-auto">
            <div class="text-center space-y-6 mb-20 max-w-3xl mx-auto">
                <div class="inline-flex items-center gap-3">
                    <div class="h-[1px] w-8 bg-gold-accent"></div>
                    <span class="text-gold-accent font-bold tracking-[0.3em] text-xs uppercase">Documenting the
                        Change</span>
                    <div class="h-[1px] w-8 bg-gold-accent"></div>
                </div>
                <h2 class="text-5xl md:text-6xl font-serif font-bold text-deep-green leading-tight">
                    Watch Our Mission <br /><span class="font-light italic">in Action</span>
                </h2>
                <p class="text-charcoal/70 text-lg leading-relaxed font-medium">
                    From the foothills of Kabul to the urban centers, witness the healing power of reforestation. Every
                    seedling planted is a breath of hope restored to our historic landscape.
                </p>
            </div>
            <div class="relative">
                <!-- Scroll buttons -->
                <button id="scrollLeft"
                    class="absolute left-0 top-1/2 -translate-y-1/2 z-20 bg-white/90 backdrop-blur-sm rounded-full p-3 shadow-lg hover:bg-white transition-all -translate-x-4">
                    <span class="material-symbols-outlined text-deep-green text-2xl">chevron_left</span>
                </button>
                <button id="scrollRight"
                    class="absolute right-0 top-1/2 -translate-y-1/2 z-20 bg-white/90 backdrop-blur-sm rounded-full p-3 shadow-lg hover:bg-white transition-all translate-x-4">
                    <span class="material-symbols-outlined text-deep-green text-2xl">chevron_right</span>
                </button>

                <div class="relative">
                    <div id="videoContainer" class="flex gap-6 overflow-x-hidden pb-8 px-4 -mx-4">
                        <div class="flex-shrink-0 w-80 group cursor-pointer" data-video-id="ScMzIvxBSi4">
                            <div
                                class="bg-white rounded-2xl overflow-hidden shadow-lg border border-white p-2 transition-all duration-300 group-hover:-translate-y-2 group-hover:shadow-xl">
                                <div class="relative aspect-[16/10] rounded-xl overflow-hidden mb-4">
                                    <img alt="Soil Preparation" class="w-full h-full object-cover"
                                        src="https://img.youtube.com/vi/ScMzIvxBSi4/hqdefault.jpg" />
                                    <div
                                        class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="material-symbols-outlined text-white text-4xl">play_circle</span>
                                    </div>
                                </div>
                                <div class="px-2 pb-2">
                                    <span class="text-[10px] font-bold text-gold-accent uppercase tracking-widest">Episode
                                        01</span>
                                    <h4
                                        class="text-deep-green font-bold text-lg mt-1 group-hover:text-primary transition-colors">
                                        Native Species Selection</h4>
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0 w-80 group cursor-pointer" data-video-id="jNQXAC9IVRw">
                            <div
                                class="bg-white rounded-2xl overflow-hidden shadow-lg border border-white p-2 transition-all duration-300 group-hover:-translate-y-2 group-hover:shadow-xl">
                                <div class="relative aspect-[16/10] rounded-xl overflow-hidden mb-4">
                                    <img alt="Community Planting" class="w-full h-full object-cover"
                                        src="https://img.youtube.com/vi/jNQXAC9IVRw/hqdefault.jpg" />
                                    <div
                                        class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="material-symbols-outlined text-white text-4xl">play_circle</span>
                                    </div>
                                </div>
                                <div class="px-2 pb-2">
                                    <span class="text-[10px] font-bold text-gold-accent uppercase tracking-widest">Episode
                                        02</span>
                                    <h4
                                        class="text-deep-green font-bold text-lg mt-1 group-hover:text-primary transition-colors">
                                        Community Voices</h4>
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0 w-80 group cursor-pointer" data-video-id="9bZkp7q19f0">
                            <div
                                class="bg-white rounded-2xl overflow-hidden shadow-lg border border-white p-2 transition-all duration-300 group-hover:-translate-y-2 group-hover:shadow-xl">
                                <div class="relative aspect-[16/10] rounded-xl overflow-hidden mb-4">
                                    <img alt="Impact Metrics" class="w-full h-full object-cover"
                                        src="https://img.youtube.com/vi/9bZkp7q19f0/hqdefault.jpg" />
                                    <div
                                        class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="material-symbols-outlined text-white text-4xl">play_circle</span>
                                    </div>
                                </div>
                                <div class="px-2 pb-2">
                                    <span class="text-[10px] font-bold text-gold-accent uppercase tracking-widest">Episode
                                        03</span>
                                    <h4
                                        class="text-deep-green font-bold text-lg mt-1 group-hover:text-primary transition-colors">
                                        Measuring Ecosystem Recovery</h4>
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0 w-80 group cursor-pointer" data-video-id="dQw4w9WgXcQ">
                            <div
                                class="bg-white rounded-2xl overflow-hidden shadow-lg border border-white p-2 transition-all duration-300 group-hover:-translate-y-2 group-hover:shadow-xl">
                                <div class="relative aspect-[16/10] rounded-xl overflow-hidden mb-4">
                                    <img alt="The Future" class="w-full h-full object-cover"
                                        src="https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg" />
                                    <div
                                        class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="material-symbols-outlined text-white text-4xl">play_circle</span>
                                    </div>
                                </div>
                                <div class="px-2 pb-2">
                                    <span class="text-[10px] font-bold text-gold-accent uppercase tracking-widest">Episode
                                        04</span>
                                    <h4
                                        class="text-deep-green font-bold text-lg mt-1 group-hover:text-primary transition-colors">
                                        A 2050 Vision</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Modal -->
    <div id="videoModal"
        class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="relative w-full max-w-4xl">
            <button id="closeModal" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
                <span class="material-symbols-outlined text-4xl">close</span>
            </button>
            <div class="relative aspect-video bg-black rounded-lg overflow-hidden">
                <iframe id="modalIframe" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>

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
                            <h4 class="text-primary font-bold tracking-[0.3em] text-xs uppercase">
                                Our Core Purpose
                            </h4>
                            <div class="relative">
                                <h2 class="text-5xl md:text-7xl font-serif font-light text-deep-green leading-tight">
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

        <section class="py-16 sm:py-24 bg-background-light/60">
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
                                <div
                                    class="mt-6 rounded-2xl bg-background-light dark:bg-background-dark p-6 border border-gray-200/70 dark:border-gray-800">
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
                                <div
                                    class="absolute -inset-2 rounded-3xl bg-gradient-to-br from-primary/20 via-emerald-500/10 to-transparent blur-2xl">
                                </div>
                                <div
                                    class="relative overflow-hidden rounded-3xl border border-gray-200/70 dark:border-gray-800 bg-white/60 dark:bg-background-dark/60 shadow-sm">
                                    <div class="aspect-[4/3]">
                                        <img alt="Planting drought-tolerant trees in the hills surrounding Kabul"
                                            class="h-full w-full object-cover"
                                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDpCpXne7VxAqOhiNQsRPM10zkTmQFSIg9ltGYZGSm3e9zV9-x3KSbEsczDjc98q1XokZ72XjiIlQacMvvwJeX0KKFYmcNKgMznUPFaVBi6vpY_Om2F7eSoPwOA_WTe3_CgzJju-3jgvczry5MZkpIYEKHbbW6qrx0CzzTFYz1kiXFAhFfBRt_fGCGO9HZrfnBKX8XEbbEWuCopaPX9QoYCpFhQAcZZHMyxOC6d2XPaQAo_lqvUV8_OLEMI9N5NMGw7PuL_NLFbIhA" />
                                    </div>
                                    <div class="p-5 sm:p-6">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Kabul, Afghanistan
                                        </p>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Small weekly action that
                                            grows into lasting change.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div
                                class="rounded-2xl bg-white dark:bg-background-dark p-6 border border-gray-200/70 dark:border-gray-800 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 3v18m9-9H3" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">6 trees every week</h3>
                                </div>
                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Consistent, measurable action—week
                                    after week.</p>
                            </div>
                            <div
                                class="rounded-2xl bg-white dark:bg-background-dark p-6 border border-gray-200/70 dark:border-gray-800 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Kabul’s hills</h3>
                                </div>
                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Restoring the land around our
                                    communities.</p>
                            </div>
                            <div
                                class="rounded-2xl bg-white dark:bg-background-dark p-6 border border-gray-200/70 dark:border-gray-800 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10V6m0 12v2" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Drought-tolerant</h3>
                                </div>
                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Choosing trees that can thrive
                                    with limited water.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="relative py-18 md:py-24 px-6 overflow-hidden bg-background-light" id="donation-section">
            <div class="absolute inset-0 z-0">
                <div class="w-full h-full bg-cover bg-center"
                    style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCp83JCfKCRYgHZuZA8dzBJ3Ih5yeidFw9eQGrfMH89CUBjfUnGXPlxXvfb0DmVToP0iwQwPaK0W5XabUIrIKFxbSwRZd0LqiyyCermKdbgrB2uPKlC48Z7W_on-lUkUOnlJd62ok3dkmH7JXdQxf63fiy8lTJ7mxggLO5Vjo_p7uKwbi43Dl3GWm8aiiPqcYvOfRpQgKV513yPQakVJ-1w2d86ue27OPY3p8N9H5sr5Mg5oew18KgTO2Ehhj7cSGj-2a6ht7DOZOs');">
                </div>
                <div class="absolute inset-0 bg-white/95"></div>
            </div>
            <div class="relative max-w-4xl mx-auto text-center mb-20">
                <h4 class="text-sage-green font-bold tracking-[0.4em] text-xs uppercase mb-4">Support the Canopy</h4>
                <h2 class="text-5xl md:text-7xl font-serif text-deep-green leading-tight">
                    Your Contribution <br />
                    <span class="font-bold italic">Roots Our Change.</span>
                </h2>
                <div class="w-24 h-[1px] bg-gold-accent mx-auto mt-8 opacity-40"></div>
            </div>

            <div class="relative grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
                <span
                    class="material-symbols-outlined absolute -top-12 -left-12 text-sage-green/20 text-7xl floating parallax-leaf">eco</span>
                <span
                    class="material-symbols-outlined absolute -bottom-12 -right-12 text-gold-accent/20 text-8xl floating parallax-leaf"
                    style="animation-delay: 2s;">spa</span>
                <span
                    class="material-symbols-outlined absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-primary/5 text-[300px] parallax-leaf">potted_plant</span>

                <div
                    class="glass-panel rounded-[2rem] p-10 flex flex-col h-full relative group hover:-translate-y-2 transition-transform duration-500 overflow-hidden">
                    <div class="absolute top-0 right-0 p-6 opacity-10">
                        <span class="material-symbols-outlined text-6xl text-gold-accent">location_on</span>
                    </div>
                    <div class="mb-8">
                        <span class="material-symbols-outlined text-gold-accent text-3xl mb-4">distance</span>
                        <h3 class="font-serif text-2xl font-bold text-deep-green mb-2">Local Handover</h3>
                        <p class="text-sage-green text-xs font-bold uppercase tracking-widest">Kabul, Afghanistan</p>
                    </div>
                    <div class="gold-line-art mb-8"></div>
                    <div class="space-y-6 flex-grow">
                        <div>
                            <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-1">Primary
                                Location</p>
                            <p class="font-serif text-lg text-charcoal/80">Haidari Market</p>
                            <p class="text-sm text-charcoal/60 italic">District 4, Kabul</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-1">Exchange
                                Office</p>
                            <p class="font-serif text-lg text-charcoal/80">Saray Shazada</p>
                            <p class="text-sm text-charcoal/60">Main Financial Hub</p>
                        </div>
                    </div>
                    <div class="mt-10 pt-6 border-t border-charcoal/5">
                        <button
                            class="w-full py-3 rounded-full border border-gold-accent/30 text-gold-accent text-sm font-bold hover:bg-gold-accent hover:text-white transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">map</span>
                            Get Directions
                        </button>
                    </div>
                </div>

                <div
                    class="glass-panel rounded-[2rem] p-10 flex flex-col h-full relative group hover:-translate-y-2 transition-transform duration-500 overflow-hidden border-sage-green/20">
                    <div class="absolute top-0 right-0 p-6 opacity-10">
                        <span class="material-symbols-outlined text-6xl text-sage-green">account_balance_wallet</span>
                    </div>
                    <div class="mb-8">
                        <span class="material-symbols-outlined text-sage-green text-3xl mb-4">payments</span>
                        <h3 class="font-serif text-2xl font-bold text-deep-green mb-2">MoneyGram &amp; WU</h3>
                        <p class="text-sage-green text-xs font-bold uppercase tracking-widest">Global Remittance</p>
                    </div>
                    <div class="gold-line-art mb-8"></div>
                    <div class="space-y-8 flex-grow">
                        <div>
                            <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-3">Receiver
                                Name</p>
                            <p class="font-serif text-3xl font-bold text-deep-green leading-tight">Mohammad Iqbal
                                Alimyar</p>
                            <p class="text-xs text-sage-green mt-2 font-medium">Verify spelling for secure
                                transfer</p>
                        </div>
                        <div class="bg-sage-green/5 p-4 rounded-xl border border-sage-green/10">
                            <p class="text-[10px] text-sage-green font-bold uppercase tracking-widest mb-2">Instructions
                            </p>
                            <p class="text-xs text-charcoal/60 leading-relaxed">Please share the MTCN or Reference
                                Number via our secure contact portal after the transaction is complete.</p>
                        </div>
                    </div>
                    <div class="mt-10 pt-6">
                        <button
                            class="w-full py-4 rounded-full bg-deep-green text-white text-sm font-bold hover:bg-deep-green/90 transition-all shadow-lg shadow-deep-green/20 flex items-center justify-center gap-2 js-copy-trigger"
                            data-copy-text="Mohammad Iqbal Alimyar">
                            <span class="material-symbols-outlined text-lg">content_copy</span>
                            Copy Full Name
                        </button>
                    </div>
                </div>

                <div
                    class="glass-panel rounded-[2rem] p-10 flex flex-col h-full relative group hover:-translate-y-2 transition-transform duration-500 overflow-hidden">
                    <div class="absolute top-0 right-0 p-6 opacity-10">
                        <span class="material-symbols-outlined text-6xl text-charcoal">account_balance</span>
                    </div>
                    <div class="mb-8">
                        <span class="material-symbols-outlined text-charcoal/70 text-3xl mb-4">assured_workload</span>
                        <h3 class="font-serif text-2xl font-bold text-deep-green mb-2">Bank Transfer</h3>
                        <p class="text-sage-green text-xs font-bold uppercase tracking-widest">International Ziraat
                            Bankası</p>
                    </div>
                    <div class="gold-line-art mb-8"></div>
                    <div class="space-y-5 flex-grow">
                        <div class="relative">
                            <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-1">IBAN
                                Number</p>
                            <div
                                class="flex items-center justify-between bg-white/40 p-3 rounded-lg border border-charcoal/5">
                                <p class="font-mono text-xs text-charcoal font-bold">TR76 0001 0021 2193 4812 5001</p>
                                <span
                                    class="material-symbols-outlined text-lg text-sage-green cursor-pointer hover:scale-110 transition-transform js-copy-trigger"
                                    data-copy-text="TR76 0001 0021 2193 4812 5001">content_copy</span>
                            </div>
                        </div>
                        <div class="relative">
                            <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-1">Swift /
                                BIC Code</p>
                            <div
                                class="flex items-center justify-between bg-white/40 p-3 rounded-lg border border-charcoal/5">
                                <p class="font-mono text-xs text-charcoal font-bold">TCZRTRA2</p>
                                <span
                                    class="material-symbols-outlined text-lg text-sage-green cursor-pointer hover:scale-110 transition-transform js-copy-trigger"
                                    data-copy-text="TCZRTRA2">content_copy</span>
                            </div>
                        </div>
                        <div class="relative">
                            <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-1">Account
                                Holder</p>
                            <p class="font-serif text-sm text-charcoal/80">EverGreen Conservation Fund</p>
                        </div>
                    </div>
                    <div class="mt-10 pt-6">
                        <button
                            class="w-full py-3 rounded-full bg-white border border-charcoal/10 text-charcoal text-sm font-bold hover:bg-charcoal/5 transition-all flex items-center justify-center gap-2 js-copy-all-bank"
                            type="button">
                            <span class="material-symbols-outlined text-lg">content_copy</span>
                            Copy All Bank Details
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-20 flex flex-col items-center gap-4 text-center">
                <div class="flex -space-x-2">
                    <div
                        class="w-10 h-10 rounded-full border-2 border-white bg-sage-green flex items-center justify-center text-white text-[10px] font-bold">
                        99+
                    </div>
                    <div
                        class="w-10 h-10 rounded-full border-2 border-white bg-gold-accent flex items-center justify-center text-white text-xs">
                        <span class="material-symbols-outlined text-sm">shield_with_heart</span>
                    </div>
                </div>
                <p class="text-charcoal/40 text-[10px] font-bold uppercase tracking-[0.3em]">Fully Encrypted &amp;
                    Secure Transfers</p>
            </div>
        </section>

    </main>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var copyTriggers = document.querySelectorAll('.js-copy-trigger[data-copy-text]');

                function showCheckMark(target) {
                    // Remove existing marker on this target if any
                    var existing = target.parentElement.querySelector('.js-copy-check');
                    if (existing) {
                        existing.remove();
                    }

                    var mark = document.createElement('span');
                    mark.className =
                        'js-copy-check inline-flex items-center justify-center ml-2 text-primary text-xs font-bold';
                    mark.textContent = '✓';
                    target.parentElement.appendChild(mark);

                    setTimeout(function() {
                        if (mark && mark.parentElement) {
                            mark.parentElement.removeChild(mark);
                        }
                    }, 1000);
                }

                copyTriggers.forEach(function(el) {
                    el.addEventListener('click', function() {
                        var text = el.getAttribute('data-copy-text');
                        if (!text) return;

                        var onSuccess = function() {
                            showCheckMark(el);
                        };

                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(text).then(onSuccess).catch(function(err) {
                                console.error('Copy failed', err);
                            });
                        } else {
                            var textarea = document.createElement('textarea');
                            textarea.value = text;
                            textarea.style.position = 'fixed';
                            textarea.style.left = '-9999px';
                            document.body.appendChild(textarea);
                            textarea.select();
                            try {
                                document.execCommand('copy');
                                onSuccess();
                            } catch (e) {
                                console.error('Copy failed', e);
                            }
                            document.body.removeChild(textarea);
                        }
                    });
                });

                // Copy all bank details in one click
                var copyAllBankBtn = document.querySelector('.js-copy-all-bank');
                if (copyAllBankBtn) {
                    copyAllBankBtn.addEventListener('click', function() {
                        var iban = 'TR76 0001 0021 2193 4812 5001';
                        var swift = 'TCZRTRA2';
                        var holder = 'EverGreen Conservation Fund';
                        var allText = 'IBAN: ' + iban + '\nSWIFT/BIC: ' + swift + '\nAccount Holder: ' + holder;

                        var onSuccess = function() {
                            showCheckMark(copyAllBankBtn);
                        };

                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(allText).then(onSuccess).catch(function(err) {
                                console.error('Copy failed', err);
                            });
                        } else {
                            var textarea = document.createElement('textarea');
                            textarea.value = allText;
                            textarea.style.position = 'fixed';
                            textarea.style.left = '-9999px';
                            document.body.appendChild(textarea);
                            textarea.select();
                            try {
                                document.execCommand('copy');
                                onSuccess();
                            } catch (e) {
                                console.error('Copy failed', e);
                            }
                            document.body.removeChild(textarea);
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection

@push('scripts')
    <script>
        // Video carousel scroll functionality
        document.addEventListener('DOMContentLoaded', function() {
            const videoContainer = document.getElementById('videoContainer');
            const scrollLeftBtn = document.getElementById('scrollLeft');
            const scrollRightBtn = document.getElementById('scrollRight');

            if (videoContainer && scrollLeftBtn && scrollRightBtn) {
                const scrollAmount = 340; // Width of one video card + gap

                scrollLeftBtn.addEventListener('click', function() {
                    videoContainer.scrollBy({
                        left: -scrollAmount,
                        behavior: 'smooth'
                    });
                });

                scrollRightBtn.addEventListener('click', function() {
                    videoContainer.scrollBy({
                        left: scrollAmount,
                        behavior: 'smooth'
                    });
                });

                // Update button visibility based on scroll position
                function updateButtonVisibility() {
                    const maxScroll = videoContainer.scrollWidth - videoContainer.clientWidth;

                    if (videoContainer.scrollLeft <= 0) {
                        scrollLeftBtn.style.opacity = '0.3';
                        scrollLeftBtn.style.pointerEvents = 'none';
                    } else {
                        scrollLeftBtn.style.opacity = '1';
                        scrollLeftBtn.style.pointerEvents = 'auto';
                    }

                    if (videoContainer.scrollLeft >= maxScroll - 10) {
                        scrollRightBtn.style.opacity = '0.3';
                        scrollRightBtn.style.pointerEvents = 'none';
                    } else {
                        scrollRightBtn.style.opacity = '1';
                        scrollRightBtn.style.pointerEvents = 'auto';
                    }
                }

                videoContainer.addEventListener('scroll', updateButtonVisibility);
                updateButtonVisibility(); // Initial check
            }

            // YouTube video functionality
            const videoCards = document.querySelectorAll('[data-video-id]');
            const videoModal = document.getElementById('videoModal');
            const modalIframe = document.getElementById('modalIframe');
            const closeModal = document.getElementById('closeModal');

            videoCards.forEach(function(card) {
                card.addEventListener('click', function() {
                    const videoId = this.getAttribute('data-video-id');

                    // Set iframe src with autoplay
                    modalIframe.setAttribute('src', 'https://www.youtube.com/embed/' + videoId +
                        '?autoplay=1&rel=0');

                    // Show modal
                    videoModal.classList.remove('hidden');
                    videoModal.classList.add('flex');

                    // Prevent body scroll
                    document.body.style.overflow = 'hidden';
                });
            });

            // Close modal when clicking close button
            closeModal.addEventListener('click', function() {
                closeVideoModal();
            });

            // Close modal when clicking outside
            videoModal.addEventListener('click', function(e) {
                if (e.target === videoModal) {
                    closeVideoModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !videoModal.classList.contains('hidden')) {
                    closeVideoModal();
                }
            });

            function closeVideoModal() {
                // Hide modal
                videoModal.classList.add('hidden');
                videoModal.classList.remove('flex');

                // Clear iframe src to stop video
                modalIframe.setAttribute('src', '');

                // Restore body scroll
                document.body.style.overflow = '';
            }
        });
    </script>
@endpush
