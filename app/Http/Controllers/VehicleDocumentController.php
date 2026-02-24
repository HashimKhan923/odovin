<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use thiagoalessio\TesseractOCR\TesseractOCR;

class VehicleDocumentController extends Controller
{
    public function index(Vehicle $vehicle)
    {
        // $this->authorize('view', $vehicle);

        $vehicle->load('documents');

        return view('vehicles.documents.index', compact('vehicle'));
    }

    public function create(Vehicle $vehicle)
    {
        // $this->authorize('update', $vehicle);

        $documentTypes = [
            'registration' => 'Registration',
            'insurance' => 'Insurance',
            'warranty' => 'Warranty',
            'inspection' => 'Inspection',
            'driver_license' => 'Driver License',
            'passport' => 'Passport',
            'id_card' => 'ID Card',
            'other' => 'Other',
        ];

        return view('vehicles.documents.create', compact('vehicle', 'documentTypes'));
    }

    /**
     * Extract data from uploaded document using OCR
     */
    public function extractData(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $extractedData = $this->processDocument($file);

            return response()->json([
                'success' => true,
                'data' => $extractedData,
                'message' => 'Document processed successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Document extraction failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process document and extract relevant information
     */
    private function processDocument($file)
    {
        $fileName = strtolower($file->getClientOriginalName());
        $mimeType = $file->getClientMimeType();
        
        // Initialize extracted data
        $data = [
            'type' => null,
            'title' => null,
            'issue_date' => null,
            'expiry_date' => null,
            'notes' => null,
        ];

        // Perform OCR first to get text content
        $extractedText = '';
        if (in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'])) {
            try {
                $extractedText = $this->performOCR($file);
                \Log::info('OCR Full Text: ' . $extractedText);
            } catch (\Exception $e) {
                \Log::warning('OCR failed: ' . $e->getMessage());
            }
        }

        // Determine document type from filename AND OCR text
        $data['type'] = $this->detectDocumentType($fileName, $extractedText);
        
        // Generate title from filename and type
        $data['title'] = $this->generateTitle($fileName, $data['type'], $extractedText);

        // Extract fields from OCR text
        if (!empty($extractedText)) {
            $ocrData = $this->extractFieldsFromText($extractedText, $data['type']);
            $data = array_merge($data, $ocrData);
        }

        return $data;
    }

    /**
     * Detect document type from filename and OCR text
     */
    private function detectDocumentType($fileName, $text = '')
    {
        $keywords = [
            'driver_license' => [
                'driver license', 'driving license', 'driver licence', 'driving licence',
                'dl', 'drivers license', 'license id', 'lic no', 'license number',
                'dob', 'class', 'exp', 'iss', // Common abbreviations on licenses
            ],
            'passport' => [
                'passport', 'travel document', 'passport no', 'nationality',
                'place of birth', 'authority', 'p<'
            ],
            'id_card' => [
                'id card', 'identity card', 'national id', 'citizen card',
                'identification card', 'cnic', 'nic'
            ],
            'registration' => [
                'registration', 'reg', 'vehicle registration', 'rc',
                'certificate of registration', 'vehicle title', 'title certificate'
            ],
            'insurance' => [
                'insurance', 'policy', 'coverage', 'insured', 'premium',
                'liability', 'auto insurance', 'car insurance', 'policy number'
            ],
            'warranty' => [
                'warranty', 'guarantee', 'extended warranty', 'service contract',
                'warranty certificate'
            ],
            'inspection' => [
                'inspection', 'safety', 'emission', 'smog', 'test',
                'certification', 'vehicle inspection'
            ],
        ];

        $searchText = strtolower($fileName . ' ' . $text);

        // Check each type with priority (more specific first)
        foreach ($keywords as $type => $words) {
            foreach ($words as $word) {
                if (Str::contains($searchText, strtolower($word))) {
                    \Log::info("Document type detected: {$type} (matched keyword: {$word})");
                    return $type;
                }
            }
        }

        return 'other';
    }

    /**
     * Generate a readable title from filename
     */
    private function generateTitle($fileName, $type, $text = '')
    {
        // Remove extension
        $title = pathinfo($fileName, PATHINFO_FILENAME);
        
        // Replace underscores and hyphens with spaces
        $title = str_replace(['_', '-'], ' ', $title);
        
        // Capitalize words
        $title = ucwords($title);
        
        // If title is too generic or short, use type-based title
        if (strlen($title) < 5 || in_array(strtolower($title), ['document', 'scan', 'img', 'image', 'file'])) {
            $currentYear = date('Y');
            
            // Try to extract name from OCR for driver license
            if ($type === 'driver_license' && !empty($text)) {
                if (preg_match('/NAME\s+([A-Z\s]+)/i', $text, $matches)) {
                    $name = trim($matches[1]);
                    if (strlen($name) > 3 && strlen($name) < 50) {
                        return "Driver License - " . ucwords(strtolower($name));
                    }
                }
            }
            
            $typeLabels = [
                'driver_license' => 'Driver License',
                'passport' => 'Passport',
                'id_card' => 'ID Card',
                'registration' => 'Vehicle Registration',
                'insurance' => 'Insurance Policy',
                'warranty' => 'Warranty',
                'inspection' => 'Inspection Certificate',
                'other' => 'Document',
            ];
            
            $label = $typeLabels[$type] ?? 'Document';
            $title = $label . ' ' . $currentYear;
        }

        return $title;
    }

    /**
     * Perform OCR on the document using Tesseract
     */
    private function performOCR($file)
    {
        $extractedText = '';
        
        try {
            $filePath = $file->getRealPath();
            $mimeType = $file->getClientMimeType();
            
            // If PDF, convert first page to image
            if ($mimeType === 'application/pdf') {
                $filePath = $this->convertPdfToImage($file);
            }
            
            // Perform OCR with enhanced settings
            $ocr = new TesseractOCR($filePath);
            $ocr->lang('eng'); // Set language
            
            // PSM modes: 3 = Fully automatic page segmentation (default)
            //           6 = Assume a single uniform block of text
            //           11 = Sparse text. Find as much text as possible
            $ocr->psm(3); // Try automatic first
            
            $extractedText = $ocr->run();
            
            // If no text found, try with different PSM mode
            if (empty(trim($extractedText))) {
                \Log::info('No text with PSM 3, trying PSM 11');
                $ocr->psm(11);
                $extractedText = $ocr->run();
            }
            
            // Clean up temporary image if created
            if ($mimeType === 'application/pdf' && file_exists($filePath) && $filePath !== $file->getRealPath()) {
                @unlink($filePath);
            }
            
        } catch (\Exception $e) {
            \Log::error('Tesseract OCR failed: ' . $e->getMessage());
            throw $e;
        }
        
        return $extractedText;
    }

    /**
     * Convert PDF first page to image for OCR
     */
    private function convertPdfToImage($file)
    {
        try {
            // Using Imagick to convert PDF to image
            if (extension_loaded('imagick')) {
                $imagick = new \Imagick();
                $imagick->setResolution(300, 300); // High resolution for better OCR
                $imagick->readImage($file->getRealPath() . '[0]'); // Read first page only
                $imagick->setImageFormat('png');
                
                $tempPath = storage_path('app/temp/' . uniqid() . '.png');
                $imagick->writeImage($tempPath);
                $imagick->clear();
                $imagick->destroy();
                
                return $tempPath;
            }
            
            // Fallback: return original file
            return $file->getRealPath();
            
        } catch (\Exception $e) {
            \Log::warning('PDF to image conversion failed: ' . $e->getMessage());
            return $file->getRealPath();
        }
    }

    /**
     * Extract specific fields from OCR text based on document type
     */
    private function extractFieldsFromText($text, $documentType = null)
    {
        $data = [];

        // Extract dates based on document type
        $dates = $this->extractDates($text, $documentType);
        
        if (!empty($dates)) {
            // For driver license, passport, ID card - look for ISS/EXP or Issue/Expiry
            if (in_array($documentType, ['driver_license', 'passport', 'id_card'])) {
                $data = $this->extractDriverLicenseDates($text, $dates);
            } 
            // For registration, insurance, warranty
            else {
                if (count($dates) >= 2) {
                    $data['issue_date'] = $dates[0];
                    $data['expiry_date'] = end($dates);
                } elseif (count($dates) === 1) {
                    if (preg_match('/expir|valid until|validity|due date|exp/i', $text)) {
                        $data['expiry_date'] = $dates[0];
                    } else {
                        $data['issue_date'] = $dates[0];
                    }
                }
            }
        }

        // Extract document/policy/license number
        $documentNumber = $this->extractDocumentNumber($text, $documentType);
        if ($documentNumber) {
            $labels = [
                'driver_license' => 'License No: ',
                'passport' => 'Passport No: ',
                'id_card' => 'ID No: ',
                'insurance' => 'Policy No: ',
                'registration' => 'Registration No: ',
            ];
            $label = $labels[$documentType] ?? 'Document No: ';
            $data['notes'] = $label . $documentNumber;
        }

        // Extract additional info based on document type
        if ($documentType === 'driver_license') {
            $licenseInfo = $this->extractDriverLicenseInfo($text);
            if ($licenseInfo && !$data['notes']) {
                $data['notes'] = $licenseInfo;
            } elseif ($licenseInfo && $data['notes']) {
                $data['notes'] .= "\n" . $licenseInfo;
            }
        }

        // Extract vehicle information if found
        $vehicleInfo = $this->extractVehicleInfo($text);
        if ($vehicleInfo && !$data['notes']) {
            $data['notes'] = $vehicleInfo;
        } elseif ($vehicleInfo && $data['notes']) {
            $data['notes'] .= "\n" . $vehicleInfo;
        }

        return $data;
    }

    /**
     * Extract dates specifically for driver licenses
     */
    private function extractDriverLicenseDates($text, $allDates)
    {
        $data = [];
        
        // Look for ISS and EXP labels
        $lines = explode("\n", $text);
        
        foreach ($lines as $line) {
            $line = strtoupper($line);
            
            // Check for ISS (Issue) date
            if (preg_match('/ISS[UE]*\s*[:]*\s*(\d{1,2}[\.\/-]\d{1,2}[\.\/-]\d{2,4})/i', $line, $matches)) {
                $issDate = $this->parseDate($matches[1]);
                if ($issDate) {
                    $data['issue_date'] = $issDate;
                    \Log::info("Found ISS date: {$issDate}");
                }
            }
            
            // Check for EXP (Expiry) date
            if (preg_match('/EXP[IRY]*\s*[:]*\s*(\d{1,2}[\.\/-]\d{1,2}[\.\/-]\d{2,4})/i', $line, $matches)) {
                $expDate = $this->parseDate($matches[1]);
                if ($expDate) {
                    $data['expiry_date'] = $expDate;
                    \Log::info("Found EXP date: {$expDate}");
                }
            }
            
            // Alternative: look for "ISSUE" and "EXPIRY" or "VALID UNTIL"
            if (preg_match('/ISSUE[D]*\s*[:]*\s*(\d{1,2}[\.\/-]\d{1,2}[\.\/-]\d{2,4})/i', $line, $matches)) {
                if (!isset($data['issue_date'])) {
                    $issDate = $this->parseDate($matches[1]);
                    if ($issDate) {
                        $data['issue_date'] = $issDate;
                    }
                }
            }
            
            if (preg_match('/(EXPIR[Y]*|VALID\s*UNTIL)\s*[:]*\s*(\d{1,2}[\.\/-]\d{1,2}[\.\/-]\d{2,4})/i', $line, $matches)) {
                if (!isset($data['expiry_date'])) {
                    $expDate = $this->parseDate($matches[2]);
                    if ($expDate) {
                        $data['expiry_date'] = $expDate;
                    }
                }
            }
        }
        
        // If we didn't find labeled dates, use the extracted dates array
        if (!isset($data['issue_date']) && !isset($data['expiry_date']) && count($allDates) >= 2) {
            // Assume earlier date is issue, later is expiry
            sort($allDates);
            $data['issue_date'] = $allDates[0];
            $data['expiry_date'] = end($allDates);
        }
        
        return $data;
    }

    /**
     * Extract driver license specific information
     */
    private function extractDriverLicenseInfo($text)
    {
        $info = [];
        
        // Extract class/category
        if (preg_match('/CLASS\s*[:]*\s*([A-Z0-9]+)/i', $text, $matches)) {
            $info[] = 'Class: ' . trim($matches[1]);
        }
        
        // Extract DOB
        if (preg_match('/DOB\s*[:]*\s*(\d{1,2}[\.\/-]\d{1,2}[\.\/-]\d{2,4})/i', $text, $matches)) {
            $info[] = 'DOB: ' . trim($matches[1]);
        }
        
        return !empty($info) ? implode(', ', $info) : null;
    }

    /**
     * Extract document/policy/license numbers
     */
    private function extractDocumentNumber($text, $documentType = null)
    {
        // Specific patterns based on document type
        $patterns = [];
        
        if ($documentType === 'driver_license') {
            $patterns = [
                '/(?:LICENSE|LICENCE|DL|LIC)[\s#:NO]*([A-Z0-9-]{6,20})/i',
                '/ID[\s#:]*([0-9-]{8,20})/i',
            ];
        } elseif ($documentType === 'passport') {
            $patterns = [
                '/PASSPORT[\s#:NO]*([A-Z0-9]{6,12})/i',
                '/P<[A-Z]{3}([A-Z0-9]+)/i',
            ];
        } else {
            $patterns = [
                '/(?:POLICY|DOCUMENT|CERTIFICATE|REG|REGISTRATION|NUMBER)[\s#:]*([A-Z0-9-]{6,20})/i',
                '/(?:NO|#)[\s:]*([A-Z0-9-]{6,20})/i',
            ];
        }

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $number = trim($matches[1]);
                // Filter out numbers that are likely dates or too generic
                if (!preg_match('/^\d{1,2}[\.\/-]\d{1,2}[\.\/-]\d{2,4}$/', $number)) {
                    return $number;
                }
            }
        }

        return null;
    }

    /**
     * Extract vehicle information (VIN, plate number, etc.)
     */
    private function extractVehicleInfo($text)
    {
        $info = [];

        // VIN pattern (17 characters)
        if (preg_match('/\b([A-HJ-NPR-Z0-9]{17})\b/i', $text, $matches)) {
            $info[] = 'VIN: ' . $matches[1];
        }

        // License plate patterns
        if (preg_match('/(?:PLATE|LICENSE|REGISTRATION)[\s#:]*([A-Z0-9-]{4,10})/i', $text, $matches)) {
            $plate = trim($matches[1]);
            if (!in_array(strtoupper($plate), ['NUMBER', 'NO', 'ID'])) {
                $info[] = 'Plate: ' . $plate;
            }
        }

        return !empty($info) ? implode(', ', $info) : null;
    }

    /**
     * Extract dates from text with enhanced detection
     */
    private function extractDates($text, $documentType = null)
    {
        $dates = [];
        
        // Enhanced date patterns including dots and various formats
        $patterns = [
            // DD.MM.YYYY or DD/MM/YYYY or DD-MM-YYYY
            '/\b(\d{1,2})[\.\/-](\d{1,2})[\.\/-](\d{4})\b/',
            // YYYY-MM-DD or YYYY/MM/DD or YYYY.MM.DD
            '/\b(\d{4})[\.\/-](\d{1,2})[\.\/-](\d{1,2})\b/',
            // DD Month YYYY (e.g., 10 February 1990)
            '/\b(\d{1,2})\s+(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*[\s,]+(\d{4})\b/i',
            // Month DD, YYYY (e.g., February 10, 1990)
            '/\b(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\s+(\d{1,2})[\s,]+(\d{4})\b/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    try {
                        $date = $this->parseDate($match[0]);
                        if ($date && $this->isValidDate($date)) {
                            $dates[] = $date;
                            \Log::info("Extracted date: {$date} from: {$match[0]}");
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }

        // Remove duplicates and sort
        $dates = array_unique($dates);
        sort($dates);

        \Log::info('All extracted dates: ' . implode(', ', $dates));

        return $dates;
    }

    /**
     * Parse date string to Y-m-d format with better handling
     */
    private function parseDate($dateString)
    {
        try {
            // Clean the date string
            $dateString = trim($dateString);
            
            // Handle DD.MM.YYYY format specifically
            if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', $dateString, $matches)) {
                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $year = $matches[3];
                $dateString = "{$year}-{$month}-{$day}";
            }
            
            $date = new \DateTime($dateString);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            \Log::warning("Failed to parse date: {$dateString}");
            return null;
        }
    }

    /**
     * Validate if date is reasonable (not too far in past or future)
     */
    private function isValidDate($dateString)
    {
        try {
            $date = new \DateTime($dateString);
            $now = new \DateTime();
            $fiftyYearsAgo = (new \DateTime())->modify('-50 years'); // For DOB
            $twentyYearsAhead = (new \DateTime())->modify('+20 years'); // For expiry
            
            return $date >= $fiftyYearsAgo && $date <= $twentyYearsAhead;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function store(Request $request, Vehicle $vehicle)
    {
        // $this->authorize('update', $vehicle);

        $validated = $request->validate([
            'type' => 'required|in:registration,insurance,warranty,inspection,driver_license,passport,id_card,other',
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'notes' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents/' . $vehicle->id, 'public');

        $document = $vehicle->documents()->create([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'issue_date' => $validated['issue_date'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Create reminder for expiring documents
        if ($document->expiry_date) {
            $vehicle->reminders()->create([
                'type' => $validated['type'],
                'title' => $validated['title'] . ' Expiration',
                'description' => 'Your ' . $validated['title'] . ' will expire soon',
                'due_date' => $document->expiry_date,
                'reminder_date' => $document->expiry_date->subDays(30),
                'priority' => 'high',
            ]);
        }

        return redirect()
            ->route('vehicles.documents.index', $vehicle)
            ->with('success', 'Document uploaded successfully!');
    }

    public function show(Vehicle $vehicle, VehicleDocument $document)
    {
        // $this->authorize('view', $vehicle);

        return view('vehicles.documents.show', compact('vehicle', 'document'));
    }

    public function edit(Vehicle $vehicle, VehicleDocument $document)
    {
        // $this->authorize('update', $vehicle);

        $documentTypes = [
            'registration' => 'Registration',
            'insurance' => 'Insurance',
            'warranty' => 'Warranty',
            'inspection' => 'Inspection',
            'driver_license' => 'Driver License',
            'passport' => 'Passport',
            'id_card' => 'ID Card',
            'other' => 'Other',
        ];

        return view('vehicles.documents.edit', compact('vehicle', 'document', 'documentTypes'));
    }

    public function update(Request $request, Vehicle $vehicle, VehicleDocument $document)
    {
        // $this->authorize('update', $vehicle);

        $validated = $request->validate([
            'type' => 'required|in:registration,insurance,warranty,inspection,driver_license,passport,id_card,other',
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            // Delete old file
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $validated['file_path'] = $file->store('documents/' . $vehicle->id, 'public');
            $validated['file_type'] = $file->getClientMimeType();
        }

        $document->update($validated);

        return redirect()
            ->route('vehicles.documents.index', $vehicle)
            ->with('success', 'Document updated successfully!');
    }

    public function destroy(Vehicle $vehicle, VehicleDocument $document)
    {
        // $this->authorize('delete', $vehicle);

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->route('vehicles.documents.index', $vehicle)
            ->with('success', 'Document deleted successfully!');
    }

    public function download(Vehicle $vehicle, VehicleDocument $document)
    {
        // $this->authorize('view', $vehicle);

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($document->file_path, $document->title);
    }
}