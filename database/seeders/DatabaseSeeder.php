<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\JobCategory;
use App\Models\Company;
use App\Models\JobVacancy;
use App\Models\Resume;
use App\Models\JobApplication;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $faker = Faker::create();

        User::firstOrCreate(
            [
                'email' => 'admin@admin.com',
            ]
            ,
            [
                'name' => 'admin',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // seed data to test with 

        $jobData = json_decode(file_get_contents(database_path('fakeData/job_data.json')), true);
        $jobApplication = json_decode(file_get_contents(database_path('fakeData/job_applications.json')), true);

        //create job categories
        foreach ($jobData['jobCategories'] as $category) {
            JobCategory::firstOrCreate([
                'name' => $category,
            ]);
        }

        //create companies
        foreach ($jobData['companies'] as $company) {
            $companyOwner = User::firstOrCreate(['email' => $faker->unique()->safeEmail()],
            [
                'name' => $faker->name(),
                'password' => Hash::make('123456'),
                'role' => 'company-owner',
                'email_verified_at' => now(),
            ]);


            Company::firstOrCreate(['name' => $company['name'],],[
                'address' => $company['address'],
                'industry' => $company['industry'],
                'website' => $company['website'],
                'ownerId' => $companyOwner->id,
            ]);
        }


     //   create job vacancies
        foreach ($jobData['jobVacancies'] as $jobVacancy) {
            $company = Company::where('name', $jobVacancy['company'])->firstOrFail();
            $category = JobCategory::where('name', $jobVacancy['category'])->firstOrFail();

            JobVacancy::firstOrCreate([
                'title' => $jobVacancy['title'],
            ],[
                'description' => $jobVacancy['description'],
                'location' => $jobVacancy['location'],
                'type' => $jobVacancy['type'],
                'salary' => $jobVacancy['salary'],
                'companyId' => $company->id,
                'categoryId' => $category->id,
            ]);
        }
        
        // create job applications
        foreach ($jobApplication['jobApplications'] as $application) {
        // get random job vacancy
            $jobVacancy = JobVacancy::inRandomOrder()->first();
            // create user
            $applicant = User::firstOrCreate(['email' => $faker->unique()->safeEmail()],[
                'name' => $faker->name(),
                'password' => Hash::make('123456'),
                'role' => 'job-seeker',
                'email_verified_at' => now(),
            ]);

            // create resume
            $resume = Resume::firstOrCreate([
                'userId' => $applicant->id,
            ],[
                'filename' => $application['resume']['filename'],
                'fileUri' => $application['resume']['fileUri'],
                'contactDetails' => $application['resume']['contactDetails'],
                'summary' => $application['resume']['summary'],
                'skills' => $application['resume']['skills'],
                'experience' => $application['resume']['experience'],
                'education' => $application['resume']['education'],
            ]);

            // create job application
            JobApplication::create(
                [
                'userId' => $applicant->id,
                'jobVacancyId' => $jobVacancy->id,
                'resumeId' => $resume->id,
                'status' => $application['status'],
                'aiGeneratedScore' => $application['aiGeneratedScore'],
                'aiGeneratedFeedback' => $application['aiGeneratedFeedback'],
                
            ]);


        }
    }
}
