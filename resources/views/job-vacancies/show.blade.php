<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{$jobVacancy->title }}
        </h2>
    </x-slot>

    <div class="py-12 flex flex-col items-center">

        <div class="bg-black shadow-lg rounded-lg p-6 max-w-7xl">
            <div class="px-6">

                <a href="{{ route('dashboard') }}"
                    class="bg-indigo-500 w-fit  text-white px-4 py-2 rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500  mb-4 max-w-7xl block text-center">
                    ← Back to Dashboard</a>
            </div>
            <div class="bg-black shadow-lg rounded-lg p-6 max-w-7xl">

                <h3 class="text-white text-2xl font-bold">
                    {{ $jobVacancy->title }}
                </h3>
                <p class="text-white mt-2">
                    {{ $jobVacancy->company->name }}
                </p>
                <p class="text-white mt-2">
                    {{ $jobVacancy->location }} . ${{ number_format($jobVacancy->salary) }} / year - <span
                        class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        {{ $jobVacancy->type }} </span>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-4 mt-4">
                    <div>
                        <p><strong>Job Description:</strong></p>
                        {!! nl2br(e($jobVacancy->description)) !!}
                    </div>
                    <div class="bg-gray-900 rounded-lg space-y-4 p-[20px]">
                        <p><strong>Job Overview:</strong></p>
                        <div class="mb-3">
                            <p>Published at:</p>
                            <p class="text-white">{{ $jobVacancy->created_at->format('M d, Y') }}</p>
                        </div>

                        <div class="mb-3">
                            <p>Company:</p>
                            <p class="text-white">{{ $jobVacancy->company->name }}</p>
                        </div>
                        <div class="mb-3">
                            <p>Location:</p>
                            <p class="text-white">{{ $jobVacancy->location }}</p>
                        </div>
                        <div class="mb-3">
                            <p>Salary:</p>
                            <p class="text-white">${{ number_format($jobVacancy->salary) }} / year</p>
                        </div>
                        <div class="mb-3">
                            <p>Type:</p>
                            <p class="text-white">{{ $jobVacancy->type }}</p>
                        </div>
                        <div class="mb-3">
                            <p>Category:</p>
                            <p class="text-white">{{ $jobVacancy->JobCategory->name }}</p>
                        </div>
                    </div>
                </div>


                <div class="mt-10">
                    <!-- Apply Now button -->
                    <x-primary-button onclick="window.location='{{ route('job-vacancies.apply', $jobVacancy->id) }}'">
                        Apply Now
                    </x-primary-button>
                </div>
            </div>
        </div>
    </div>



</x-app-layout>