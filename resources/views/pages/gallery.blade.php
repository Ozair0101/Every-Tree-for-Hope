@extends('layouts.layout')
@section('title', 'Gallery Page')
@section('content')

    <main class="flex-grow">
        <section class="relative h-[600px] flex items-center overflow-hidden bg-neutral-900">
            <div class="absolute right-0 top-0 w-full md:w-2/3 h-full">
                <img alt="Sun-drenched forest canopy" class="w-full h-full object-cover opacity-70"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCiSLJVNhgqX_rzJKgvmlDvloU-fcMmenRiN6mZ1Wqevab2hUyke18xq1TOFuMxsqoCVU8oS5wF9skWOEUP9MZePgGonhgkM6bX84t6Iv02iR2PilvhZHea3utpkTvdOoNtvq1v8eYMNuDCvGmkJejzO3YNLClwcjgDP-j-N6T3tqgEvhVO10uaCBCh9xyPNLY_dY7FJmn0WdOo_CT5uzpI1QlpJjhOlMUmQYCjmdVc6qoZyX_3kNo3kLI5c_p04yr_6WNLSMZ-im8" />
                <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
            </div>
            <div class="relative w-full h-full max-w-7xl mx-auto px-6 flex items-center">
                <div class="relative z-10 max-w-2xl">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/20 border border-primary/40 text-white text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur-sm">
                        <span class="material-icons text-xs">nature_people</span>
                        Botanical Collection
                    </div>
                    <h1 class="text-6xl md:text-7xl font-serif text-white mb-6 leading-[1.1] drop-shadow-2xl">
                        Species <br />
                        <span class="italic text-primary-light">Gallery</span>
                    </h1>
                    <p class="text-lg md:text-xl text-gray-200 font-medium leading-relaxed max-w-lg mb-8 drop-shadow-lg">
                        Explore our curated collection of botanical specimens. Each species is selected for its high
                        environmental impact and regional resilience in the face of climate change.
                    </p>
                    <div class="flex gap-4">
                        <button
                            class="bg-primary hover:bg-primary/90 text-white px-8 py-4 rounded-lg text-sm font-bold transition-all uppercase tracking-wider shadow-lg hover:shadow-xl">
                            Explore Catalog
                        </button>
                        <button
                            class="flex items-center gap-2 px-6 py-4 bg-white/10 backdrop-blur-sm text-white border border-white/20 hover:bg-white/20 transition-all rounded-lg text-sm font-bold uppercase tracking-wider">
                            <span class="material-icons">play_circle_filled</span>
                            Watch Growth Study
                        </button>
                    </div>
                </div>
            </div>
        </section>
        <section
            class="py-8 mt-20 bg-white dark:bg-background-dark/50 sticky top-20 z-40 border-b border-neutral-mid dark:border-white/5">
            <div class="max-w-7xl mx-auto px-6 flex flex-wrap items-center justify-between gap-6">
                <div class="flex items-center gap-3 overflow-x-auto pb-2 md:pb-0 no-scrollbar">
                    <button
                        class="filter-btn-active whitespace-nowrap px-6 py-2 border border-neutral-mid dark:border-white/20 rounded-lg text-sm font-semibold transition-all">All
                        Species</button>
                    <button
                        class="whitespace-nowrap px-6 py-2 border border-neutral-mid dark:border-white/20 hover:border-primary rounded-lg text-sm font-semibold transition-all bg-white dark:bg-transparent">Coniferous</button>
                    <button
                        class="whitespace-nowrap px-6 py-2 border border-neutral-mid dark:border-white/20 hover:border-primary rounded-lg text-sm font-semibold transition-all bg-white dark:bg-transparent">Deciduous</button>
                    <button
                        class="whitespace-nowrap px-6 py-2 border border-neutral-mid dark:border-white/20 hover:border-primary rounded-lg text-sm font-semibold transition-all bg-white dark:bg-transparent">Fruit
                        Trees</button>
                    <button
                        class="whitespace-nowrap px-6 py-2 border border-neutral-mid dark:border-white/20 hover:border-primary rounded-lg text-sm font-semibold transition-all bg-white dark:bg-transparent">Fast
                        Growing</button>
                    <button
                        class="whitespace-nowrap px-6 py-2 border border-neutral-mid dark:border-white/20 hover:border-primary rounded-lg text-sm font-semibold transition-all bg-white dark:bg-transparent">Native</button>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <span class="material-icons text-lg">sort</span>
                    <span class="font-semibold">Sort by:</span>
                    <select
                        class="bg-transparent border-none focus:ring-0 font-bold text-neutral-high dark:text-white cursor-pointer">
                        <option>Most Popular</option>
                        <option>CO2 Rate</option>
                        <option>Alphabetical</option>
                    </select>
                </div>
            </div>
        </section>
        <section class="py-16 max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div
                    class="species-card group bg-white dark:bg-white/5 border border-neutral-mid dark:border-white/10 rounded-xl overflow-hidden transition-all hover:shadow-2xl hover:shadow-primary/5">
                    <div
                        class="relative aspect-square bg-white dark:bg-neutral-low/10 overflow-hidden border-b border-neutral-mid dark:border-white/10">
                        <img alt="Silver Birch Tree"
                            class="tree-image w-full h-full object-contain transition-transform duration-700 p-8"
                            data-alt="Lone silver birch tree on white background"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDLX5LpklIa7X_VSEmY1yPZNkWOfLwCjdXSqmVNvsOS09wAQAPSIbDrpzWOeQMisLfpkrx2aqtS8Io34Z_0HMYoXAecbZiT71ZxXByr1aVXqMEprPGCAB3TCPAXvhZYKeG21zszp39gyZKSEvV6GfxpXSbmta5hXrm8jzA_bIj7Jd8iteWTx40k5FZD0VdqnM_BRp7bDsJmzg9XHWd2i-4kfy0gZgrKwzvKXbSE42UK9rZPnzS3u0huwvbWy_Qfq39jZq6257VuPsc" />
                        <div
                            class="absolute top-4 right-4 bg-white/80 backdrop-blur rounded-full p-2 border border-neutral-mid">
                            <span class="material-icons text-primary">eco</span>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-2xl font-800 text-neutral-high dark:text-white">Silver Birch</h3>
                            <span
                                class="text-xs font-bold uppercase tracking-widest text-primary bg-primary/10 px-2 py-1 rounded">Fast
                                Growing</span>
                        </div>
                        <p class="text-sm italic text-gray-500 mb-6 dark:text-gray-400">Betula pendula</p>
                        <div class="grid grid-cols-3 gap-4 mb-8">
                            <div class="text-center">
                                <p class="text-xs text-gray-400 font-bold uppercase mb-1">Max Height</p>
                                <p class="text-sm font-800">30m</p>
                            </div>
                            <div class="text-center border-x border-neutral-mid dark:border-white/10">
                                <p class="text-xs text-gray-400 font-bold uppercase mb-1">CO2 Abs.</p>
                                <p class="text-sm font-800">3.1t/yr</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-400 font-bold uppercase mb-1">Lifespan</p>
                                <p class="text-sm font-800">100y</p>
                            </div>
                        </div>
                        <button
                            class="w-full bg-neutral-low dark:bg-white/5 hover:bg-primary hover:text-white border border-neutral-mid dark:border-white/20 py-3 rounded-lg font-bold text-sm transition-all group-hover:border-primary">
                            Plant This Tree
                        </button>
                    </div>
                </div>
                <div
                    class="species-card group bg-white dark:bg-white/5 border border-neutral-mid dark:border-white/10 rounded-xl overflow-hidden transition-all hover:shadow-2xl hover:shadow-primary/5">
                    <div
                        class="relative aspect-square bg-white dark:bg-neutral-low/10 overflow-hidden border-b border-neutral-mid dark:border-white/10">
                        <img alt="English Oak Tree"
                            class="tree-image w-full h-full object-contain transition-transform duration-700 p-8"
                            data-alt="Majestic oak tree specimen studio shot"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBHgwEvrjg1lVr-7Wu70eGA-xyCTWRmdoSCfW8-cFOJ09OC5fk6Cy46HsdJPdcidmUpTTzYmCj80yG0_Tyd9M1y4aihAXmEIaZQ8tu29n04hkmu0E8VQRCvl2ZhyE9XDoHx59Riv8znACR18SRKfcbnXMyas2qKeew5bGk8AsIiVnOnk3vXOQ2efyBnZx25NbB5meRQzl8PXkjV_IyElPvSBwL2h2UQDg2rNcdLKu3463GigJKD90IKZ-FotxkJDVItWxg1xMt20Bw" />
                        <div
                            class="absolute top-4 right-4 bg-white/80 backdrop-blur rounded-full p-2 border border-neutral-mid">
                            <span class="material-icons text-primary">workspace_premium</span>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-2xl font-800 text-neutral-high dark:text-white">English Oak</h3>
                            <span
                                class="text-xs font-bold uppercase tracking-widest text-primary bg-primary/10 px-2 py-1 rounded">Native</span>
                        </div>
                        <p class="text-sm italic text-gray-500 mb-6 dark:text-gray-400">Quercus robur</p>
                        <div class="grid grid-cols-3 gap-4 mb-8">
                            <div class="text-center">
                                <p class="text-xs text-gray-400 font-bold uppercase mb-1">Max Height</p>
                                <p class="text-sm font-800">40m</p>
                            </div>
                            <div class="text-center border-x border-neutral-mid dark:border-white/10">
                                <p class="text-xs text-gray-400 font-bold uppercase mb-1">CO2 Abs.</p>
                                <p class="text-sm font-800">5.8t/yr</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-400 font-bold uppercase mb-1">Lifespan</p>
                                <p class="text-sm font-800">500y+</p>
                            </div>
                        </div>
                        <button
                            class="w-full bg-neutral-low dark:bg-white/5 hover:bg-primary hover:text-white border border-neutral-mid dark:border-white/20 py-3 rounded-lg font-bold text-sm transition-all group-hover:border-primary">
                            Plant This Tree
                        </button>
                    </div>
                </div>
                <div
                    class="species-card group bg-white dark:bg-white/5 border border-neutral-mid dark:border-white/10 rounded-xl overflow-hidden transition-all hover:shadow-2xl hover:shadow-primary/5">
                    <div
                        class="relative aspect-square bg-white dark:bg-neutral-low/10 overflow-hidden border-b border-neutral-mid dark:border-white/10">
                        <img alt="Japanese Maple Tree"
                            class="tree-image w-full h-full object-contain transition-transform duration-700 p-8"
                            data-alt="Vibrant red japanese maple on white"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBIXUvtlcpu8U8l6m47FWS89bwgjd_f1XVrRKqirlS22FOE3ILfYDS-wTMC7q6MzZdx4OkOJaayqC8DDcYWiShrQx3Q0pPyyj_kYbsPykDpAk5OyOxtG2JfbSY_4MUvJA0jJ1tMoRmzDYRbxjG6ih3YgeybaUjs0k6Ht_NIc6MpeeGUw2kDGrlCjbVC6ltk4pFRv9mOViIMUhxkKoBZEeZb6NN22AoDxsn58owrIANzzEYZETpJUhZq2I_9jq_BiTnmH1VLo3W7kUk" />
                    </div>
                    <div class="p-8">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-2xl font-800 text-neutral-high dark:text-white">Japanese Maple</h3>
                            <span
                                class="text-xs font-bold uppercase tracking-widest text-primary bg-primary/10 px-2 py-1 rounded">Deciduous</span>
                        </div>
                        <p class="text-sm italic text-gray-500 mb-6 dark:text-gray-400">Acer palmatum</p>
                        <div class="grid grid-cols-3 gap-4 mb-8">
                            <div class="text-center">
                                <p class="text-xs text-gray-400 font-bold uppercase mb-1">Max Height</p>
                                <p class="text-sm font-800">10m</p>
                            </div>
                            <div class="text-center border-x border-neutral-mid dark:border-white/10">
                                <p class="text-xs text-gray-400 font-bold uppercase mb-1">CO2 Abs.</p>
                                <p class="text-sm font-800">1.2t/yr</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-400 font-bold uppercase mb-1">Lifespan</p>
                                <p class="text-sm font-800">80y</p>
                            </div>
                        </div>
                        <button
                            class="w-full bg-neutral-low dark:bg-white/5 hover:bg-primary hover:text-white border border-neutral-mid dark:border-white/20 py-3 rounded-lg font-bold text-sm transition-all group-hover:border-primary">
                            Plant This Tree
                        </button>
                    </div>
                </div>
            </div>
            <div class="mt-20 flex flex-col items-center gap-6">
                <button
                    class="px-12 py-4 border-2 border-primary text-primary hover:bg-primary hover:text-white rounded-lg font-800 text-sm transition-all uppercase tracking-widest">
                    Load More Species
                </button>
                <p class="text-sm text-gray-400 font-medium">Displaying 6 of 124 available species</p>
            </div>
        </section>
    </main>
@endsection

<!-- Custom Styles for Gallery -->
@push('styles')
    <style>
        .species-card:hover .tree-image {
            transform: scale(1.05);
        }

        .filter-btn-active {
            border-color: #22c55e !important;
            color: #22c55e !important;
            background-color: rgba(34, 197, 94, 0.05);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .hero-gradient-overlay {
            background: linear-gradient(to right, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0.95) 30%, rgba(255, 255, 255, 0.4) 70%, rgba(255, 255, 255, 0) 100%);
        }

        .text-primary-light {
            color: #86efac !important;
        }

        .bg-primary/20 {
            background-color: rgba(34, 197, 94, 0.2) !important;
        }

        .border-primary/40 {
            border-color: rgba(34, 197, 94, 0.4) !important;
        }
    </style>
@endpush
