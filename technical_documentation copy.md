# Technical Documentation: Logisteed Application
Last updated: 2025-08-29

## Table of Contents
1. [Document Approval System](#document-approval-system)
2. [Procurement and Purchasing System](#procurement-and-purchasing-system)
3. [Common User Activities](#common-user-activities)

## Document Approval System

### Use Case 1: Creating a New Document

**Actor**: Employee (Document Requester)  
**Location**: `Modules/DocumentApproval/app/Http/Controllers/DocumentApprovalController.php`

#### Activity Flow
1. User initiates document creation
2. System loads document types
3. User fills form and submits
4. System processes and routes for approval

#### Code Implementation

```php
// Document type loading
public function getDocumentType(Request $request) {
    $companyId = $request->companyId;
    $viewType = $request->viewType;
    $model = new DocumentApprovalModel();
    $getData = $model->masterDocumentTypes()
                    ->from('master_doc_types as a')
                    ->select('a.*', 'b.group_name', 'b.group_description')
                    ->leftJoin('master_doc_groups as b', 'b.doc_group_id', '=', 'a.doc_group_id')
                    ->where(function($query) use ($companyId) {
                            $query->whereNull('a.company_id')
                                ->orWhere('a.company_id', '=', $companyId);
                        })
                    ->where('a.is_active', '=', '1')
                    ->where('a.is_view', '=', '1')
                    ->orderBy('a.doc_order', 'asc')
                    ->get();
    return $getData;
}

// Document submission
public function saveForm(Request $request) {
    // Validation
    $validatedData = $request->validate([
        'documentType' => 'required|int|max:255',
        'departmentOrder' => 'required|string|max:255',
        'approver.*' => 'required|string|max:255',
        'attachmentFileDocument.*' => 'required|file|max:5120|mimetypes:application/pdf,...'
    ]);

    // Process submission
    $transactionStatusId = ($request->actionType == 'SUBMIT') ? '2' : '1'; // 2=SUBMITTED, 1=DRAFT
    
    // Save document and initialize approval flow
    // ... implementation details
}
```

#### Data Flow
```
User Input → Validation → Database Storage
├── Document Metadata
│   ├── Type
│   ├── Purpose
│   └── Department
├── Attachments
│   ├── Files
│   └── Metadata
└── Approval Matrix
    ├── Approvers
    └── Flow Configuration
```

### Use Case 2: Document Approval Process

**Actor**: Approver/Reviewer  
**Location**: `Modules/DocumentApproval/app/Http/Controllers/DocumentApprovalController.php`

#### Activity Flow
1. Approver receives notification
2. Reviews document
3. Takes action
4. System processes and updates status

#### Code Implementation

```php
// Approval action processing
public function updateAction(Request $request) {
    try {
        // Validate action
        if($actionType == 'REJECT' || $actionType == 'CANCEL'){
            $validatedData = $request->validate([
                'commentAction' => 'required|string|max:255',
            ]);
        }
        else if($actionType == 'SEND_BACK'){
            $validatedData = $request->validate([
                'commentAction' => 'required|string|max:255',
                'sendBackTo' => 'required|max:255',
            ]);
        }

        // Process approval flow
        $getFlowSign = $model->documentApprovalHeader()
                            ->from('doc_approval_header AS a')
                            ->leftJoin('doc_approval_flow_sign AS b', function($join) {
                                $join->on('b.doc_approval_id', '=', 'a.doc_approval_id')
                                    ->where('b.is_active', '1');
                            })
                            ->where('a.doc_approval_id', $docApprovalId)
                            ->where('b.employee_id', $employeeId)
                            ->first();

        // Update status and notify next approver
        // ... implementation details
    }
}
```

#### Data Flow
```
Approval Action → Status Update → Notification
├── Decision
│   ├── Approve
│   ├── Reject
│   └── Send Back
├── Comments
├── Timestamp
└── Next Approver
    └── Notification
```

## Document Creation and PDF Generation

### Document Types and Templates

The system supports multiple document types with specific templates:

1. **Order Form (Type 1)**
   - Template: `F.PUR-02.01_ORDER_FORM_FINAL.xlsx`
   - Variations based on signers and row count
   - Supports up to 25 rows of items

2. **Application & PO Form (Type 2)**
   - Templates: 
     - `F.PUR-02.02-04_APPLICATION_FINAL.xlsx`
     - `F.PUR-02.02-04_PO_NEW.xlsx`
   - Includes company header based on application type
   - Combines application and PO into single PDF

3. **Comparison Form (Type 6)**
   - Template: `TEMPLATE_COMPARISON_FORM.xlsx`
   - Landscape orientation
   - Supports vendor comparison data

4. **Inspection Form (Type 4)**
   - Template: `F.PUR-02.02-04_INSPECTION_FINAL.xlsx`
   - Includes delivery and inspection details

### PDF Generation Process

#### 1. Template Processing

```php
// Load and configure spreadsheet
$spreadsheet = IOFactory::load(Storage::path($templatePath));
$spreadsheet->getDefaultStyle()->getFont()->setName('arial');
$spreadsheet->getDefaultStyle()->getAlignment()->setWrapText(true);

// Configure page settings
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->getPageSetup()
    ->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
    ->setPaperSize(PageSetup::PAPERSIZE_A4)
    ->setFitToPage(true)
    ->setFitToWidth(1)
    ->setFitToHeight(1);

// Set margins
$worksheet->getPageMargins()
    ->setTop(0.2)
    ->setLeft(0.2)
    ->setRight(0.2)
    ->setBottom(0.2);
```

#### 2. Data Population

```php
// Populate document data
$worksheet->setCellValue('E5', ': ' . $dataForm['docNumber']);
$worksheet->setCellValue('E6', ': ' . $dataForm['requestDate']);
$worksheet->setCellValue('Y5', ': ' . $dataForm['departmentName']);
$worksheet->setCellValue('Y6', ': ' . $dataForm['locationName']);

// Handle special formatting
if($dataForm['paymentType'] == 'TRANSFER') {
    $worksheet->setCellValue('U60', 'X');
    $worksheet->setCellValue('Z60', '');
} else {
    $worksheet->setCellValue('U60', '');
    $worksheet->setCellValue('Z60', 'X');
}
```

#### 3. PDF Conversion

```php
// Configure PDF settings
$mpdf = new MpdfLibrary([
    'tempDir' => storage_path('app/temp'),
    'fontDir' => [storage_path('fonts')],
    'fontdata' => [
        'arial' => [
            'R' => 'arial.ttf',    // Regular
            'B' => 'arial_Bold.ttf',   // Bold
            'I' => 'arial_Italic.ttf',   // Italic
            'BI' => 'arial_Bold_Italic.ttf', // Bold Italic
        ],
    ],
    'default_font' => 'arial'
]);

// Add header for specific document types
$headerHtml = '
<div style="text-align: center;">
    <img src="' . $headerPath . '" style="width: 100%; height: 14mm; margin-top: 5mm; margin-left: 10mm; margin-right: 10mm; margin-bottom: 2mm" />
</div>';
$mpdf->SetHTMLHeader($headerHtml);

// Convert and save
$pdfWriter = new Mpdf($spreadsheet);
$pdfWriter->setFont('arial');
$pdfWriter->save($tempPdfFile);
```

#### 4. Document Combination (for Application & PO)

```php
// Combine multiple PDFs
$combinedPdfFile = Storage::path($filePath . $dataForm['filename'] . '.pdf');
$pdf = new Fpdi();

// Add PO pages
$pageCount1 = $pdf->setSourceFile($tempPdfFile2);
for ($i = 1; $i <= $pageCount1; $i++) {
    $template = $pdf->importPage($i);
    $pdf->AddPage();
    $pdf->useTemplate($template);
}

// Add Application pages
$pageCount2 = $pdf->setSourceFile($tempPdfFile1);
for ($i = 1; $i <= $pageCount2; $i++) {
    $template = $pdf->importPage($i);
    $pdf->AddPage();
    $pdf->useTemplate($template);
}

$pdf->Output($combinedPdfFile, 'F');
```

### File Storage and Management

1. **Storage Structure**
   ```
   private/
   ├── template/
   │   ├── F.PUR-02.01_ORDER_FORM_FINAL.xlsx
   │   ├── F.PUR-02.02-04_APPLICATION_FINAL.xlsx
   │   └── ...
   ├── doc_approval/
   │   └── [year]/
   │       └── [document_type]/
   │           └── [document_number]/
   │               ├── [filename].xlsx
   │               └── [filename].pdf
   └── fonts/
       ├── arial.ttf
       ├── arial_Bold.ttf
       └── ...
   ```

2. **File Naming Convention**
   - Order Form: `order_form_[number].pdf`
   - Application: `[number]_app.pdf`
   - PO: `[number]_po.pdf`
   - Combined: `[number].pdf`

### Asynchronous Processing

For better performance, document generation is handled asynchronously:

```php
// Queue PDF generation job
GeneratePdfJob::dispatch('4', $dataForm->toArray());
```

### Security Considerations

1. **File Access Control**
   - Files stored in private storage
   - Access controlled through tokens
   - Temporary files cleaned up after processing

2. **Input Validation**
   - File type restrictions
   - Size limits
   - Content validation

3. **Output Security**
   - PDF encryption options
   - Watermarking capabilities
   - Access tracking

## QR Code Implementation

### Overview

The system implements QR codes in the PO template (`F.PUR-02.02-04_PO_NEW.xlsx`) to provide quick access to the document. The QR code is placed in the range `U59:AH68` and contains a shortened URL that links to the document. The system uses a custom URL shortener implementation optimized for internal use with permanent URLs and high volume support.

### URL Shortening Examples

The system transforms long document URLs into shorter, more manageable URLs. Here are some examples:

1. **Purchase Order Document**
   ```
   Original URL:
   https://app.logisteed.id/proc_pur/view?token=eyJpZCI6IjEyMzQ1IiwidHlwZSI6InBvIiwic3RhdHVzIjoiYWN0aXZlIn0&cid=789&doc=PO-2024-001234

   Shortened URL:
   https://app.logisteed.id/s/aB3cD9
   ```

2. **Application Form**
   ```
   Original URL:
   https://app.logisteed.id/proc_pur/view?token=eyJpZCI6IjY3ODkwIiwidHlwZSI6ImFwcCIsInN0YXR1cyI6ImFjdGl2ZSJ9&cid=789&doc=APP-2024-005678

   Shortened URL:
   https://app.logisteed.id/s/xY7zW2
   ```

3. **Comparison Form**
   ```
   Original URL:
   https://app.logisteed.id/proc_pur/view?token=eyJpZCI6IjkwMTIzIiwidHlwZSI6ImNvbXAiLCJzdGF0dXMiOiJhY3RpdmUifQ&cid=789&doc=COMP-2024-009012

   Shortened URL:
   https://app.logisteed.id/s/mN5pQ8
   ```

4. **Inspection Form**
   ```
   Original URL:
   https://app.logisteed.id/proc_pur/view?token=eyJpZCI6IjQ1Njc4IiwidHlwZSI6Imluc3AiLCJzdGF0dXMiOiJhY3RpdmUifQ&cid=789&doc=INS-2024-004567

   Shortened URL:
   https://app.logisteed.id/s/kL9vB4
   ```

**Note**: 
- The shortened URLs are permanent and will always redirect to the original document
- The same document will always get the same shortened URL (deduplication)
- The short codes are 6 characters long, using a combination of numbers and letters (0-9, a-z, A-Z)
- All shortened URLs follow the pattern: `https://app.logisteed.id/s/{6-character-code}`

### Implementation Steps

1. **Custom URL Shortener Service**
   ```php
   // Create a new service class: App/Services/UrlShortenerService.php
   class UrlShortenerService {
       private $model;
       private $baseUrl;
       private $shortCodeLength = 6; // Reduced length for higher capacity
       private $maxRetries = 3;
       
       public function __construct() {
           $this->model = new ShortenedUrl();
           $this->baseUrl = config('app.url') . '/s/';
       }
       
       public function shortenUrl($longUrl) {
           // Check if URL already exists
           $existing = $this->model->where('long_url', $longUrl)->first();
           if ($existing) {
               return $this->baseUrl . $existing->short_code;
           }
           
           // Generate unique short code with retry mechanism
           $attempts = 0;
           do {
               $shortCode = $this->generateShortCode();
               $exists = $this->model->where('short_code', $shortCode)->exists();
               $attempts++;
           } while ($exists && $attempts < $this->maxRetries);
           
           if ($attempts >= $this->maxRetries) {
               throw new \Exception('Unable to generate unique short code after ' . $this->maxRetries . ' attempts');
           }
           
           // Create shortened URL record
           $shortenedUrl = $this->model->create([
               'short_code' => $shortCode,
               'long_url' => $longUrl,
               'created_by' => auth()->id(),
               'access_count' => 0,
               'last_accessed_at' => null
           ]);
           
           return $this->baseUrl . $shortCode;
       }
       
       private function generateShortCode() {
           // Using base62 encoding for maximum character set (0-9, a-z, A-Z)
           $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
           $code = '';
           
           // Generate 6-character code (62^6 = 56,800,235,584 possible combinations)
           for ($i = 0; $i < $this->shortCodeLength; $i++) {
               $code .= $characters[random_int(0, strlen($characters) - 1)];
           }
           
           return $code;
       }
       
       public function getLongUrl($shortCode) {
           return Cache::remember("short_url:{$shortCode}", now()->addDays(7), function() use ($shortCode) {
               $shortenedUrl = $this->model->where('short_code', $shortCode)->first();
               
               if ($shortenedUrl) {
                   // Update access statistics
                   $shortenedUrl->increment('access_count');
                   $shortenedUrl->update(['last_accessed_at' => now()]);
                   return $shortenedUrl->long_url;
               }
               
               return null;
           });
       }
   }
   ```

2. **Database Migration**
   ```php
   // Create new migration: database/migrations/2024_03_xx_create_shortened_urls_table.php
   public function up() {
       Schema::create('shortened_urls', function (Blueprint $table) {
           $table->id();
           $table->string('short_code', 6)->unique(); // Reduced to 6 characters
           $table->text('long_url');
           $table->unsignedBigInteger('created_by');
           $table->unsignedBigInteger('access_count')->default(0);
           $table->timestamp('last_accessed_at')->nullable();
           $table->timestamps();
           
           // Optimized indexes
           $table->index('short_code', 'short_code_idx');
           $table->index('long_url', 'long_url_idx', 'hash'); // Using hash index for text column
           $table->index('last_accessed_at', 'last_accessed_idx');
           $table->foreign('created_by')->references('id')->on('users');
       });
   }
   ```

3. **Model Definition**
   ```php
   // Create new model: App/Models/ShortenedUrl.php
   class ShortenedUrl extends Model {
       protected $fillable = [
           'short_code',
           'long_url',
           'created_by',
           'access_count',
           'last_accessed_at'
       ];
       
       protected $casts = [
           'access_count' => 'integer',
           'last_accessed_at' => 'datetime'
       ];
       
       // Optimize for high-volume reads
       protected $table = 'shortened_urls';
       public $timestamps = true;
       public $incrementing = true;
   }
   ```

4. **Route and Controller**
   ```php
   // Add to routes/web.php
   Route::get('s/{shortCode}', [UrlController::class, 'redirect'])
        ->name('shortened.redirect')
        ->where('shortCode', '[a-zA-Z0-9]{6}'); // Validate short code format
   
   // Create new controller: App/Http/Controllers/UrlController.php
   class UrlController extends Controller {
       private $urlShortener;
       
       public function __construct(UrlShortenerService $urlShortener) {
           $this->urlShortener = $urlShortener;
       }
       
       public function redirect($shortCode) {
           try {
               $longUrl = $this->urlShortener->getLongUrl($shortCode);
               
               if (!$longUrl) {
                   abort(404, 'URL not found');
               }
               
               return redirect($longUrl);
           } catch (\Exception $e) {
               Log::error('URL redirect failed: ' . $e->getMessage(), [
                   'short_code' => $shortCode,
                   'error' => $e->getMessage()
               ]);
               abort(500, 'Internal server error');
           }
       }
   }
   ```

5. **QR Code Generation**
   ```php
   // Create a new service class: App/Services/QrCodeService.php
   use SimpleSoftwareIO\QrCode\Facades\QrCode;
   
   class QrCodeService {
       private $urlShortener;
       
       public function __construct(UrlShortenerService $urlShortener) {
           $this->urlShortener = $urlShortener;
       }
       
       public function generateQrCode($longUrl, $size = 200) {
           // Generate permanent shortened URL
           $shortUrl = $this->urlShortener->shortenUrl($longUrl);
           
           return QrCode::format('png')
                       ->size($size)
                       ->errorCorrection('H')
                       ->generate($shortUrl);
       }
       
       public function saveQrCode($qrCode, $path) {
           Storage::put($path, $qrCode);
           return $path;
       }
   }
   ```

6. **Integration with Document Generation**
   ```php
   // In DocumentApprovalController.php
   private function addQrCodeToPo($worksheet, $documentUrl) {
       // Initialize services
       $qrCodeService = app(QrCodeService::class);
       
       // Generate QR code with permanent shortened URL
       $qrCode = $qrCodeService->generateQrCode($documentUrl);
       
       // Save QR code temporarily
       $qrCodePath = 'temp/qrcodes/' . uniqid() . '.png';
       $qrCodeService->saveQrCode($qrCode, $qrCodePath);
       
       // Add QR code to worksheet
       $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
       $drawing->setName('QR Code');
       $drawing->setPath(Storage::path($qrCodePath));
       $drawing->setCoordinates('U59');
       $drawing->setWidth(200);
       $drawing->setHeight(200);
       $drawing->setWorksheet($worksheet);
       
       // Clean up temporary file
       Storage::delete($qrCodePath);
       
       // Add URL text below QR code
       $worksheet->setCellValue('U68', 'Scan to view document');
       $worksheet->getStyle('U68')->getFont()->setSize(8);
   }
   ```

### Performance Optimizations

1. **Database Optimizations**
   - Reduced short code length to 6 characters (still supports 56.8 billion unique codes)
   - Hash index on long_url for faster lookups
   - Optimized indexes for common queries
   - Efficient storage of access statistics

2. **Caching Strategy**
   ```php
   // In UrlShortenerService.php
   public function getLongUrl($shortCode) {
       return Cache::remember("short_url:{$shortCode}", now()->addDays(7), function() use ($shortCode) {
           $shortenedUrl = $this->model->where('short_code', $shortCode)->first();
           
           if ($shortenedUrl) {
               // Update access statistics asynchronously
               dispatch(function() use ($shortenedUrl) {
                   $shortenedUrl->increment('access_count');
                   $shortenedUrl->update(['last_accessed_at' => now()]);
               })->afterResponse();
               
               return $shortenedUrl->long_url;
           }
           
           return null;
       });
   }
   ```

3. **High Volume Considerations**
   - Asynchronous access count updates
   - Efficient index usage
   - Optimized storage
   - No expiration cleanup needed
   - Deduplication of URLs

### Monitoring and Statistics

1. **Access Tracking**
   ```php
   // Create new command: App/Console/Commands/UrlStats.php
   class UrlStats extends Command {
       protected $signature = 'urls:stats';
       
       public function handle() {
           $stats = ShortenedUrl::select([
               DB::raw('COUNT(*) as total_urls'),
               DB::raw('SUM(access_count) as total_accesses'),
               DB::raw('AVG(access_count) as avg_accesses'),
               DB::raw('MAX(access_count) as max_accesses'),
               DB::raw('COUNT(CASE WHEN access_count > 0 THEN 1 END) as active_urls')
           ])->first();
           
           $this->info('URL Statistics:');
           $this->info('Total URLs: ' . $stats->total_urls);
           $this->info('Total Accesses: ' . $stats->total_accesses);
           $this->info('Average Accesses: ' . round($stats->avg_accesses, 2));
           $this->info('Max Accesses: ' . $stats->max_accesses);
           $this->info('Active URLs: ' . $stats->active_urls);
       }
   }
   ```

2. **Popular URLs Report**
   ```php
   // Create new command: App/Console/Commands/PopularUrls.php
   class PopularUrls extends Command {
       protected $signature = 'urls:popular {--limit=10}';
       
       public function handle() {
           $urls = ShortenedUrl::select('short_code', 'long_url', 'access_count', 'last_accessed_at')
                              ->orderBy('access_count', 'desc')
                              ->limit($this->option('limit'))
                              ->get();
           
           $this->info('Most Accessed URLs:');
           foreach ($urls as $url) {
               $this->info(sprintf(
                   "Code: %s, Accesses: %d, Last Access: %s",
                   $url->short_code,
                   $url->access_count,
                   $url->last_accessed_at?->format('Y-m-d H:i:s') ?? 'Never'
               ));
           }
       }
   }
   ```

### Security Considerations

1. **URL Validation**
   - Validate long URLs before shortening
   - Restrict to internal domains
   - Sanitize input
   - Rate limiting on creation

2. **Access Control**
   - Track access patterns
   - Monitor for abuse
   - Log access attempts
   - IP-based rate limiting

3. **Data Protection**
   - Secure storage of URLs
   - Access logging
   - Audit trail
   - Regular security reviews

## Notification System

### Notification Types

1. **Document Approval Notifications**
   - `NEED_APPROVAL`: Sent to approvers when a document requires their review
   - `NOTIFICATION_APPROVED`: Sent to applicant when document is approved
   - `COMPLETED_REJECTED`: Sent when document is rejected
   - `COMPLETED_REVISED`: Sent when document needs revision
   - `NEXT_FORM_FLOW`: Sent when approved document moves to next stage
   - `INSPECTION_FORM_SENT`: Sent when inspection form is generated

2. **Reminder Notifications**
   - `APPROVER_ONGOING`: Reminder for pending approvals
   - `BATCH_APPROVAL_REQUEST`: Scheduled reminders for approval requests

### Notification Flow

#### 1. Document Creation and Submission

```php
// When document is submitted
$arrNotifications[$docApprovalId][] = [
    'docApprovalId' => $docApprovalId,
    'referenceId' => $referenceId,
    'signFlowId' => $insertFlowSign->id,
    'companyId' => $companyId,
    'employeeId' => $request->approver[$x],
    'flowAs' => 'APPROVER',
    'notification' => 'NEED_APPROVAL'
];

// Send notifications
$requestData = new Request();
$requestData->merge(['notifications' => $arrNotifications]);
$this->sendEmail($requestData);
```

#### 2. Approval Status Updates

```php
// Status mapping for notifications
$statusMapping = [
    'COMPLETED_APPROVED' => 'APPROVED',
    'NOTIFICATION_APPROVED' => 'APPROVED',
    'COMPLETED_REJECTED' => 'REJECTED',
    'COMPLETED_REVISED' => 'SEND BACK TO REVISE',
];

// Notify applicant of status change
$arrNotifications[$docApprovalId][] = [
    'docApprovalId' => $docApprovalId,
    'referenceId' => $getForm->document_id,
    'signFlowId' => $rowApplicantSigner->sign_flow_id,
    'employeeId' => $rowApplicantSigner->employee_id,
    'companyId' => $rowApplicantSigner->company_id,
    'flowAs' => $rowApplicantSigner->flow_as,
    'notification' => $applicantNotification
];
```