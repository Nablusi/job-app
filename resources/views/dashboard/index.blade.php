<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="bg-black shadow-lg rounded-lg p-6 max-w-7xl mx-auto">
            <h3 class="text-white text-2xl font-bold">
                {{ 'Welcome back,' }} {{ Auth::user()->name }}!

            </h3>

            <!-- search bar -->
            <div class="mt-4">
                <form action="{{ route('dashboard') }}" method="GET" class="flex">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search jobs..."
                        class="w-full px-4 py-2 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-black">
                    <button type="submit"
                        class="bg-indigo-500 text-white px-4 py-2 rounded-r-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">Search</button>

                    <!-- clear search -->
                    @if (request('search'))
                        <a href="{{ route('dashboard', ['filter' => request('filter') ?? '']) }}"
                            class="bg-red-500 text-white px-4 py-2 rounded-r-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 ml-2">Clear</a>
                    
                    @endif


                    @if (request('filter'))
                        <input type="hidden" name="filter" value="{{ request('filter') }}">

                    @endif
                </form>
            </div>

            <!-- filters -->
            <div class="mt-4 flex space-x-4">
                <a href="{{ route('dashboard', ['filter' => 'Full-Time', 'search' => request('search') ?? '']) }}"
                    class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">Full-Time</a>
                <a href="{{ route('dashboard', ['filter' => 'Part-Time', 'search' => request('search') ?? '']) }}"
                    class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">Part-Time</a>
                <a href="{{ route('dashboard', ['filter' => 'Hybrid', 'search' => request('search') ?? '']) }}"
                    class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">Hybrid</a>
                <a href="{{ route('dashboard', ['filter' => 'Contract', 'search' => request('search') ?? '']) }}"
                    class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">Contract</a>
                <a href="{{ route('dashboard', ['filter' => 'Remote', 'search' => request('search') ?? '']) }}"
                    class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">Remote</a>
                <a href="{{ route('dashboard', ['filter' => 'Internship', 'search' => request('search') ?? '']) }}"
                    class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">Internship</a>

                <a href="{{ route('dashboard', ['search' => request('search') ?? '']) }}"
                    class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">Clear</a>
            </div>


            <!-- jobs list -->


            @forelse ($jobs as $job)
                <div class="flex justify-between items-center border-b border-white/10  p-4 mt-6 rounded-lg">
                    <div>
                        <a href="{{ route('job-vacancies.show', $job->id) }}" class="text-lg font-semibold text-blue-400 hover:underline"> {{ $job->title }} </a>
                        <p class="text-sm text-white"> {{ $job->location }} </p>
                        <p class="text-sm text-white"> $ {{number_format($job->salary) }} / year </p>
                    </div>
                    <span class="bg-blue-500 text-white p-2 rounded-lg h-fit"> {{ $job->type }} </span>
                </div>
            @empty
                <p class="text-white mt-6">No jobs found.</p>
            @endforelse
        </div>

        <div class="mt-5">
            {{ $jobs->links() }}
        </div>
</x-app-layout>