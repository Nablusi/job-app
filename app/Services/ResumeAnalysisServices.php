<?php


namespace App\Services;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Storage;
class ResumeAnalysisServices
{
    public function extractResumeInformation(string $fileUrl)
    {
        ini_set('max_execution_time', 700);
        try {

            // Extract raw text from the resume using an external service or library
            $rawText = $this->extractTextFromPdf($fileUrl);

            log::debug('Extracted Resume Text: ' . strlen($rawText) . ' characters');
            // use OpenAI to analyze the resume content
            $response = OpenAI::chat()->create([
                'model' => 'gpt-5-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert resume analyzer. Extract key information from resumes including a summary, skills, experience, and education. without adding any extra information or interpretation. the output must be in json format only.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Parse the following resume content and extract the information as a json object with keys: summary, skills, experience, education.\n\nResume Text:\n" . $rawText . "\n\n return an empty string for any key that is not found."
                    ]
                ],
                'response_format' => [
                    'type' => 'json_object'
                ],
            ]);

            $result = $response->choices[0]->message->content;

            log::debug('OpenAI Response: ' . $result);

            $parsedResult = json_decode($result, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                log::error('JSON openAI Error: ' . json_last_error_msg());
                throw new \Exception('Failed to parse OpenAI response: ' . json_last_error_msg());
            }

            // validate keys exist
            $requiredKeys = ['summary', 'skills', 'experience', 'education'];
            $missingKeys = array_diff($requiredKeys, array_keys($parsedResult));

            if (!empty($missingKeys)) {
                log::error('Missing keys in OpenAI response: ' . implode(', ', $missingKeys));
                throw new \Exception('Missing keys in OpenAI response: ' . implode(', ', $missingKeys));
            }

            // output: summary, skills, experience, education -> json response

            //return json decoded response
            return [
                'summary' => $parsedResult['summary'],
                'skills' => $parsedResult['skills'],
                'experience' => $parsedResult['experience'],
                'education' => $parsedResult['education']
            ];
        } catch (\Exception $e) {
            log::error('Error in extractResumeInformation: ' . $e->getMessage());
            return [
                'summary' => '',
                'skills' => '',
                'experience' => '',
                'education' => ''
            ];
        }
    }

    private function extractTextFromPdf(string $fileUrl): string
    {
        $temp_file = tempnam(sys_get_temp_dir(), 'resume_');
        $filePath = parse_url($fileUrl, PHP_URL_PATH);

        if (!$filePath) {
            throw new \Exception("Invalid file URL");
        }

        $filename = basename($filePath);

        $storagePath = "resumes/" . $filename;

        if (!Storage::disk('public')->exists($storagePath)) {
            throw new \Exception("File does not exist in public storage");
        }

        $pdfContent = Storage::disk('public')->get($storagePath);


        if (!$pdfContent) {
            throw new \Exception("Failed to retrieve file content from cloud storage");
        }

        file_put_contents($temp_file, $pdfContent);

        // check if pdf is installed

        $pdfToTextPath = [
            '/usr/bin/pdftotext',
            '/usr/local/bin/pdftotext',
            'C:\\Program Files\\xpdf-tools-win\\bin64\\pdftotext.exe'
        ];
        $pdfToTextAvailable = null;

        foreach ($pdfToTextPath as $path) {
            if (file_exists($path)) {
                $pdfToTextAvailable = true;
                break;
            }
        }

        if (!$pdfToTextAvailable) {
            throw new \Exception("pdftotext is not installed on the server.");
        }

        // extract text using pdftotext

        // After extracting text
        $text = (new Pdf('C:\\Program Files\\xpdf-tools-win\\bin64\\pdftotext.exe'))
            ->setPdf($temp_file)
            ->text();

        // Clean up text encoding
        $text = mb_convert_encoding($text, 'UTF-8', 'auto');
        $text = preg_replace('/[^\x00-\x7F\xA0-\xFF]/', '', $text); // remove stray binary chars


        // clean up temporary file

        unlink($temp_file);

        return $text;
    }

    public function analyzeResume($jobVacancy, $resumeData)
    {
        ini_set('max_execution_time', 500);

        try {
            $jobDetails = json_encode([
                'title' => $jobVacancy->title,
                'description' => $jobVacancy->description,
                'location' => $jobVacancy->location,
                'salary' => $jobVacancy->salary,
                'type' => $jobVacancy->type,
                'categoryId' => $jobVacancy->categoryId,
                'companyId' => $jobVacancy->companyId,
            ]);

            $resumeDetails = json_encode($resumeData);

            $response = OpenAI::chat()->create([
                'model' => 'gpt-5-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are an expert HR professional and recruiter. You are a given a job vacancy and a resume and determine if the candidate is a good fit for the job. The output must be in json format only.
                                     Provide a score from 0 to 100 for the candidates  suitability for the job.
                                    Response should only be Json that has the following kys: 'aiGeneratedScore', 'aiGeneratedFeedback'.
                                    aiGenerated feedback should be detailed and specific to the job and the candidate's resume.
                        "
                    ],
                    [
                        'role' => 'user',
                        'content' => "Given the following job vacancy details and resume details, provide a match score and explanation in json format.\n\nJob Vacancy Details:\n" . $jobDetails . "\n\nResume Details:\n" . $resumeDetails . "\n\nReturn a json object with keys: match_score (integer 0-100), explanation (string)."
                    ]
                ],
                'response_format' => [
                    'type' => 'json_object'
                ],
            ]);

            $result = $response->choices[0]->message->content;
            log::debug('OpenAI Analysis Response: ' . $result);

            $parsedResult = json_decode($result, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                log::error('JSON openAI Error: ' . json_last_error_msg());
                throw new \Exception('Failed to parse OpenAI response: ' . json_last_error_msg());
            }

            if (!isset($parsedResult['aiGeneratedScore']) || !isset($parsedResult['aiGeneratedFeedback'])) {
                log::error('Missing keys in OpenAI analysis response');
                throw new \Exception('Missing keys in OpenAI analysis response');
            }

            return $parsedResult;

        } catch (\Exception $e) {
            log::error('Error in analyzeResume: ' . $e->getMessage());
            return [
                'aiGeneratedScore' => 0,
                'aiGeneratedFeedback' => 'Error analyzing resume: ' . $e->getMessage()
            ];
        }
    }
}

