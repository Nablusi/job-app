<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('status', ['pending', 'reviewed', 'interviewed', 'accepted', 'rejected'])->default('pending');
            $table->float('aiGeneratedScore',2)->default(0);
            $table->longText('aiGeneratedFeedback')->nullable();

            $table->timestamps();
            $table->softDeletes();

  

            //relationship with resumeId table (applicantId references id in users table)
            $table->uuid('resumeId');
            $table->foreign('resumeId')->references('id')->on('resumes')->onDelete('restrict');


            //relationship with userId table (applicantId references id in users table)
            $table->uuid('userId');
            $table->foreign('userId')->references('id')->on('users')->onDelete('restrict');

            //relationship with jobVacancyId table (jobVacancyId references id in job_vacancies table)
            $table->uuid('jobVacancyId');
            $table->foreign('jobVacancyId')->references('id')->on('job_vacancies')->onDelete('restrict');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
