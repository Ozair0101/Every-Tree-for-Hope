<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Main Gallery - Every Tree for Hope</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#19b357",
                        "background-light": "#f6f8f7",
                        "background-dark": "#112118",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-200">
    <div class="relative flex min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">
            <div class="px-4 sm:px-8 md:px-16 lg:px-24 xl:px-40 flex flex-1 justify-center py-5">
                <div class="layout-content-container flex flex-col w-full max-w-6xl flex-1">
                    <header
                        class="flex items-center justify-between whitespace-nowrap border-b border-solid border-gray-200 dark:border-gray-700 px-4 sm:px-6 lg:px-10 py-3">
                        <div class="flex items-center gap-4 text-gray-900 dark:text-white">
                            <div class="size-6 text-primary">
                                <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z"
                                        fill="currentColor"></path>
                                </svg>
                            </div>
                            <h2 class="text-lg font-bold leading-tight tracking-[-0.015em]">Every Tree for Hope</h2>
                        </div>
                        <nav class="hidden lg:flex flex-1 justify-end gap-8">
                            <div class="flex items-center gap-9">
                                <a class="text-sm font-medium leading-normal text-gray-800 dark:text-gray-200 hover:text-primary dark:hover:text-primary"
                                    href="#">Home</a>
                                <a class="text-sm font-medium leading-normal text-gray-800 dark:text-gray-200 hover:text-primary dark:hover:text-primary"
                                    href="#">About</a>
                                <a class="text-sm font-medium leading-normal text-gray-800 dark:text-gray-200 hover:text-primary dark:hover:text-primary"
                                    href="#">Events</a>
                                <a class="text-sm font-medium leading-normal text-gray-800 dark:text-gray-200 hover:text-primary dark:hover:text-primary"
                                    href="#">Donate</a>
                                <a class="text-sm font-bold leading-normal text-primary dark:text-primary"
                                    href="#">Gallery</a>
                            </div>
                            <button
                                class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors">
                                <span class="truncate">Join Us</span>
                            </button>
                        </nav>
                        <div class="lg:hidden">
                            <button
                                class="p-2 rounded-md text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800">
                                <span class="material-symbols-outlined">menu</span>
                            </button>
                        </div>
                    </header>
                    <main class="flex flex-col gap-8 md:gap-12 mt-8">
                        <div class="@container">
                            <div class="@[480px]:p-4">
                                <div class="flex min-h-[480px] flex-col gap-6 bg-cover bg-center bg-no-repeat @[480px]:gap-8 @[480px]:rounded-xl items-center justify-center p-4 text-center"
                                    data-alt="A lush green forest canopy seen from above, with sunlight filtering through the leaves."
                                    style='background-image: linear-gradient(rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.5) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuBbG2GR81PX9-kr4HtIOdrEFk9ppOXpUX9Pfw71kY62NrEO0cuzJn8KcpHeGa8jA8sdxHP6wCNZAo99oKpaOdSzLszRshLWa2pQXySn-TsZ8-cGh717JB2DZ0EMgH3VKdI06B2FO-ghKY0Z9FHxTq3Y4n9XV68qyh2grRINxm-MomkbMWAouSrTPw1SuwiDnOaumkuYxia3UaJjgD3tpr5WV-vHighNgJhxZcuqIw_RlvOQX1QiOIHqryJrpG3Z3bN-2JiImx40-7k");'>
                                    <div class="flex flex-col gap-2">
                                        <h1
                                            class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl">
                                            Our Forest of Hope: A Visual Journey</h1>
                                        <h2
                                            class="text-white text-sm font-normal leading-normal @[480px]:text-base max-w-xl mx-auto">
                                            Explore the stories of growth, community, and change captured through our
                                            work.</h2>
                                    </div>
                                    <button
                                        class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 @[480px]:h-12 @[480px]:px-5 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] @[480px]:text-base hover:bg-primary/90 transition-colors">
                                        <span class="truncate">Explore Gallery</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2 sm:gap-3 p-3 overflow-x-auto whitespace-nowrap justify-center">
                            <button
                                class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-full bg-primary/20 dark:bg-primary/30 px-4 text-primary dark:text-primary text-sm font-bold leading-normal">All</button>
                            <button
                                class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-full bg-gray-100 dark:bg-gray-800 px-4 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-medium leading-normal">Planting
                                Events</button>
                            <button
                                class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-full bg-gray-100 dark:bg-gray-800 px-4 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-medium leading-normal">Community
                                Stories</button>
                            <button
                                class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-full bg-gray-100 dark:bg-gray-800 px-4 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-medium leading-normal">Our
                                Impact</button>
                            <button
                                class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-full bg-gray-100 dark:bg-gray-800 px-4 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-medium leading-normal">Before
                                &amp; After</button>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 p-4">
                            <div class="bg-cover bg-center flex flex-col gap-3 rounded-lg justify-end p-4 aspect-[3/4] group cursor-pointer"
                                data-alt="Volunteers planting trees in a sunny park."
                                style='background-image: linear-gradient(0deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuA3ZCQ5HfiHNOwfyjhdC7Kh2R6cdevVS-Z0usprIS96jOeh_bEEw1g4NZk9CkF9dtfV4NLayjBeb1DEt-0kNqO6x7oUtHghRE8ako1yB5OLsC0SxLdDa0uEnB0pcm0WzrKjuZNwxSRfZkWohpwtn2nXAniZB_5ttbLn6MaKMcgLeUw28W6yDyrXNQf8wLcEItok2UklLF2ll10SNhNpu44LOYTT1ncC_v4dmfzo-CL3R8j-iMTlfabQe5zktWm_jxGEYiWMNzB4pbs");'>
                                <p
                                    class="text-white text-base font-bold leading-tight line-clamp-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    Community Tree Planting in Springfield Park</p>
                            </div>
                            <div class="bg-cover bg-center flex flex-col gap-3 rounded-lg justify-end p-4 aspect-[3/4] group cursor-pointer"
                                data-alt="A dry, barren field under a cloudy sky."
                                style='background-image: linear-gradient(0deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuClh-jT1xBNJVLjPVTrJRdKAFQ07-49cmqfa-VnCGWxCJ8-VP0cAvjqwHKxtrDfIayxIjVebShbc6TxPhnOzdrrwfAfAR4_UUFZmBQapq65wTtwBH6cV9iw2CLr3oHG08KJNPSZSBJWxbvPZOFmekx7cwR_7O8BF5TwplHpCxiNgWW3OB6ysT43Pz5Y8D939dyWBTq5EE0MMRNi_m_QWQGV_PE3CoounAYuCY9CpY1kKh55AOB1209xIX6K3mN6rI1VBmomMf1_c4c");'>
                                <p
                                    class="text-white text-base font-bold leading-tight line-clamp-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    Before: Barren field in Oakhaven</p>
                            </div>
                            <div class="bg-cover bg-center flex flex-col gap-3 rounded-lg justify-end p-4 aspect-[3/4] group cursor-pointer"
                                data-alt="The same field now filled with young, thriving trees."
                                style='background-image: linear-gradient(0deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuAXR4YsouY6TabNr3jKCd4YBEumfp59PMZBm3efl4L2R4gCyR2z7x9PNtgGI2OYW19s-C9t8_MprtmUV8Vf_k2UhXo9h2kPOcCkagP1EmR5XHQy6njxlj7nbLUwBpgS4kmxix4eLh26o2owTqISskl78gza2AMgu0Nax5Om3yFJUSy3-5jluOI2_gsKfPZpVt-cELVejQOc61UL2RrbTLHsFAIW4n_l4OB7wVUR1dDop4a1zBgzaWR9Gn_RucXZ3hlb5aHrpl_MRbk");'>
                                <p
                                    class="text-white text-base font-bold leading-tight line-clamp-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    After: A thriving young forest in Oakhaven</p>
                            </div>
                            <div class="bg-cover bg-center flex flex-col gap-3 rounded-lg justify-end p-4 aspect-[3/4] group cursor-pointer"
                                data-alt="A group of students gathered around a tree, listening to a guide."
                                style='background-image: linear-gradient(0deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuCxROM_BhWXtJeeu1aU-wkiA1OtOTCUyNQpHg66xlCeuE7p5awJ28Mc4R5hxZHxpokESgUFOUwyqzR2wpQICedTEXWGXH9muoSFpan9QUfktbVs61Agk9tNALJKoCWlh7E05TBS1f_xFJUSM5mMk7NW__85cUP03Yjkk8BW2WCPLDREYcBvbMfdj6ZlKd5bP-XN4Et6JXWAdEFt6bfk1yfG3ZdVnloZBv_DyVOaRsPiQEPvXsTS6pXNF9BnZDwCF3FKgjUJxfLL2jU");'>
                                <p
                                    class="text-white text-base font-bold leading-tight line-clamp-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    Students learning about local ecosystems</p>
                            </div>
                            <div class="bg-cover bg-center flex flex-col gap-3 rounded-lg justify-end p-4 aspect-[3/4] group cursor-pointer"
                                data-alt="A smiling group of volunteers posing with their shovels."
                                style='background-image: linear-gradient(0deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuCIOyu9_d5VugjgWTyGcT1xwFJfby7pTnFO-iFexi5gwCM-hceOJjiT48tgH-QN69mz7AN8aWzPKpAwKTcmVpaYUZvMHfdTOBgziHB6Q8ry6E235RfeQO6Arff3iyopdOTVNNqYiBpqgrsUfZhaQ4bvAFEQOjHghcSNZ58u-KvESSONElNMS3BHXWAysmBCyyfnq9Eevp2uW9md2tgJJJI-qv_9Y-uSWQnJerexLh5lMHM5W-UQjgGS4Glo3GT4gG01sM8P067mCP4");'>
                                <p
                                    class="text-white text-base font-bold leading-tight line-clamp-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    Volunteers celebrating a successful planting day</p>
                            </div>
                            <div class="bg-cover bg-center flex flex-col gap-3 rounded-lg justify-end p-4 aspect-[3/4] group cursor-pointer relative"
                                data-alt="A short video clip of people planting a tree."
                                style='background-image: linear-gradient(0deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuACYxMZdBdP8mR-X8-nCMD_tMIDCeuKIdBazJ5G432AFQ3EGDiR6Em4PoBDppaS_4GaXz0FotPxPMJ4eM-8RL0TCOQYL2wmuqm601zwTE0GJY9E1hcV2HabEH3Vgx-aPwdHz-mZ30vvyApvUTvXUoZk4UzfH8IYawarXRBOW7aOSK95fyBxwKNQy5i_3tOJd-PN1_LJK0bgtOd47XqHXmKUgqLNhqjAC1sV9z8XvlXKX_BzEX2RZnXkACAw8TaG14MgQOQ3Ba2YiOg");'>
                                <span
                                    class="material-symbols-outlined text-white absolute top-4 right-4">play_circle</span>
                                <p
                                    class="text-white text-base font-bold leading-tight line-clamp-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    Community event in action</p>
                            </div>
                            <div class="bg-cover bg-center flex flex-col gap-3 rounded-lg justify-end p-4 aspect-[3/4] group cursor-pointer"
                                data-alt="A small bird perched on the branch of a new sapling."
                                style='background-image: linear-gradient(0deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuAhqA_Mkb9lMrlp0VDR5FvjtHCVSwhba12Of5Eeh_wCTm4w5dc6NxlfjgScBg9iR28895e8tnaAhKg1hROrXOnYewEhmXbVThZCEsLSXB5FgDWG4PRi2FszpLIMOgwPWI6mW7JWibvjCedzx-bhho2w_5kJMjYT4G5w19YQ9BWnEx1AF71bRkXc1eU6vEKTikMJ8ym0ldQlV4Rkf2xcQGaB57oYu68OBNwnYDSd7_3sW8ziQRTYdTpvR15sy-8evMGphd8nYsu2RUo");'>
                                <p
                                    class="text-white text-base font-bold leading-tight line-clamp-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    A bird returns to a newly planted tree</p>
                            </div>
                            <div class="bg-cover bg-center flex flex-col gap-3 rounded-lg justify-end p-4 aspect-[3/4] group cursor-pointer"
                                data-alt="A family, parents and child, planting a small tree together."
                                style='background-image: linear-gradient(0deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuAIZMJsyd84CrL9wysJgItJqWEc9jsV81RSV5C4jt7P92P1Qa26LpZz5vk_xTFdGxSJMO3z5sXpqEoRvr9GdWplA95z8gliH1YkO3AQu9NfRF8zrq8jyfY0Ibj1Cti-oiU-Y5yyUyVwVx3SFHerW-VyImUp0RDkHlqmcwj_FN1S5PWSaB_6FkGNsHeHjJ7bfR_NfzZW2NlbJ5o4WTzzr3FQLy0vlEw45fyS9yCiKI8kgZbwmDvDeKt1b9kZvoLxepsjnzloTtms5-Y");'>
                                <p
                                    class="text-white text-base font-bold leading-tight line-clamp-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    A family plants a memorial tree</p>
                            </div>
                            <div class="bg-cover bg-center flex flex-col gap-3 rounded-lg justify-end p-4 aspect-[3/4] group cursor-pointer"
                                data-alt="A row of small saplings in pots, ready to be planted."
                                style='background-image: linear-gradient(0deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuDT92D-9PsFwXIrVTrTeEyX7worSgf6inux44fgDrfPWQacli3yCkxNxtrhp2mI31UO0N-5WeoxHNvkUwb1t4-IfU28EGGR9MMUhAIVQ89HXbU4YHXJIuaQ4MFi8cF-JH8lchlaTb_4xUfZpIz7eAprfd3w86s9zkT28zgXTBAf45NN7mS44xn3bQrxZI7L6NVy6te2h38Ajaw0Cnxf-swi0HDEopzyqDyvhziQJYddjrHXYqfJvGr_UPkgtBI59lg3L8qdM-vgXn4");'>
                                <p
                                    class="text-white text-base font-bold leading-tight line-clamp-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    New saplings ready for their new home</p>
                            </div>
                            <div class="bg-cover bg-center flex flex-col gap-3 rounded-lg justify-end p-4 aspect-[3/4] group cursor-pointer"
                                data-alt="A beautiful sunset over a newly reforested hillside."
                                style='background-image: linear-gradient(0deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuBLjxc9MO9Bv6pHiMK_3NOlJ_9zMUmsCH-LCIha9ZgETaAnm--O4UmPKmRFO3Va1IlVDtAdTvUq76oIY0RwoXKzQSJs4mciJmHTske1Pi72DC0thfh9IbfrLeJd0al8VN0mxicj0jLMgQSQGiK5cSCTQEXgCRIWI9v0TOPtbzuJuRaS3vzWrXuMnXGKtvl9e2n15NTinnEtMsb8HZc0PVHJU6OfiIocrr7Gvokd5QLf950xl3u756CUBySAnr5PdsMKA9MDeqSvXYI");'>
                                <p
                                    class="text-white text-base font-bold leading-tight line-clamp-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    Sunset over the Riverside reforestation project</p>
                            </div>
                            <div class="bg-cover bg-center flex flex-col gap-3 rounded-lg justify-end p-4 aspect-[3/4] group cursor-pointer"
                                data-alt="A group of people cheering around a newly planted tree with a sign that says '10,000th tree'."
                                style='background-image: linear-gradient(0deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuDbftCB5RfUcLLZSSj_ca6CflRMYBnHMMTr8cQyPTKhpWWbo5-ALJC2LHJezBn4-P1Wl-a2sXOb6enq1CPjnnVk7DUvb0AkpBtLhAAbR_PThSeuVIWZengrGWHHJQBM3tblpI0sxUtI-eD-mlsRsahp-M5c5e-d1okyCeA05o3dGlXBoBjmf31br3uuZCK2r_NEsUh-06n7KWLL3zOnDgNhKwH5qu6VhzfprhpZ4ncHo1tc1rHlFsCEerF_dQ9cv3OjppFW9z36NpU");'>
                                <p
                                    class="text-white text-base font-bold leading-tight line-clamp-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    Our 10,000th tree planted!</p>
                            </div>
                            <div class="bg-cover bg-center flex flex-col gap-3 rounded-lg justify-end p-4 aspect-[3/4] group cursor-pointer"
                                data-alt="A city street with newly planted trees along the sidewalk."
                                style='background-image: linear-gradient(0deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuCLXWEqVo6MJrUaXiQBsAm0wQqgVehyl11VmLN-JUbcE2ZTMhGoJ8psR4LfsHnec2h9njTaQ8o3LaqXE3QCuCWxosZpVaEmNrlZAFeoWXQVbfjSFfAKxEZBJ4D0LWkyMQsuBmR8Bz5UCSe0KXzjRiMOEyAdrDdwpho6poNjgKAmQrfkbYomVEiiChXJbOGAgtMTb7gSd7Tm-6IrWyPShBMPO1Rtzu0c6QWX0lsIrV4EdpAX4M6U6nC-gppcLZBUd_SHJmtJZRKipko");'>
                                <p
                                    class="text-white text-base font-bold leading-tight line-clamp-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    Urban greening project on Maple Street</p>
                            </div>
                        </div>
                        <div class="flex px-4 py-3 justify-center">
                            <button
                                class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-base font-bold leading-normal tracking-[0.015em] hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                                <span class="truncate">Load More</span>
                            </button>
                        </div>
                    </main>
                    <footer
                        class="mt-16 border-t border-gray-200 dark:border-gray-700 pt-10 pb-6 text-center text-gray-500 dark:text-gray-400">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-8 text-left px-4">
                            <div>
                                <h4 class="font-bold text-gray-800 dark:text-white mb-3">Navigate</h4>
                                <ul class="space-y-2">
                                    <li><a class="hover:text-primary" href="#">Home</a></li>
                                    <li><a class="hover:text-primary" href="#">About Us</a></li>
                                    <li><a class="hover:text-primary" href="#">Events</a></li>
                                    <li><a class="hover:text-primary" href="#">Gallery</a></li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 dark:text-white mb-3">Get Involved</h4>
                                <ul class="space-y-2">
                                    <li><a class="hover:text-primary" href="#">Donate</a></li>
                                    <li><a class="hover:text-primary" href="#">Volunteer</a></li>
                                    <li><a class="hover:text-primary" href="#">Start a Project</a></li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 dark:text-white mb-3">Resources</h4>
                                <ul class="space-y-2">
                                    <li><a class="hover:text-primary" href="#">Blog</a></li>
                                    <li><a class="hover:text-primary" href="#">FAQs</a></li>
                                    <li><a class="hover:text-primary" href="#">Contact</a></li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 dark:text-white mb-3">Follow Us</h4>
                                <div class="flex space-x-4">
                                    <a class="hover:text-primary" href="#">Facebook</a>
                                    <a class="hover:text-primary" href="#">Twitter</a>
                                    <a class="hover:text-primary" href="#">Instagram</a>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm">© 2024 Every Tree for Hope. All Rights Reserved.</p>
                    </footer>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
