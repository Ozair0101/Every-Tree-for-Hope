@extends('layouts.layout')

@section('content')
    <div class="relative min-h-screen w-full flex flex-col">
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 right-[-5%] text-sage-green/5">
                <span class="material-symbols-outlined text-[30rem] select-none rotate-12">spa</span>
            </div>
            <span class="material-symbols-outlined leaf-float top-1/4 left-1/4 text-4xl">energy_savings_leaf</span>
            <span class="material-symbols-outlined leaf-float top-1/2 right-1/3 text-2xl">eco</span>
        </div>
        <main class="relative z-10 flex-1 flex flex-col items-center">
            <section
                class="w-full max-w-7xl mx-auto px-6 md:px-12 lg:px-24 pt-16 pb-24 flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                <div class="w-full lg:w-5/12 text-left z-10">
                    <h4 class="text-sage-green font-bold tracking-[0.4em] text-xs uppercase mb-6">Connect with Kabul
                    </h4>
                    <h1 class="text-6xl md:text-7xl lg:text-8xl font-serif text-charcoal leading-[1.1] mb-8">
                        Let's Grow <br />
                        <span class="italic font-normal text-deep-green">Together</span>
                    </h1>
                    <p class="text-charcoal/70 text-lg max-w-md font-light leading-relaxed">
                        Join us in our mission to reforest the rugged hills of Kabul. Every connection planted today
                        blossoms into a greener future for Afghanistan.
                    </p>
                    <div class="w-24 h-[1px] bg-gold-accent mt-12 opacity-60"></div>
                </div>
                <div class="w-full lg:w-7/12 flex justify-center lg:justify-end">
                    <div class="relative w-full aspect-[4/3] hill-organic-mask overflow-hidden shadow-2xl">
                        <img alt="The rugged, sun-drenched hills surrounding Kabul with new saplings"
                            class="object-cover w-full h-full scale-105 hover:scale-100 transition-transform duration-1000"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuD_luKpgfJE5RBkIsl2Pwvvw1vosV0FwGQz6yMz8Tv4Wy3oSshuZ7Fk1emjcLpX3bAuSoWFz1vp3oUViwxaCaixQ-I7gnDRXuj9BKPBERYX9raiF4EfaxNlll6ro31_AjLgIULYrtJ3MbOFHB9aJE6p0MciGyWg4MllujEZ1yiCkKXVkKgw8M0-m2sLOH3_5E--tqECpazCENmYWAGTLmKuEzOfn8A0QMNguP0TtGtQwVf-hSuqBbpV0xKVRU2xS93GvipFF5BGmFo" />
                        <div
                            class="absolute inset-0 bg-gradient-to-tr from-deep-green/10 to-transparent mix-blend-multiply">
                        </div>
                    </div>
                </div>
            </section>
            <section class="w-full max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 px-6 md:px-12 lg:px-24 mb-24">
                <div
                    class="glass-panel p-10 rounded-3xl text-center group hover:-translate-y-2 transition-transform duration-500">
                    <div
                        class="w-16 h-16 bg-off-white border border-gold-accent/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-gold-accent text-3xl">location_on</span>
                    </div>
                    <h3 class="font-serif text-xl text-deep-green mb-3">Visit Us</h3>
                    <p class="text-charcoal/70 text-sm leading-relaxed">
                        Kabul, Afghanistan<br />
                        Haidari Market, District 4
                    </p>
                    <p class="text-[10px] font-bold text-sage-green uppercase tracking-widest mt-4">By Appointment Only
                    </p>
                </div>
                <div
                    class="glass-panel p-10 rounded-3xl text-center group hover:-translate-y-2 transition-transform duration-500">
                    <div
                        class="w-16 h-16 bg-off-white border border-gold-accent/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-gold-accent text-3xl">chat</span>
                    </div>
                    <h3 class="font-serif text-xl text-deep-green mb-3">Call Us</h3>
                    <a class="text-charcoal/70 text-sm hover:text-deep-green transition-colors"
                        href="https://wa.me/93749290591">
                        WhatsApp: +93 749 290 591
                    </a>
                    <p class="text-[10px] font-bold text-sage-green uppercase tracking-widest mt-4">Available Sat-Thu
                    </p>
                </div>
                <div
                    class="glass-panel p-10 rounded-3xl text-center group hover:-translate-y-2 transition-transform duration-500">
                    <div
                        class="w-16 h-16 bg-off-white border border-gold-accent/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-gold-accent text-3xl">mail</span>
                    </div>
                    <h3 class="font-serif text-xl text-deep-green mb-3">Write Us</h3>
                    <a class="text-charcoal/70 text-sm hover:text-deep-green transition-colors"
                        href="mailto:tamimalim209@gmail.com">
                        tamimalim209@gmail.com
                    </a>
                    <p class="text-[10px] font-bold text-sage-green uppercase tracking-widest mt-4">Typical Response:
                        24h</p>
                </div>
            </section>
            <section class="w-full max-w-5xl mx-auto px-6 mb-32">
                <div class="glass-panel p-12 md:p-20 rounded-[3rem] relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 opacity-5 pointer-events-none botanical-mask">
                        <img alt="nature" class="object-cover w-full h-full"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCp83JCfKCRYgHZuZA8dzBJ3Ih5yeidFw9eQGrfMH89CUBjfUnGXPlxXvfb0DmVToP0iwQwPaK0W5XabUIrIKFxbSwRZd0LqiyyCermKdbgrB2uPKlC48Z7W_on-lUkUOnlJd62ok3dkmH7JXdQxf63fiy8lTJ7mxggLO5Vjo_p7uKwbi43Dl3GWm8aiiPqcYvOfRpQgKV513yPQakVJ-1w2d86ue27OPY3p8N9H5sr5Mg5oew18KgTO2Ehhj7cSGj-2a6ht7DOZOs" />
                    </div>
                    <div class="max-w-2xl relative z-10">
                        <h2 class="font-serif text-4xl text-deep-green mb-10">Send a Message</h2>
                        <form class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <input
                                        class="w-full bg-white/60 backdrop-blur-sm border border-gray-300 rounded-xl px-4 py-3 focus:border-deep-green focus:ring-2 focus:ring-deep-green/20 transition-all text-charcoal placeholder:text-charcoal/40"
                                        placeholder="Your Name" type="text" />
                                </div>
                                <div>
                                    <input
                                        class="w-full bg-white/60 backdrop-blur-sm border border-gray-300 rounded-xl px-4 py-3 focus:border-deep-green focus:ring-2 focus:ring-deep-green/20 transition-all text-charcoal placeholder:text-charcoal/40"
                                        placeholder="Email Address" type="email" />
                                </div>
                            </div>
                            <div>
                                <input
                                    class="w-full bg-white/60 backdrop-blur-sm border border-gray-300 rounded-xl px-4 py-3 focus:border-deep-green focus:ring-2 focus:ring-deep-green/20 transition-all text-charcoal placeholder:text-charcoal/40"
                                    placeholder="Subject" type="text" />
                            </div>
                            <div>
                                <textarea
                                    class="w-full bg-white/60 backdrop-blur-sm border border-gray-300 rounded-xl px-4 py-3 focus:border-deep-green focus:ring-2 focus:ring-deep-green/20 transition-all text-charcoal placeholder:text-charcoal/40 resize-none"
                                    placeholder="How can we help you plant the future?" rows="4"></textarea>
                            </div>
                            <div class="pt-4">
                                <button
                                    class="bg-deep-green text-white px-8 py-3 rounded-xl font-semibold text-sm hover:bg-deep-green/90 transition-all shadow-lg shadow-deep-green/20 flex items-center gap-2"
                                    type="submit">
                                    Send Message
                                    <span class="material-symbols-outlined">arrow_right_alt</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
            <section class="w-full py-16 bg-white border-y border-charcoal/5">
                <div class="max-w-7xl mx-auto px-6 text-center">
                    <p class="text-[10px] font-bold text-sage-green uppercase tracking-[0.3em] mb-10">Follow the
                        Journey</p>
                    <div class="flex justify-center items-center gap-10 md:gap-16">
                        <a class="group flex flex-col items-center gap-3" href="#">
                            <div
                                class="w-12 h-12 flex items-center justify-center rounded-full bg-off-white shadow-sm group-hover:bg-deep-green group-hover:text-white transition-all text-sage-green">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path
                                        d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z">
                                    </path>
                                </svg>
                            </div>
                            <span
                                class="text-[10px] font-bold text-charcoal/40 uppercase group-hover:text-deep-green">Facebook</span>
                        </a>
                        <a class="group flex flex-col items-center gap-3" href="#">
                            <div
                                class="w-12 h-12 flex items-center justify-center rounded-full bg-off-white shadow-sm group-hover:bg-deep-green group-hover:text-white transition-all text-sage-green">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path
                                        d="M12.525.023C13.29 0 14.056 0 14.822.023c.067.85-.24 1.699-.867 2.302.751.421 1.581.65 2.442.677.03-.783-.242-1.556-.767-2.14.75.199 1.467.507 2.115.91-.137 2.133-1.406 3.993-3.322 4.792.052 1.854.197 3.693.435 5.522.083.642.04 1.294-.127 1.917-.738 2.39-3.006 4.145-5.503 4.264-2.174.103-4.32-.774-5.832-2.327-.14-.143-.243-.314-.303-.502-.173-.54-.26-1.103-.26-1.67 0-2.35 1.906-4.256 4.256-4.256.43 0 .85.065 1.246.186-.16-1.127-.235-2.261-.223-3.396-.922.451-1.936.685-2.964.685-1.934 0-3.665-1.11-4.463-2.85-.304-.663-.456-1.38-.445-2.106.01-1.458.835-2.77 2.132-3.396 1.05-.506 2.233-.506 3.282 0 1.297.626 2.122 1.938 2.132 3.396.011.726-.14 1.443-.445 2.106-.21.458-.514.863-.895 1.192.156 1.134.316 2.268.475 3.402.305-.112.63-.168.96-.168 1.428 0 2.585 1.157 2.585 2.585 0 .33-.062.65-.183.948.118.04.24.072.363.093 1.267.22 2.528-.48 3.033-1.688.24-.572.336-1.193.282-1.812-.224-1.748-.36-3.5-.407-5.257z">
                                    </path>
                                </svg>
                            </div>
                            <span
                                class="text-[10px] font-bold text-charcoal/40 uppercase group-hover:text-deep-green">TikTok</span>
                        </a>
                        <a class="group flex flex-col items-center gap-3" href="#">
                            <div
                                class="w-12 h-12 flex items-center justify-center rounded-full bg-off-white shadow-sm group-hover:bg-deep-green group-hover:text-white transition-all text-sage-green">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path
                                        d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z">
                                    </path>
                                </svg>
                            </div>
                            <span
                                class="text-[10px] font-bold text-charcoal/40 uppercase group-hover:text-deep-green">YouTube</span>
                        </a>
                        <a class="group flex flex-col items-center gap-3" href="#">
                            <div
                                class="w-12 h-12 flex items-center justify-center rounded-full bg-off-white shadow-sm group-hover:bg-deep-green group-hover:text-white transition-all text-sage-green">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z">
                                    </path>
                                </svg>
                            </div>
                            <span
                                class="text-[10px] font-bold text-charcoal/40 uppercase group-hover:text-deep-green">Instagram</span>
                        </a>
                    </div>
                </div>
            </section>
        </main>
    </div>
@endsection
