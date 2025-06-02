<?php

namespace App\Services\UserForm;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Support\Str;

/**
 * Service for processing form output, including cleaning data,
 * handling file uploads, storing JSON data, and generating PDFs.
 */
class FormOutputServices
{
    /**
     * Clean and prepare data for PDF generation.
     *
     * @param array $validatedData The validated form data.
     * @return array Cleaned form data.
     */
    public function cleanPDFData(array $validatedData): array
    {
        $cleanPDFData = [];

        foreach ($validatedData as $key => $value) {
            // Skip empty values
            if ($value === [] || $value === '' || $value === null) {
                continue;
            } elseif ($key === 'embedded-images') {
                // Skip processing for 'embedded-images' key
                unset($cleanPDFData[$key]);
            } elseif ($key === 'files') {
                // Transform file paths into their base names for better readability in the PDF
                $list = [];
                foreach ($value as $k => $item) {
                    $list[$k] = File::basename($item);
                }
                $cleanPDFData[$key] = $list;
            } else {
                $cleanPDFData[$key] = $value;
            }
        }

        return $cleanPDFData;
    }

    /**
     * Handle file uploads and generate an embeddable image.
     *
     * @param \Illuminate\Http\UploadedFile $file The uploaded file.
     * @return array [File path, Embeddable image string]
     */
    public function handleUploadFile($file)
    {
        $fileName = Str::random(16) . '.' . $file->getClientOriginalExtension();
        $filePathStr = $file->storeAs('/attachments', $fileName, 'public');

        $fullPath = storage_path('app/public/' . $filePathStr);
        $embed = null;

        // Convert images to base64 for embedding
        if (Str::startsWith(File::mimeType($fullPath), 'image/')) {
            $mimeType = File::mimeType($fullPath);
            $embed = 'data:' . $mimeType . ';base64,' . base64_encode(File::get($fullPath));
        }

        return [$filePathStr, $embed];
    }

    /**
     * Store submitted form data as a JSON file.
     *
     * @param string $formName The form name/title.
     * @param array $validatedData The validated form fields.
     * @return string Path to the stored JSON file.
     */
    public function storeFormDataAsJson(string $formName, array $validatedData): string
    {
        $jsonPath = storage_path("app/public/forms/");
        $formDataWithName = [
            "form_name" => $formName,
            "submitted_at" => now()->toDayDateTimeString(),
            "fields" => $validatedData,
        ];

        // Create directory if it does not exist
        if (!File::exists($jsonPath)) {
            File::makeDirectory($jsonPath, 0755, true);
        }

        // Generate unique filename for JSON storage
        $fileName = "form_" . now()->format("Ymd_His") . ".json";
        File::put($jsonPath . $fileName, json_encode($formDataWithName, JSON_PRETTY_PRINT));

        return $jsonPath . $fileName;
    }

    /**
     * Generate a PDF document from form submission data.
     *
     * @param string $formName The form title.
     * @param array $validatedData The cleaned form fields.
     * @param array $embeddedImages The embedded images for display.
     * @return array [PDF data, PDF file path]
     */
    public function generatePdf(string $formName, array $validatedData, array $embeddedImages, array $hiddenFields = []): array
    {
        $logoPath = storage_path('app/public/logo-96x96.png');
        $logo = 'data:' . File::mimeType($logoPath) . ';base64,' . base64_encode(File::get($logoPath));

        // Prepare data for the PDF template
        $pdfData = [
            "title" => $formName,
            'logo' => $logo,
            "fields" => $validatedData,
            "embeddedImages" => $embeddedImages,
            "hiddenFields" => $hiddenFields // Add hidden fields
        ];

        // Create a PDF from the view template
        $pdf = Pdf::setPaper('a4')
            ->loadView("forms.pdf", $pdfData)
            ->setOptions(["isRemoteEnabled" => true]);

        // Save PDF file
        $pdfContent = $pdf->output();
        $relativePath = "pdf/form_" . now()->format("Ymd_His") . ".pdf";
        Storage::disk("public")->put($relativePath, $pdfContent);

        return [$pdfData, "public/" . $relativePath];
    }
}
