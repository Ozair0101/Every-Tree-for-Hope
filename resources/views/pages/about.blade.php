@extends('layouts.layout')
@section('title', 'About Page')
@section('content')
    <!-- Hero Section -->
    <header class="relative h-screen w-full flex items-center justify-center overflow-hidden">
        <img alt="Kabul Hills" class="absolute inset-0 w-full h-full object-cover brightness-75 scale-105"
            data-alt="Cinematic sunrise over the rugged Kabul hills"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuA5c6jFI7yLgeT_1m1wGniEFqNjyjb0sayKKCfyPXgaSvxt-xCTxNOI0BhGhB1yDlMuQhNtDsBBP1ruiG8uckdSFF9LNbJqNcsGcIxBC_IgvrYLDLlu9yh9msErageJtkVTeX_YlNi5x2R8XAkZmZwDKhLoqG8v_Ss6NwtcaxF_onGkQJQKg5b4f-InFhTWpqR7kYyE3FNAuqG4TxZp_wArs4VelnWZkYIL17OFYm-F9hj7oLSx4nuP08XJB_0HvrTYS8KADP0dLKI" />
        <div
            class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-background-light dark:to-background-dark">
        </div>
        <div class="relative z-10 text-center max-w-4xl px-4">
            <span class="inline-block text-primary font-bold tracking-[0.3em] uppercase mb-4 drop-shadow-md">Our
                Legacy</span>
            <h1 class="text-7xl md:text-9xl font-display text-white mb-8 leading-tight">The Heart <br />Behind the Green
            </h1>
            <p class="text-xl md:text-2xl text-white/90 font-light max-w-2xl mx-auto italic">
                Restoring the lungs of Kabul, one seedling at a time.
            </p>
        </div>
        <!-- Floating Leaf Elements (Decorative) -->
        <div class="absolute top-1/4 left-10 text-primary/30 rotate-12">
            <span class="material-symbols-outlined text-6xl">energy_savings_leaf</span>
        </div>
        <div class="absolute bottom-1/4 right-10 text-primary/20 -rotate-45">
            <span class="material-symbols-outlined text-8xl">spa</span>
        </div>
    </header>
    <!-- Our Story Section -->
    <section class="py-32 px-8 max-w-7xl mx-auto botanical-pattern">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            <div class="lg:col-span-5 relative">
                <div class="aspect-[3/4] rounded-xl overflow-hidden shadow-2xl">
                    <img alt="Tree Nursery" class="w-full h-full object-cover"
                        data-alt="Lush green saplings in a modern nursery"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCqqBZKmAQU-OXMuI2cto6vKbaVrhY_EbkjmuBxHlIzxEv1AN6RYebK8f08gOYvURua98t1kQBTu7NsjXFHyZwsi8MN7Anjn_OSerI4aeM0Hurw3YpvgKOrWAuz0PVNfEJZDJlzj3fhKz4u-GNZChXJHNpT-C4MewtzkjQkvsolUejTwfjfxgtn2rTl7bDsKPEiPoygYoIbcpvcEYSC7UxcOCza-iV85fDXz2Nr-fmcgVsw9bnlOUmgYPJh2gSn9drBujJVaH5-8t8" />
                </div>
                <div class="absolute -bottom-10 -right-10 w-64 h-64 bg-accent-gold/10 rounded-full -z-10 blur-3xl">
                </div>
            </div>
            <div class="lg:col-span-7">
                <span class="text-accent-gold font-bold tracking-widest uppercase text-sm mb-4 block">Our Origin
                    Story</span>
                <h2 class="text-5xl md:text-6xl font-display mb-8 text-stone-900 dark:text-white">A Grassroots Movement
                    Reimagined.</h2>
                <div class="space-y-6 text-lg text-stone-600 dark:text-stone-300 leading-relaxed font-light">
                    <p>
                        It began in the spring of 2018, not in a boardroom, but on the dusty slopes of the Koh-e-Asmai
                        mountains. A small group of Kabul residents, tired of the smog-filled horizons, decided to
                        reclaim their city's natural heritage.
                    </p>
                    <p>
                        What started with fifty saplings carried by hand has evolved into a sophisticated botanical
                        operation. We blend ancient Afghan agricultural wisdom with modern ecological data to ensure
                        every tree we plant survives the harsh seasons of our capital.
                    </p>
                    <div class="flex items-center gap-6 pt-6">
                        <div class="h-px flex-1 bg-accent-gold/30"></div>
                        <span class="text-accent-gold italic font-display text-2xl">Rooted in Resilience</span>
                        <div class="h-px flex-1 bg-accent-gold/30"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Mission & Vision Section -->
    <section class="py-24 bg-stone-100 dark:bg-stone-900/50">
        <div class="max-w-7xl mx-auto px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Mission Card -->
                <div
                    class="glass-card p-12 rounded-xl relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
                    <div
                        class="absolute top-0 right-0 p-8 text-accent-gold/20 group-hover:text-accent-gold/40 transition-colors">
                        <span class="material-symbols-outlined text-8xl">landscape</span>
                    </div>
                    <h3 class="text-3xl font-display mb-6 text-stone-900 dark:text-white">Our Mission</h3>
                    <p class="text-xl text-stone-600 dark:text-stone-300 leading-relaxed font-light relative z-10">
                        To transform Kabul's urban landscape through sustainable reforestation, fostering a culture of
                        environmental stewardship that secures a breathable future for all citizens.
                    </p>
                    <div class="mt-8 flex items-center gap-2 text-accent-gold">
                        <div class="w-8 h-px bg-accent-gold"></div>
                        <span class="uppercase tracking-widest text-xs font-bold">The Promise</span>
                    </div>
                </div>
                <!-- Vision Card -->
                <div
                    class="glass-card p-12 rounded-xl relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
                    <div class="absolute top-0 right-0 p-8 text-primary/20 group-hover:text-primary/40 transition-colors">
                        <span class="material-symbols-outlined text-8xl">visibility</span>
                    </div>
                    <h3 class="text-3xl font-display mb-6 text-stone-900 dark:text-white">Our Vision</h3>
                    <p class="text-xl text-stone-600 dark:text-stone-300 leading-relaxed font-light relative z-10">
                        A Kabul where the hills are permanently green, the air is pristine, and every neighborhood
                        thrives under the shade of a resilient, native urban forest.
                    </p>
                    <div class="mt-8 flex items-center gap-2 text-primary">
                        <div class="w-8 h-px bg-primary"></div>
                        <span class="uppercase tracking-widest text-xs font-bold">The Horizon</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Founder's Message -->
    <section class="py-32 px-8 bg-white dark:bg-background-dark">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row items-center gap-16">
                <div class="w-full md:w-1/3">
                    <div class="aspect-square rounded-full border-8 border-background-light overflow-hidden shadow-xl">
                        <img alt="Mohammad Iqbal Alimyar" class="w-full h-full object-cover"
                            data-alt="Portrait of Mohammad Iqbal Alimyar looking inspired"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBFc6_q8OE6jhUzlrpiaswc_o1CdqQ6hwH8oF_3pQn90pTe2wRrUs6AbN1pownHb-U4G2ZPOfTeavje_HdOf6pa0lre-F_e5gUYCG5ddp1IxbJZSBFLGoQt07wx0egQigZRL-smajXqCMUYkA0vFpEHww4ES4q1mDoi3OM3YMbCzna-mfioJcxPU0SNIR1lUDvupNpDoGRkcVsfSlNKge7L0hkbviXomGrQ_HyrlKKIT1oImxjSTAWKJ4EHB0a0uToItW1H92bsNIQ" />
                    </div>
                </div>
                <div class="w-full md:w-2/3">
                    <span class="text-primary font-bold uppercase tracking-tighter text-4xl mb-6 block">"</span>
                    <blockquote
                        class="text-3xl md:text-4xl font-display italic text-stone-800 dark:text-stone-200 mb-8 leading-snug">
                        Every tree planted is a promise kept to the future generations of Kabul. We aren't just planting
                        wood and leaves; we are planting hope.
                    </blockquote>
                    <div class="flex flex-col">
                        <span class="text-xl font-bold text-stone-900 dark:text-white">Mohammad Iqbal Alimyar</span>
                        <span class="text-stone-500 uppercase tracking-widest text-sm">Founder &amp; Chief
                            Visionary</span>
                        <div class="mt-4 signature-font text-4xl text-accent-gold">
                            M. Iqbal Alimyar
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Our Team Section -->
    <section class="py-32 bg-background-light dark:bg-stone-950 px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20">
                <h2 class="text-5xl font-display mb-4">The Visionaries</h2>
                <p class="text-stone-500 tracking-widest uppercase text-sm">A dedicated collective of ecologists and
                    activists</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12">
                @php
                    $teamMembers = \App\Models\Team::active()->ordered()->get();
                @endphp
                @forelse($teamMembers as $member)
                    <div class="group text-center">
                        <div
                            class="relative mb-6 mx-auto w-64 h-64 rounded-full overflow-hidden border-4 border-transparent group-hover:border-primary transition-all duration-500 p-2">
                            <img alt="{{ $member->name }}"
                                class="w-full h-full object-cover rounded-full filter grayscale group-hover:grayscale-0 transition-all duration-500"
                                data-alt="Professional portrait of {{ $member->name }}"
                                src="{{ $member->full_image_url }}" />
                            @if ($member->linkedin_url || $member->email)
                                <div
                                    class="absolute inset-0 bg-primary/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    <div class="flex gap-4">
                                        @if ($member->linkedin_url)
                                            <a class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-primary"
                                                href="{{ $member->linkedin_url }}" target="_blank"><span
                                                    class="material-symbols-outlined text-sm">link</span></a>
                                        @endif
                                        @if ($member->email)
                                            <a class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-primary"
                                                href="mailto:{{ $member->email }}"><span
                                                    class="material-symbols-outlined text-sm">mail</span></a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <h4 class="text-xl font-bold text-stone-900 dark:text-white">{{ $member->name }}</h4>
                        <p class="text-stone-500 font-light">{{ $member->position }}</p>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-stone-500">No team members available at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    <!-- Partners Section -->
    <section class="py-24 bg-white dark:bg-stone-900 overflow-hidden">
        <div class="max-w-7xl mx-auto px-8">
            <h5 class="text-center text-stone-400 uppercase tracking-[0.4em] text-xs font-bold mb-16">Global Partners
                &amp; Allies</h5>
            <div class="flex flex-wrap justify-center items-center gap-16 opacity-60 hover:opacity-100 transition-opacity">
                <!-- Mock Logos -->
                <div class="flex items-center gap-3 grayscale">
                    <span class="material-symbols-outlined text-4xl">public</span>
                    <span class="text-2xl font-display font-bold">EarthCare</span>
                </div>
                <div class="flex items-center gap-3 grayscale">
                    <span class="material-symbols-outlined text-4xl">water_drop</span>
                    <span class="text-2xl font-display font-bold">HydroAlliance</span>
                </div>
                <div class="flex items-center gap-3 grayscale">
                    <span class="material-symbols-outlined text-4xl">foundation</span>
                    <span class="text-2xl font-display font-bold">CivicGreen</span>
                </div>
                <div class="flex items-center gap-3 grayscale">
                    <span class="material-symbols-outlined text-4xl">forest</span>
                    <span class="text-2xl font-display font-bold">NatureNet</span>
                </div>
                <div class="flex items-center gap-3 grayscale">
                    <span class="material-symbols-outlined text-4xl">park</span>
                    <span class="text-2xl font-display font-bold">KabulUnity</span>
                </div>
            </div>
        </div>
    </section>
@endsection
