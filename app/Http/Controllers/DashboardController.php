<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = JobVacancy::query();

        // search term
        $search = $request->input('search');
        // filter term (type)
        $filter = $request->input('filter');

        // Apply filter if selected
        if ($filter && in_array($filter, ['Full-Time', 'Part-Time', 'Hybrid', 'Contract', 'Remote', 'Internship'])) {
            $query->where('type', $filter);
        }

        // Apply search if entered
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('salary', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $jobs = $query->latest()->paginate(10)->appends($request->all());

        return view('dashboard.index', compact('jobs'));
    }
}
