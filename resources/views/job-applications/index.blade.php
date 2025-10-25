<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('My Applications') }}
        </h2>
    </x-slot>

    <!-- validate session -->
    @if (session('success'))
        <div class="w-full bg-indigo-500 text-white p-4 mb-4 rounded-md ">
            {{ session('success') }}
        </div>
    @endif

    <div class="py-12">
        <div class="bg-black shadow-lg rounded-lg p-6 max-w-7xl mx-auto space-x-y-4">
            @forelse ($jobApplications as $jobApplication)
                <div class="bg-gray-900 shadow-lg rounded-lg p-6 max-w-7xl mx-auto space-y-4 mb-3">
                    <h3 class="text-white text-2xl font-bold">{{ $jobApplication->jobVacancy->title }}</h3>
                    <p class="text-sm">{{ $jobApplication->jobVacancy->company->name }}</p>
                    <p class="text-xs"> {{ $jobApplication->jobVacancy->location }} </p>

                    <div class="flex items-center justify-between">
                        <p> {{ $jobApplication->created_at->format('d M Y') }} </p>
                        <p class="px-3 py-1 bg-blue-600 text-white rounded-md"> {{ $jobApplication->jobVacancy->type }} </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <span>Applied with: {{ $jobApplication->resume->filename }}  </span>
                        <a href="{{ Storage::disk('public')->url($jobApplication->resume->fileUri)}}" target="_blank" class="text-indigo-400 underline">View Resume</a>
                    </div>
                    @php
                        $status = $jobApplication->status;
                        $statusColors = [
                            'pending' => 'bg-yellow-500',
                            'reviewed' => 'bg-indigo-500',
                            'interviewed' => 'bg-blue-500',
                            'accepted' => 'bg-green-500',
                            'rejected' => 'bg-red-500',
                        ];
                        $statusColorClass = $statusColors[$status] ?? 'bg-gray-500';
                    @endphp


                    <div class="flex items-start flex-col gap-2 mt-4">
                        <div class="flex gap-2">

                            <p class="text-sm {{ $statusColorClass }}  text-white p-2 rounded-md w-fit">Status: {{ $jobApplication->status }}  </p>
                            <p class="text-sm bg-indigo-500 text-white p-2 rounded-md w-fit">Score: {{ $jobApplication->aiGeneratedScore }}  </p>
                        </div>

                        <p class="text-sm">AI Feedback: {{ $jobApplication->aiGeneratedFeedback }}  </p>
                    </div>

                </div>

            @empty
                <p class="text-white">You have not applied to any jobs yet.</p>
            @endforelse

        </div>

        <div class="mt-6" style="display: flex; justify-content: center; width: 90% !important;">
            {{ $jobApplications->links() }}
        </div>

    </div>

</x-app-layout>