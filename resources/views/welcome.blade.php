<x-main-layout title="Shaghalni - find your dream job">
    <div class="inline-flex flex-col items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8"
        x-data="{show: false}" x-init="setTimeout(() => show = true, 300)">
        <!-- x-clock عشان يكون اول شي الديسبلي نان -->
        <x-hero-container>
            <h1 class="text-4xl font-bold text-white/60 rounded-full bg-white/10 px-3 py-1 w-fit "> Shaghalni </h1>
        </x-hero-container>
        <x-hero-container>
            <h1 class="text-4xl sm:text-6xl md:text-8xl font-bold mb-6 tracking-tight">
                <span class="text-white"> Find your </span> <br />
                <span class="text-white/60 font-serif italic"> Dream job </span>

            </h1>
        </x-hero-container>

        <x-hero-container>
            <p class="text-lg text-white/60 mb-8 max-w-lg text-center"> Explore thousands of job opportunities
                with all the information you need. It's your
                future. Come find it. Manage all your job
                applications from start to finish. </p>
        </x-hero-container>

        <x-hero-container>
            <a href="{{ route('register') }}"
                class="px-8 py-3 bg-white/10 text-white rounded-full hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white/30 transition">Register
            </a>
            <a href="{{ route('login') }}"
                class="ml-4 px-8 py-3 bg-white/10 text-white rounded-full hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white/30 transition bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 shadow-lg">
                Login
            </a>
        </x-hero-container>
    </div>
</x-main-layout>