<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobApplication;

class JobApplicationController extends Controller
{
    public function index()
    {
        $jobApplications = JobApplication::where('userId', auth()->id())->latest()->paginate(10);
        return view('job-applications.index', compact('jobApplications'));
    }
}
