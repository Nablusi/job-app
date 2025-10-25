<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{$jobVacancy->title }} . Apply
        </h2>
    </x-slot>

    <div class="py-12 mx-auto w-[80%]">

        <div class="bg-black shadow-lg rounded-lg p-6 max-w-7xl">
            <div class="px-6">

                <a href="{{ route('job-vacancies.show', $jobVacancy->id)}}"
                    class="bg-indigo-500 w-fit  text-white px-4 py-2 rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500  mb-4 max-w-7xl block text-center">
                    ← Back to job details</a>
            </div>
            <div class="bg-black shadow-lg rounded-lg p-6 max-w-7xl">

                <h3 class="text-white text-2xl font-bold">
                    {{ $jobVacancy->title }}
                </h3>
                <p class="text-white mt-2">
                    {{ $jobVacancy->company->name }}
                </p>
                <p class="text-white mt-2 sm:text-[12px] md:text-[16px] lg:text-[20px]">
                    {{ $jobVacancy->location }} . ${{ number_format($jobVacancy->salary) }} / year -
                    <span
                        class="bg-indigo-500 mt-5 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        {{ $jobVacancy->type }}
                    </span>
                </p>

            </div>
        </div>
    </div>

    <form class=" mx-auto w-[80%]" action="{{ route('job-vacancies.process-application', $jobVacancy->id) }}"
        method="POST" enctype="multipart/form-data">
        @csrf
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- resume selection -->
        <div>
            <h3 class="text-xl font-semibold text-white mb-4"> Choose Your Resume </h3>
        </div>
        <div class="mb-6">
            <x-input-label for="resume" :value="__('Select from existing resumes:')" class="text-white mb-2" />
            <!-- list of resumes -->
            <div class="flex items-center">
                <div class="space-y-4">
                    @forelse ($resumes as $resume)
                        <div class="flex items-center gap-2">
                            <input type="radio" name="resume_option" id={{ $resume->id }} value={{ $resume->id }} class="mr-2"  @error('resume_option') class="border-red-500" @else class="border-gray-600" @enderror>                                
                            <label for="resume_{{ $resume->id }}" class="text-white">
                                {{ $resume->filename }}
                                <span>Last Updated: {{ $resume->updated_at->diffForHumans() }} </span>
                            </label>
                        </div>
                    @empty
                        <p class="text-white">No resumes found. Please upload a new resume.</p>

                    @endforelse

                </div>
            </div>

        </div>

        <!-- upload resume -->
        <div x-data="{ fileName: '', hasError: {{ $errors->has('resume_file') ? 'true' : 'false' }} }" class="mb-6">
            <div class="flex items-center">
                <input x-ref="newResumeRadio" type="radio" name="resume_option" id="new_resume" value="new_resume" class="mr-2" @error('resume_option') class="border-red-500" @else class="border-gray-500"@enderror >
                <x-input-label for="new_resume" :value="__('Upload a new resume:')" class="text-white" />
            </div>

            <div class="flex items-center">
                <div class="flex-1">
                    <label for="new_resume_file" class="block text-white cursor-pointer">
                        <div class="border-2 border-dashed border-gray-600 rounded-600 p-4 hover:border-blue-500 transition"
                            :class="{ 'border-blue-500': fileName, 'border-red-500': hasError }">
                            <input @change="fileName = $event.target.files[0].name; $refs.newResumeRadio.checked = true"
                                type="file" name="resume_file" id="new_resume_file" class="hidden" accept=".pdf" />

                            <div class="text-center">
                                <template x-if="!fileName">
                                    <p class="text-white">Click to upload PDF format only, max size 5MB</p>
                                </template>

                                <template x-if="fileName">
                                    <div>
                                        <p x-text="fileName" class="mt-2 text-blue-500 font-medium"></p>
                                        <p class="text-sm text-gray-400 mt-1"> Click to change file</p>
                                    </div>
                                </template>


                            </div>

                        </div>

                    </label>

                </div>

            </div>

        </div>
        <!-- submit button -->
        <div class="pb-5">

            <x-primary-button class="w-full mt-5"
                onclick="window.location='{{ route('job-vacancies.apply', $jobVacancy->id) }}'">
                Submit Application
            </x-primary-button>
        </div>
    </form>





</x-app-layout>