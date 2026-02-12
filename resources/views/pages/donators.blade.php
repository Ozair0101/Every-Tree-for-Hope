@extends('layouts.layout')
@section('title', 'Donators Page')
@section('content')
    <main>
        <section class="relative min-h-[85vh] flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0 z-0">
                <img alt="Sun-drenched hills of Kabul" class="w-full h-full object-cover scale-105"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCp83JCfKCRYgHZuZA8dzBJ3Ih5yeidFw9eQGrfMH89CUBjfUnGXPlxXvfb0DmVToP0iwQwPaK0W5XabUIrIKFxbSwRZd0LqiyyCermKdbgrB2uPKlC48Z7W_on-lUkUOnlJd62ok3dkmH7JXdQxf63fiy8lTJ7mxggLO5Vjo_p7uKwbi43Dl3GWm8aiiPqcYvOfRpQgKV513yPQakVJ-1w2d86ue27OPY3p8N9H5sr5Mg5oew18KgTO2Ehhj7cSGj-2a6ht7DOZOs" />
                <div class="absolute inset-0 bg-gradient-to-t from-deep-green/60 via-deep-green/20 to-transparent">
                </div>
                <div class="absolute inset-0 bg-black/20"></div>
            </div>
            <div class="relative z-10 text-center space-y-8 negative-space-container pt-20">
                <div
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20">
                    <span class="material-symbols-outlined text-gold-accent text-sm">verified</span>
                    <span class="text-white text-[9px] font-bold uppercase tracking-[0.25em]">Transparency &amp;
                        Recognition</span>
                </div>
                <h1 class="text-white text-6xl md:text-9xl font-serif font-bold tracking-tight drop-shadow-2xl">
                    Meet the <span class="italic font-light text-gold-accent">Guardians</span>
                </h1>
                <p class="text-white/90 text-lg md:text-2xl font-light max-w-2xl mx-auto leading-relaxed drop-shadow-lg">
                    Celebrating the individuals and families who fuel the restoration of Kabul’s green heritage through
                    dedicated tree planting and environmental stewardship.
                </p>
                <div class="pt-8">
                    <span
                        class="material-symbols-outlined text-white animate-bounce text-3xl">keyboard_double_arrow_down</span>
                </div>
            </div>
        </section>
        <section class="py-32 bg-white relative botanical-pattern">
            <div class="negative-space-container">
                <div class="text-center mb-20 space-y-4">
                    <h4 class="text-gold-accent font-bold tracking-[0.4em] text-[10px] uppercase">Institutional Support
                    </h4>
                    <h2 class="text-5xl font-serif text-deep-green">Our Visionary Sponsors</h2>
                    <div class="w-24 h-1 bg-gold-accent/30 mx-auto mt-6"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                    <div class="md:col-span-12 lg:col-span-8 group">
                        <div
                            class="glass-sponsor rounded-[2.5rem] p-10 lg:p-16 flex flex-col md:flex-row items-center gap-12 relative">
                            <div class="absolute top-8 right-8 flex items-center gap-2">
                                <span
                                    class="px-4 py-1.5 rounded-full bg-gold-accent/10 border border-gold-accent/20 text-gold-accent text-[9px] font-bold uppercase tracking-widest">Lead
                                    Partner</span>
                            </div>
                            <div class="w-full md:w-1/3 flex justify-center">
                                <img alt="Sponsor Logo" class="w-full max-w-[200px] grayscale-hover"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCp83JCfKCRYgHZuZA8dzBJ3Ih5yeidFw9eQGrfMH89CUBjfUnGXPlxXvfb0DmVToP0iwQwPaK0W5XabUIrIKFxbSwRZd0LqiyyCermKdbgrB2uPKlC48Z7W_on-lUkUOnlJd62ok3dkmH7JXdQxf63fiy8lTJ7mxggLO5Vjo_p7uKwbi43Dl3GWm8aiiPqcYvOfRpQgKV513yPQakVJ-1w2d86ue27OPY3p8N9H5sr5Mg5oew18KgTO2Ehhj7cSGj-2a6ht7DOZOs" />
                            </div>
                            <div class="w-full md:w-2/3 space-y-6 text-center md:text-left">
                                <h3 class="text-3xl font-serif text-deep-green">Global Green Foundation</h3>
                                <p class="text-charcoal/70 text-lg font-light leading-relaxed">
                                    Spearheading the irrigation infrastructure for the Kabul foothills reforestation
                                    initiative, ensuring the survival of over 10,000 native saplings.
                                </p>
                                <div class="pt-4 flex flex-col sm:flex-row items-center gap-6">
                                    <a class="text-deep-green font-bold text-[10px] uppercase tracking-[0.2em] border-b-2 border-gold-accent pb-1 hover:text-gold-accent transition-colors"
                                        href="#">View Partner Profile</a>
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-gold-accent">eco</span>
                                        <span class="text-charcoal/50 text-[10px] font-bold uppercase">12,000+ Trees
                                            Sponsored</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-6 lg:col-span-4 group h-full">
                        <div
                            class="glass-sponsor rounded-[2.5rem] p-10 flex flex-col h-full justify-between hover:-translate-y-2 transition-transform duration-500">
                            <div class="space-y-6">
                                <div class="flex justify-between items-start">
                                    <img alt="Sponsor Logo" class="max-h-12 w-auto grayscale-hover"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDqbsRfkaGON8ghibhqD0FPuvhemuTfvhq_g1lSPz3wYG6q0ViQhZ6BAOn1lWNpqzfmJr0XJbLIN-a7O0EjzNovxSpDbYB4X0qR1gC4IqW8TdOQV_FcmFPAICD1ok8hMNr1-NMIrWGuKXWQfMn3RILiUlG7cXr5g6Vg-21sIzI_9FnD_f9QHbe_IdU71Y4_6JOlRzpP92BALfb2tzoLmwJ9AMpwLqxBdnLJKOIDCKVdYIiqcT_IJ5iOsBHRziCrooDb-fS1XolmyJM" />
                                    <span
                                        class="px-3 py-1 rounded-full bg-stone-100 text-charcoal/60 text-[8px] font-bold uppercase tracking-widest">Corporate
                                        Partner</span>
                                </div>
                                <div class="space-y-3">
                                    <h3 class="text-xl font-serif text-deep-green">Eco-Ventures Group</h3>
                                    <p class="text-charcoal/60 text-sm font-light leading-relaxed">
                                        Pioneering sustainable employment for 50 local families through our
                                        community-led nursery program.
                                    </p>
                                </div>
                            </div>
                            <div class="mt-8">
                                <a class="text-gold-accent font-bold text-[9px] uppercase tracking-widest group-hover:text-deep-green transition-colors flex items-center gap-2"
                                    href="#">
                                    View Partner Profile <span
                                        class="material-symbols-outlined text-sm">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-6 lg:col-span-4 group h-full">
                        <div
                            class="glass-sponsor rounded-[2.5rem] p-10 flex flex-col h-full justify-between hover:-translate-y-2 transition-transform duration-500">
                            <div class="space-y-6">
                                <div class="flex justify-between items-start">
                                    <img alt="Sponsor Logo" class="max-h-12 w-auto grayscale-hover"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCp83JCfKCRYgHZuZA8dzBJ3Ih5yeidFw9eQGrfMH89CUBjfUnGXPlxXvfb0DmVToP0iwQwPaK0W5XabUIrIKFxbSwRZd0LqiyyCermKdbgrB2uPKlC48Z7W_on-lUkUOnlJd62ok3dkmH7JXdQxf63fiy8lTJ7mxggLO5Vjo_p7uKwbi43Dl3GWm8aiiPqcYvOfRpQgKV513yPQakVJ-1w2d86ue27OPY3p8N9H5sr5Mg5oew18KgTO2Ehhj7cSGj-2a6ht7DOZOs" />
                                    <span
                                        class="px-3 py-1 rounded-full bg-stone-100 text-charcoal/60 text-[8px] font-bold uppercase tracking-widest">Environmental
                                        NGO</span>
                                </div>
                                <div class="space-y-3">
                                    <h3 class="text-xl font-serif text-deep-green">The Kabul Collective</h3>
                                    <p class="text-charcoal/60 text-sm font-light leading-relaxed">
                                        Funding the restoration of historic cedar forests and public parks within the
                                        city's green belt.
                                    </p>
                                </div>
                            </div>
                            <div class="mt-8">
                                <a class="text-gold-accent font-bold text-[9px] uppercase tracking-widest group-hover:text-deep-green transition-colors flex items-center gap-2"
                                    href="#">
                                    View Partner Profile <span
                                        class="material-symbols-outlined text-sm">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-6 lg:col-span-4 group h-full">
                        <div
                            class="glass-sponsor rounded-[2.5rem] p-10 flex flex-col h-full justify-between hover:-translate-y-2 transition-transform duration-500">
                            <div class="space-y-6">
                                <div class="flex justify-between items-start">
                                    <img alt="Sponsor Logo" class="max-h-12 w-auto grayscale-hover"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCp83JCfKCRYgHZuZA8dzBJ3Ih5yeidFw9eQGrfMH89CUBjfUnGXPlxXvfb0DmVToP0iwQwPaK0W5XabUIrIKFxbSwRZd0LqiyyCermKdbgrB2uPKlC48Z7W_on-lUkUOnlJd62ok3dkmH7JXdQxf63fiy8lTJ7mxggLO5Vjo_p7uKwbi43Dl3GWm8aiiPqcYvOfRpQgKV513yPQakVJ-1w2d86ue27OPY3p8N9H5sr5Mg5oew18KgTO2Ehhj7cSGj-2a6ht7DOZOs" />
                                    <span
                                        class="px-3 py-1 rounded-full bg-stone-100 text-charcoal/60 text-[8px] font-bold uppercase tracking-widest">Philanthropic
                                        Trust</span>
                                </div>
                                <div class="space-y-3">
                                    <h3 class="text-xl font-serif text-deep-green">Legacy Heritage Trust</h3>
                                    <p class="text-charcoal/60 text-sm font-light leading-relaxed">
                                        Providing long-term environmental education grants for students participating in
                                        urban forestry.
                                    </p>
                                </div>
                            </div>
                            <div class="mt-8">
                                <a class="text-gold-accent font-bold text-[9px] uppercase tracking-widest group-hover:text-deep-green transition-colors flex items-center gap-2"
                                    href="#">
                                    View Partner Profile <span
                                        class="material-symbols-outlined text-sm">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-6 lg:col-span-4 group h-full">
                        <div
                            class="glass-sponsor rounded-[2.5rem] p-10 flex flex-col h-full justify-between hover:-translate-y-2 transition-transform duration-500">
                            <div class="space-y-6">
                                <div class="flex justify-between items-start">
                                    <div class="bg-deep-green/5 p-2 rounded-lg">
                                        <span class="material-symbols-outlined text-deep-green text-3xl">landscape</span>
                                    </div>
                                    <span
                                        class="px-3 py-1 rounded-full bg-stone-100 text-charcoal/60 text-[8px] font-bold uppercase tracking-widest">Urban
                                        Alliance</span>
                                </div>
                                <div class="space-y-3">
                                    <h3 class="text-xl font-serif text-deep-green">Metropolitan Green</h3>
                                    <p class="text-charcoal/60 text-sm font-light leading-relaxed">
                                        Implementing vertical greening and micro-forests in the most densely populated
                                        Kabul neighborhoods.
                                    </p>
                                </div>
                            </div>
                            <div class="mt-8">
                                <a class="text-gold-accent font-bold text-[9px] uppercase tracking-widest group-hover:text-deep-green transition-colors flex items-center gap-2"
                                    href="#">
                                    View Partner Profile <span
                                        class="material-symbols-outlined text-sm">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-24 bg-white relative overflow-hidden">
            <div class="negative-space-container">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div class="relative group">
                        <div
                            class="absolute -inset-4 bg-gold-accent/5 rounded-[3rem] -z-10 group-hover:scale-105 transition-transform duration-700">
                        </div>
                        <img alt="The Rahim Family Portrait"
                            class="w-full h-[600px] object-cover rounded-[2.5rem] shadow-2xl grayscale-[0.2] hover:grayscale-0 transition-all duration-700"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDqbsRfkaGON8ghibhqD0FPuvhemuTfvhq_g1lSPz3wYG6q0ViQhZ6BAOn1lWNpqzfmJr0XJbLIN-a7O0EjzNovxSpDbYB4X0qR1gC4IqW8TdOQV_FcmFPAICD1ok8hMNr1-NMIrWGuKXWQfMn3RILiUlG7cXr5g6Vg-21sIzI_9FnD_f9QHbe_IdU71Y4_6JOlRzpP92BALfb2tzoLmwJ9AMpwLqxBdnLJKOIDCKVdYIiqcT_IJ5iOsBHRziCrooDb-fS1XolmyJM" />
                        <div
                            class="absolute bottom-8 right-8 bg-white p-6 rounded-2xl shadow-xl max-w-xs border border-stone-100">
                            <p class="text-deep-green font-serif text-xl italic leading-relaxed">"We plant these trees
                                so that the next generation of Kabul breathes cleaner air."</p>
                            <div class="mt-4 flex items-center gap-2">
                                <span class="w-8 h-px bg-gold-accent"></span>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gold-accent">Featured
                                    Guardian</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-8">
                        <div class="space-y-2">
                            <h4 class="text-gold-accent font-bold tracking-[0.3em] text-[10px] uppercase">Guardian
                                Spotlight</h4>
                            <h2 class="text-5xl font-serif text-deep-green">The Rahim Family</h2>
                        </div>
                        <div class="flex gap-12 border-y border-stone-100 py-8">
                            <div>
                                <p class="text-[10px] font-bold text-charcoal/40 uppercase tracking-widest mb-1">Impact
                                </p>
                                <p class="text-3xl font-serif text-deep-green">1,250 Trees</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-charcoal/40 uppercase tracking-widest mb-1">Member
                                    Since</p>
                                <p class="text-3xl font-serif text-deep-green">2021</p>
                            </div>
                        </div>
                        <p class="text-charcoal/70 leading-relaxed text-lg font-light">
                            The Rahim family has been instrumental in the reforestation of the Paghman foothills. Their
                            commitment extends beyond financial support, having visited the nurseries multiple times to
                            see the growth of their almond and cedar saplings first-hand.
                        </p>
                        <button
                            class="flex items-center gap-4 text-deep-green font-bold text-xs uppercase tracking-widest group">
                            Read Their Story
                            <span
                                class="material-symbols-outlined text-lg group-hover:translate-x-2 transition-transform">arrow_right_alt</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-32 bg-white botanical-bg">
            <div class="negative-space-container">
                <div class="mb-16 flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="space-y-4">
                        <h3 class="text-4xl font-serif text-deep-green">Our Global Community</h3>
                        <p class="text-charcoal/50 font-light max-w-md">Browse our verified list of supporters.
                            Transparency is the root of our initiative.</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-charcoal/30">search</span>
                            <input
                                class="pl-12 pr-6 py-3 bg-stone-50 border-none rounded-full text-sm focus:ring-1 focus:ring-gold-accent/50 w-64"
                                placeholder="Find a guardian..." type="text" />
                        </div>
                    </div>
                </div>
                <div class="w-full overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-stone-100">
                                <th class="py-6 px-4 text-[10px] font-bold text-charcoal/40 uppercase tracking-widest">
                                    Guardian</th>
                                <th class="py-6 px-4 text-[10px] font-bold text-charcoal/40 uppercase tracking-widest">
                                    Impact</th>
                                <th class="py-6 px-4 text-[10px] font-bold text-charcoal/40 uppercase tracking-widest">
                                    Location</th>
                                <th
                                    class="py-6 px-4 text-[10px] font-bold text-charcoal/40 uppercase tracking-widest text-right">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-50">
                            <tr class="donor-row transition-colors cursor-pointer group">
                                <td class="py-6 px-4">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 rounded-full overflow-hidden border-2 border-stone-100 group-hover:border-gold-accent transition-colors">
                                            <img alt="Donor Profile" class="w-full h-full object-cover"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDqbsRfkaGON8ghibhqD0FPuvhemuTfvhq_g1lSPz3wYG6q0ViQhZ6BAOn1lWNpqzfmJr0XJbLIN-a7O0EjzNovxSpDbYB4X0qR1gC4IqW8TdOQV_FcmFPAICD1ok8hMNr1-NMIrWGuKXWQfMn3RILiUlG7cXr5g6Vg-21sIzI_9FnD_f9QHbe_IdU71Y4_6JOlRzpP92BALfb2tzoLmwJ9AMpwLqxBdnLJKOIDCKVdYIiqcT_IJ5iOsBHRziCrooDb-fS1XolmyJM" />
                                        </div>
                                        <span
                                            class="text-deep-green font-serif text-xl group-hover:text-gold-accent transition-colors">Dr.
                                            Aria Mansoor</span>
                                    </div>
                                </td>
                                <td class="py-6 px-4">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sage-green text-sm">park</span>
                                        <span class="text-charcoal font-medium">450 Trees Planted</span>
                                    </div>
                                </td>
                                <td class="py-6 px-4">
                                    <span class="text-charcoal/60 text-sm">Kabul, Afghanistan</span>
                                </td>
                                <td class="py-6 px-4 text-right">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-deep-green/5 rounded-full">
                                        <span class="material-symbols-outlined text-[14px] text-deep-green">verified</span>
                                        <span
                                            class="text-[9px] font-bold text-deep-green uppercase tracking-widest">Verified</span>
                                    </span>
                                </td>
                            </tr>
                            <tr class="donor-row transition-colors cursor-pointer group">
                                <td class="py-6 px-4">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 rounded-full overflow-hidden border-2 border-stone-100 group-hover:border-gold-accent transition-colors">
                                            <img alt="Donor Profile" class="w-full h-full object-cover"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCp83JCfKCRYgHZuZA8dzBJ3Ih5yeidFw9eQGrfMH89CUBjfUnGXPlxXvfb0DmVToP0iwQwPaK0W5XabUIrIKFxbSwRZd0LqiyyCermKdbgrB2uPKlC48Z7W_on-lUkUOnlJd62ok3dkmH7JXdQxf63fiy8lTJ7mxggLO5Vjo_p7uKwbi43Dl3GWm8aiiPqcYvOfRpQgKV513yPQakVJ-1w2d86ue27OPY3p8N9H5sr5Mg5oew18KgTO2Ehhj7cSGj-2a6ht7DOZOs" />
                                        </div>
                                        <span
                                            class="text-deep-green font-serif text-xl group-hover:text-gold-accent transition-colors">Global
                                            Ethos Fund</span>
                                    </div>
                                </td>
                                <td class="py-6 px-4">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sage-green text-sm">park</span>
                                        <span class="text-charcoal font-medium">2,100 Trees Planted</span>
                                    </div>
                                </td>
                                <td class="py-6 px-4">
                                    <span class="text-charcoal/60 text-sm">London, UK</span>
                                </td>
                                <td class="py-6 px-4 text-right">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-deep-green/5 rounded-full">
                                        <span class="material-symbols-outlined text-[14px] text-deep-green">verified</span>
                                        <span
                                            class="text-[9px] font-bold text-deep-green uppercase tracking-widest">Verified</span>
                                    </span>
                                </td>
                            </tr>
                            <tr class="donor-row transition-colors cursor-pointer group">
                                <td class="py-6 px-4">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 rounded-full overflow-hidden border-2 border-stone-100 group-hover:border-gold-accent transition-colors">
                                            <img alt="Donor Profile" class="w-full h-full object-cover"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDqbsRfkaGON8ghibhqD0FPuvhemuTfvhq_g1lSPz3wYG6q0ViQhZ6BAOn1lWNpqzfmJr0XJbLIN-a7O0EjzNovxSpDbYB4X0qR1gC4IqW8TdOQV_FcmFPAICD1ok8hMNr1-NMIrWGuKXWQfMn3RILiUlG7cXr5g6Vg-21sIzI_9FnD_f9QHbe_IdU71Y4_6JOlRzpP92BALfb2tzoLmwJ9AMpwLqxBdnLJKOIDCKVdYIiqcT_IJ5iOsBHRziCrooDb-fS1XolmyJM" />
                                        </div>
                                        <span
                                            class="text-deep-green font-serif text-xl group-hover:text-gold-accent transition-colors">Sara
                                            &amp; Omar Z.</span>
                                    </div>
                                </td>
                                <td class="py-6 px-4">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sage-green text-sm">park</span>
                                        <span class="text-charcoal font-medium">120 Trees Planted</span>
                                    </div>
                                </td>
                                <td class="py-6 px-4">
                                    <span class="text-charcoal/60 text-sm">Dubai, UAE</span>
                                </td>
                                <td class="py-6 px-4 text-right">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-deep-green/5 rounded-full">
                                        <span class="material-symbols-outlined text-[14px] text-deep-green">verified</span>
                                        <span
                                            class="text-[9px] font-bold text-deep-green uppercase tracking-widest">Verified</span>
                                    </span>
                                </td>
                            </tr>
                            <tr class="donor-row transition-colors cursor-pointer group">
                                <td class="py-6 px-4">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 rounded-full overflow-hidden border-2 border-stone-100 group-hover:border-gold-accent transition-colors">
                                            <img alt="Donor Profile" class="w-full h-full object-cover"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCp83JCfKCRYgHZuZA8dzBJ3Ih5yeidFw9eQGrfMH89CUBjfUnGXPlxXvfb0DmVToP0iwQwPaK0W5XabUIrIKFxbSwRZd0LqiyyCermKdbgrB2uPKlC48Z7W_on-lUkUOnlJd62ok3dkmH7JXdQxf63fiy8lTJ7mxggLO5Vjo_p7uKwbi43Dl3GWm8aiiPqcYvOfRpQgKV513yPQakVJ-1w2d86ue27OPY3p8N9H5sr5Mg5oew18KgTO2Ehhj7cSGj-2a6ht7DOZOs" />
                                        </div>
                                        <span
                                            class="text-deep-green font-serif text-xl group-hover:text-gold-accent transition-colors">Summit
                                            Collective</span>
                                    </div>
                                </td>
                                <td class="py-6 px-4">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sage-green text-sm">park</span>
                                        <span class="text-charcoal font-medium">850 Trees Planted</span>
                                    </div>
                                </td>
                                <td class="py-6 px-4">
                                    <span class="text-charcoal/60 text-sm">International</span>
                                </td>
                                <td class="py-6 px-4 text-right">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-deep-green/5 rounded-full">
                                        <span class="material-symbols-outlined text-[14px] text-deep-green">verified</span>
                                        <span
                                            class="text-[9px] font-bold text-deep-green uppercase tracking-widest">Verified</span>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-16 text-center">
                    <button
                        class="px-8 py-4 border border-stone-200 text-charcoal/50 rounded-full text-[10px] font-bold uppercase tracking-widest hover:border-gold-accent hover:text-gold-accent transition-all">
                        Load More Guardians
                    </button>
                </div>
            </div>
        </section>
        <section class="py-24 bg-stone-50">
            <div class="negative-space-container">
                <div class="mb-12 text-center space-y-4">
                    <h2 class="text-3xl font-serif text-deep-green">Transparency in Focus</h2>
                    <p class="text-charcoal/50 text-sm max-w-xl mx-auto">Every donation is a living legacy. Click on
                        any guardian above to see their personalized impact profile.</p>
                </div>
                <div class="glass-modal max-w-4xl mx-auto rounded-[3rem] p-8 md:p-16 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8">
                        <span
                            class="material-symbols-outlined text-charcoal/20 cursor-pointer hover:text-charcoal/50">close</span>
                    </div>
                    <div class="flex flex-col md:flex-row gap-12">
                        <div class="w-full md:w-1/3 space-y-6">
                            <div class="w-full aspect-square rounded-2xl overflow-hidden shadow-lg border border-white/40">
                                <img alt="Donor's Specific Trees" class="w-full h-full object-cover"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCp83JCfKCRYgHZuZA8dzBJ3Ih5yeidFw9eQGrfMH89CUBjfUnGXPlxXvfb0DmVToP0iwQwPaK0W5XabUIrIKFxbSwRZd0LqiyyCermKdbgrB2uPKlC48Z7W_on-lUkUOnlJd62ok3dkmH7JXdQxf63fiy8lTJ7mxggLO5Vjo_p7uKwbi43Dl3GWm8aiiPqcYvOfRpQgKV513yPQakVJ-1w2d86ue27OPY3p8N9H5sr5Mg5oew18KgTO2Ehhj7cSGj-2a6ht7DOZOs" />
                            </div>
                            <div class="space-y-2">
                                <p class="text-[9px] font-bold text-gold-accent uppercase tracking-widest">Location Tag
                                </p>
                                <p class="text-xs text-charcoal/60">Lot 402 - Kabul West Hills. Native Almond Orchard.
                                </p>
                            </div>
                        </div>
                        <div class="w-full md:w-2/3 space-y-8">
                            <div>
                                <h3 class="text-3xl font-serif text-deep-green mb-2">Thank You, Aria.</h3>
                                <p class="text-charcoal/70 italic text-sm leading-relaxed">"The trees you've planted
                                    are already showing blossoms. Your contribution of 450 saplings has provided work
                                    for three local families this season."</p>
                            </div>
                            <div class="grid grid-cols-2 gap-8">
                                <div class="p-4 bg-white/40 rounded-xl border border-white/60">
                                    <p class="text-[9px] font-bold text-charcoal/40 uppercase tracking-widest mb-1">CO2
                                        Offset</p>
                                    <p class="text-xl font-serif text-deep-green">12.5 Tons/yr</p>
                                </div>
                                <div class="p-4 bg-white/40 rounded-xl border border-white/60">
                                    <p class="text-[9px] font-bold text-charcoal/40 uppercase tracking-widest mb-1">
                                        Sapling Type</p>
                                    <p class="text-xl font-serif text-deep-green">Native Pine</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 pt-4">
                                <button
                                    class="px-6 py-3 bg-deep-green text-white rounded-full text-[9px] font-bold uppercase tracking-widest hover:opacity-90 transition-opacity">Download
                                    Impact Receipt</button>
                                <button
                                    class="px-6 py-3 border border-deep-green/20 text-deep-green rounded-full text-[9px] font-bold uppercase tracking-widest hover:bg-deep-green/5 transition-colors">Share
                                    Impact</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-32 px-6">
            <div
                class="max-w-5xl mx-auto glass-panel rounded-[3rem] p-12 md:p-24 text-center space-y-10 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-tr from-gold-accent/10 to-transparent -z-10"></div>
                <div class="space-y-6">
                    <h2 class="text-5xl md:text-6xl font-serif text-deep-green leading-tight">Write Your Name
                        <br /><span class="italic font-light text-gold-accent">in Kabul's Earth.</span>
                    </h2>
                    <p class="text-charcoal/60 text-lg md:text-xl font-light max-w-2xl mx-auto leading-relaxed">
                        Your contribution directly funds seedlings, irrigation, and local wages in Kabul's urban and
                        rural fringes. Join our community of forest guardians today.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                    <button
                        class="w-full sm:w-auto px-12 py-5 bg-deep-green text-white rounded-full font-bold text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-deep-green/20 hover:scale-105 transition-transform">
                        Donate &amp; Join the Wall
                    </button>
                    <button
                        class="w-full sm:w-auto px-12 py-5 border border-gold-accent text-deep-green rounded-full font-bold text-[10px] uppercase tracking-[0.2em] hover:bg-gold-accent/5 transition-colors">
                        Gift a Tree
                    </button>
                </div>
                <div class="flex items-center justify-center gap-8 pt-8">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-gold-accent text-xl">shield_with_heart</span>
                        <span class="text-[9px] font-bold text-charcoal/40 uppercase tracking-[0.15em]">Secure &amp;
                            Verified</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-gold-accent text-xl">receipt_long</span>
                        <span class="text-[9px] font-bold text-charcoal/40 uppercase tracking-[0.15em]">Tax
                            Deductible</span>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
