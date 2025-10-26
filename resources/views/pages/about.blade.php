@extends('layouts.layout')
@section('title', 'About Page')
@section('content')
    <main class="flex-grow">
        <section class="py-16 sm:py-24">
            <div class="container mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-neutral-900 dark:text-white sm:text-4xl">About
                        Us</h2>
                    <p class="mx-auto mt-4 max-w-3xl text-lg text-neutral-600 dark:text-neutral-400">
                        Every Tree for Hope is dedicated to fostering a greener, healthier planet by empowering
                        individuals and communities to plant trees, participate in local environmental events, and
                        adopt sustainable practices.
                    </p>
                </div>
                <div class="mt-16 space-y-16">
                    <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:items-start">
                        <div>
                            <h3 class="text-2xl font-bold text-neutral-900 dark:text-white">Our Mission</h3>
                            <p class="mt-4 text-neutral-600 dark:text-neutral-400">
                                Our mission is to inspire and mobilize people to take action against climate change
                                through tree planting and community engagement. We believe that every tree planted
                                contributes to a healthier environment and a more sustainable future.
                            </p>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-neutral-900 dark:text-white">Our Story</h3>
                            <p class="mt-4 text-neutral-600 dark:text-neutral-400">
                                Founded in 2015 by environmental advocate, Sarah Green, Every Tree for Hope began as
                                a small initiative to plant trees in urban areas. Over the years, it has grown into
                                a global movement, engaging thousands of volunteers and partners in tree planting
                                and environmental education programs.
                            </p>
                        </div>
                    </div>
                    <div>
                        <h3 class="mb-8 text-center text-2xl font-bold text-neutral-900 dark:text-white">Our Team
                        </h3>
                        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="rounded-lg bg-white dark:bg-neutral-800/50 p-6 text-center shadow-sm">
                                <div class="mx-auto h-24 w-24 rounded-full bg-cover bg-center"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuD2k2EJBIvn9wHydH9NNp0n-cY7iu11rhA-lEJt208CpvvdCef7m6QTchjBIttF3VWqUogynHz3cK8kqDYArb3wOJp2H4hokk2NswnFGq7r3YzWqvwNJQYbic3LN3kOc_r0RJKidJoiCru6neWT4TPTRYIM2xvbTw4AWS7X13Pmgu-yzG2UOZIVbxjH2npxEf8euBsAcBjAijAoRGuz59eXa3mUTg-6QYk5JTIVV_ghzh2fTvxuQec2lnuZ6JcUOHw70bI_HixTp-E");'>
                                </div>
                                <h4 class="mt-4 text-lg font-bold text-neutral-900 dark:text-white">Sarah Green</h4>
                                <p class="text-sm text-primary">Founder &amp; CEO</p>
                            </div>
                            <div class="rounded-lg bg-white dark:bg-neutral-800/50 p-6 text-center shadow-sm">
                                <div class="mx-auto h-24 w-24 rounded-full bg-cover bg-center"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAuy2inY73pULVdRQuWPR1qwweZ_ygLgz_bBYGXcMcZkF3bkYFCQ9b2b2Q88ZnZ7npvcqjjYbgIZAR50vA_HiF3UycskY3eR7zyxr7xZMO0ZCcsoU-58QjjHkyuBGb-TBIzaNvWqiAFbft65mQYXTYYdFsBkU3JwXa3CI7kVtNP2FoSdcHmZopgdbJGACDMb-pMIrtUB92UQH8UQhhMqf88t0_oJFXwrU9pGfWcZ9VC28IfFxsaaX9pCOeb2Ay5ht9mpMTJDGVwisU");'>
                                </div>
                                <h4 class="mt-4 text-lg font-bold text-neutral-900 dark:text-white">David Brown</h4>
                                <p class="text-sm text-primary">Director of Operations</p>
                            </div>
                            <div class="rounded-lg bg-white dark:bg-neutral-800/50 p-6 text-center shadow-sm">
                                <div class="mx-auto h-24 w-24 rounded-full bg-cover bg-center"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAr-0XiCj2WlgYPisqoY62oxROS9LjSba_heSj8eDxGQUIsd5IznQwJFUq5IvaX6ipgTCdqRvy-dq43dBfsmHHcRfFtiRhlcQT_npbmjK-WmLOMSpoRFomeHxX1XLN-ln6ZSbo-0uC5-2v89VBCyyMREtb4jFbcgUDUQlFwJYj2B5LCbbBOX4XOi63AoLPyxDG7ZTMBFHmyzPlKfEEklF7lBwS2Tag5ZaXoPmqWXO4JEA__z10tGz2aNTu1xd1P-3wr8nr4hkiGNTo");'>
                                </div>
                                <h4 class="mt-4 text-lg font-bold text-neutral-900 dark:text-white">Emily White</h4>
                                <p class="text-sm text-primary">Community Engagement Manager</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="mb-8 text-center text-2xl font-bold text-neutral-900 dark:text-white">Our
                            Partners</h3>
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                            <div
                                class="flex items-center justify-center rounded-lg bg-white dark:bg-neutral-800/50 p-6 shadow-sm">
                                <div class="h-20 w-full rounded-lg bg-cover bg-center"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBvbVoSKu5FifhIsW8fg4eSUa_m5nvNcRakbue1WmncrJ9Tkw1vTKtB4NpQbf_kVBHpD1e8N_bL9eK70TmQdp8Wlu-OuWRDdXJBsb2IxHgttJvZwRuBxK09aAv6HxJ12cnVBFs0T7eHZ0WEYUrmGirl0L_IwnUFMdYrq1HslGfB2LovDmx8HqCEePhK5vINGBvvRszVrROhPXGHkwPCwAjMesUvWwyTyAF_aMmaCISDx9xTWG8SI6Ox5JLOS4-ihL8hGEDgo6NOCTw"); background-size: contain; background-repeat: no-repeat;'>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-center rounded-lg bg-white dark:bg-neutral-800/50 p-6 shadow-sm">
                                <div class="h-20 w-full rounded-lg bg-cover bg-center"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBrWhQDAZOddnpP1CCxdxBsVsW5jJcmr-uBCTAlVEw4xsSEtlcOjccec3M8N88tMXpuq7Vcnbwj5UQ5l3wZNDCiL9YHhldU0TpyijUWCeiiwgvg-lnFwn1GyUWg9SOQPsS-OtMXmPp88iXbl92v4D6DcjxrQmB98yy6aUPywEc73dtHq-3d9qNJ96K0RnuAcs_mbaQlnqLK5gr7gM0I9qwPwDsycE8LRXJjOnivnP87ZzvcmZZFMtTU8r68TDhL-PR2w0Msx25-RIs"); background-size: contain; background-repeat: no-repeat;'>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-center rounded-lg bg-white dark:bg-neutral-800/50 p-6 shadow-sm">
                                <div class="h-20 w-full rounded-lg bg-cover bg-center"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBBb8kQzArS9YCy57yDmI1754EOUi255PC4i3-gT17JuujdJvujMJKbFdPsXUH9bSaPrZoaZ5VRDhXobjFLRhPYdUOj1NVtCC7ewP6sInctLxwn1oHFpWgA8-d6MaIFtuv2jm6oWUoKfVo_qx_N1MRI5wmASTGuBtCSJbD_Z6NIcHJiDhsuV1czfo1SrIu2l3DmRfikvBQz_5WQlpDGas-Tc30M9O93PsimyV19VV_o-mxN_B9yTBqsSGg7qlFcvxvqWt4doBJrdL8"); background-size: contain; background-repeat: no-repeat;'>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-center rounded-lg bg-white dark:bg-neutral-800/50 p-6 shadow-sm">
                                <div class="h-20 w-full rounded-lg bg-cover bg-center"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuB2ugOfv3xiHaTnFCUQ9cXZj0nlFUXWKKDp7I8l3MWpHiiEeqLat-YXkNjYMHMyi90UXPXo_uafFZzXkB6AOoqn7SqQs6ueOmQni9hDZimnE7BwHm3dxciRpwjbGfMuCE3enyCzLbCV7sV8CNkgzOrTqOh9HE0N0VCcCx4ePGW2Ch0E4ZORXdKSirg6DeLqU7OULCpr6r3FBtupuN4XLtoBUCTTT-7YHXOLEpzc15BLACg3beeUlGn-mheI5kZQyxxYztgmgLkXgzc"); background-size: contain; background-repeat: no-repeat;'>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
