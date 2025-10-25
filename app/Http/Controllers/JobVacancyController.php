<?php

namespace App\Http\Controllers;


use App\Models\JobApplication;
use App\Models\Resume;
use Illuminate\Http\Request;

use App\Models\JobVacancy;
use OpenAI\Laravel\Facades\OpenAI;
use App\Http\Requests\ApplyJobRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class JobVacancyController extends Controller
{
    protected $resumeAnalysisService;

    public function __construct(\App\Services\ResumeAnalysisServices $resumeAnalysisService)
    {
        $this->resumeAnalysisService = $resumeAnalysisService;
    }

    public function show(string $id)
    {
        $jobVacancy = JobVacancy::findOrFail($id);
        return view('job-vacancies.show', compact('jobVacancy'));
    }

    public function apply(string $id)
    {
        $jobVacancy = JobVacancy::findOrFail($id);
        $resumes = auth()->user()->resume;
        return view('job-vacancies.apply', compact('jobVacancy', 'resumes'));
    }

    public function processApplication(ApplyJobRequest $request, string $id)
    {
        $jobVacancy = JobVacancy::findOrFail($id);
        $resumeId = null;
        $extractedInfo = null;

        if ($request->input('resume_option') == 'new_resume') {

            $file = $request->file('resume_file');
            $extension = $file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName();
            $fileName = 'resume_' . time() . '.' . $extension;

            //store in laravel cloud storage
            $path = $file->storeAs('resumes', $fileName, 'cloud');
            $fileUrl = Storage::disk('cloud')->url($path);


            $extractedInfo = $this->resumeAnalysisService->extractResumeInformation($fileUrl);

            $resume = Resume::create([
                'userId' => auth()->id(),
                'filename' => $originalName,
                'filePath' => $path,
                'fileUri' => $fileUrl,
                'contactDetails' => json_encode([
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ]),
                'summary' => $extractedInfo['summary'],
                'skills' => json_encode($extractedInfo['skills']), // <-- FIXED
                'experience' => json_encode($extractedInfo['experience']), // <-- FIXED
                'education' => json_encode($extractedInfo['education']), // <-- FIXED
            ]);

            $resumeId = $resume->id;

        } else {
            $resumeId = $request->input('resume_option');
            $resume = Resume::findOrFail($resumeId);

            $extractedInfo = [
                'summary' => $resume->summary,
                'skills' => $resume->skills,
                'experience' => $resume->experience,
                'education' => $resume->education,
            ];

        }



        // evaluate application with AI (mocked for now)
        $evaluation = $this->resumeAnalysisService->analyzeResume($jobVacancy, $extractedInfo);


        JobApplication::create([
            'jobVacancyId' => $id,
            'userId' => auth()->id(),
            'resumeId' => $resumeId,
            'status' => 'pending',
            'aiGeneratedScore' => $evaluation['aiGeneratedScore'],
            'aiGeneratedFeedback' => $evaluation['aiGeneratedFeedback']
        ]);

        return redirect()->route('job-applications.index', $id)->with('success', 'Your application has been submitted successfully.');
    }
}
