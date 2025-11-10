<?php

namespace App\Http\Controllers;

use App\Services\SafeToken;
use Carbon\Carbon;
use Dompdf\Dompdf;
// use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Hfig\MAPI\MAPI;
use Hfig\MAPI\MapiMessageFactory;
use Hfig\MAPI\OLE\Pear\DocumentFactory;
use Hfig\MAPI\Message\Message;
use HTMLPurifier;
use HTMLPurifier_Config;

require_once base_path('vendor/iio/libmergepdf/tcpdi/tcpdi.php');
use TCPDI;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Driver\TcpdiDriver;

use Illuminate\Support\Facades\Response as FacadesResponse;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Modules\DocumentApproval\Models\DocumentApprovalModel;
use Mpdf\Mpdf;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;

// use Google\Client;
// use Google\Service\Drive;
// use Google\Service\Docs;

class RenderController extends Controller
{

    public function framePdf(Request $request){
        return view('pdf.pdf_render');
    }

    public function render(Request $request){
        $encodedToken = $request->token;
        $response = collect();
        $response->put('frameSrc', '/framePdf?page=1&zoom=100&token='.$encodedToken);

        // $encodedTokenFromUrl = str_pad(strtr($encodedToken, '-_', '+/'), strlen($encodedToken) % 4, '=');
        // $encryptedToken = base64_decode($encodedTokenFromUrl);
        // $decryptedToken = Crypt::decryptString($encryptedToken);
        // $token = json_decode($decryptedToken, true);
        // $module = $token['a'];
        // $id = $token['b'];

        // $response = collect();
        // if($module == 'DOC_APPROVAL'){
        //     $getAttachments = DB::table('doc_approval_attachment')
        //                     ->select('path_detail', 'mime_type', 'filename', 'converted', 'filename_converted')
        //                     ->where('attachment_id', $id)
        //                     ->where('is_active', '1')
        //                     ->first();

        //     if($getAttachments){
        //         $mimeType = $getAttachments->mime_type;
        //         if($getAttachments->converted == '1'){
        //             $fileName = $getAttachments->filename_converted;
        //         }
        //         else{
        //             $fileName = $getAttachments->filename;
        //         }
        //         // $fileName = $getAttachments->filename;
        //         $checkExtension = function (array $extensions) use ($fileName) {
        //             return collect($extensions)->contains(function ($ext) use ($fileName) {
        //                 return str_ends_with(strtolower($fileName), $ext);
        //             });
        //         };

        //         if (str_contains($mimeType, 'pdf') || $getAttachments->converted == '1') {
        //             $response->put('frameSrc', '/framePdf?token='.$encodedToken);
        //         }
        //         elseif (str_contains($mimeType, 'image')) {
        //             $response->put('frameSrc', '/framePdf?token='.$encodedToken);
        //         }
        //         else if (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheetml') || str_contains($mimeType, 'wordprocessingml')) {
        //             $path = '/doc_approval' . $getAttachments->path_detail . $getAttachments->filename;
        //             $url = Storage::disk('private')->path($path);
        //             $response->put('frameSrc', 'https://view.officeapps.live.com/op/embed.aspx?src='. urlencode($url).'&embedded=true');
        //         }
        //         // else if (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheetml') || str_contains($mimeType, 'wordprocessingml')) {
        //         //     // $response->put('frameSrc', '/framePdf?token='.$encodedToken);
        //         //     // $file = $request->file('file');
        //         //     // $filePath = $file->storeAs('uploads', $file->getClientOriginalName());

        //         //     $path = '/doc_approval' . $getAttachments->path_detail . $getAttachments->filename;
        //         //     $filePath = Storage::disk('private')->path($path);
        //         //     // Path ke file Service Account JSON
        //         //     $credentialsPath = Storage::disk('private')->path('/credentials/gdriveandgdocs-f430cc31ae1e.json');

        //         //     $client = new Client();
        //         //     $client->setAuthConfig($credentialsPath);
        //         //     $client->addScope(Drive::DRIVE);
        //         //     $client->addScope(Drive::DRIVE_FILE);

        //         //     // Inisialisasi Google Drive API
        //         //     $driveService = new Drive($client);
        //         //     $folderId = '1f3twaggwvcu8nJP6FrimmIkZd0AEj1l4'; // Ganti dengan ID folder Anda

        //         //     // Upload file .doc atau .docx ke Google Drive dan konversi ke Google Docs
        //         //     $fileMetadata = new Drive\DriveFile([
        //         //         'name' => $getAttachments->filename,
        //         //         'mimeType' => 'application/vnd.google-apps.document', // Konversi ke Google Docs
        //         //         'parents' => [$folderId] // Menyimpan di dalam folder
        //         //     ]);
        //         //     $content = file_get_contents($filePath);
        //         //     $uploadedFile = $driveService->files->create($fileMetadata, [
        //         //         'data' => $content,
        //         //         'mimeType' => $mimeType,
        //         //         'uploadType' => 'multipart',
        //         //     ]);

        //         //     // Ekspor file yang dikonversi ke format PDF
        //         //     $pdfFileContent = $driveService->files->export($uploadedFile->id, 'application/pdf', ['alt' => 'media']);

        //         //     // Simpan PDF yang dihasilkan ke storage Laravel
        //         //     $pdfFileName = pathinfo($getAttachments->filename, PATHINFO_FILENAME) . '.pdf';
        //         //     $pdfPath = 'converted/' . $pdfFileName;

        //         //     if (Storage::put($pdfPath, $pdfFileContent->getBody()->getContents())) {
        //         //         // Hapus file dari Google Drive jika penyimpanan berhasil
        //         //         try {
        //         //             $driveService->files->delete($uploadedFile->id);
        //         //         } catch (Exception $e) {
        //         //             echo 'Gagal menghapus file: ' . $e->getMessage();
        //         //         }
        //         //     } else {
        //         //         echo "Gagal menyimpan file PDF.";
        //         //     }
        //         // }
        //         else if (
        //             str_contains($mimeType, 'ms-outlook') ||
        //             str_contains($mimeType, 'ms-tnef') ||
        //             $mimeType === 'message/rfc822' ||
        //             str_contains($mimeType, 'vnd.ms-outlook') ||
        //             $checkExtension(['.msg', '.eml'])
        //         ) {

        //             dd($encodedToken);
        //             $response->put('frameSrc', '/framePdf?token='.$encodedToken);
        //         }
        //     }

        // }

        if ($response->isNotEmpty()) {
            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'data' => $response,
            ], 200);
        }
        else{
            return response()->json([
                'status' => 404,
                'message' => 'File Not Found',
                'data' => $response,
            ], 200);
        }
    }

    public function validate(Request $request) {
        $requestToken = SafeToken::decode($request->token);
        $docApprovalId = $requestToken['i'];
        $documentTypeId = $requestToken['d'];
        if($documentTypeId == '2') { // 2 = APPLICATION PO FORM
            $token = SafeToken::encode([
                                        'a' => 'PO_FORM',
                                        'b' => $docApprovalId,
            ]);
        }
        return view('document_validate', compact('token'));
    }

    public function renderPdf(Request $request) {
        $allowedReferers = [
            'http://127.0.0.1:8000',
            'https://app.bml-log.com',
            'https://app.logisteed.id',
        ];

        $referer = $request->headers->get('referer');
        $userAgent = $request->headers->get('user-agent');

        if (empty($referer) || stripos($userAgent, 'Mozilla') === false) {
            return response()->json(['status' => 403,
                                    'message' => 'Direct access is not allowed'],
                                Response::HTTP_FORBIDDEN);
        }
        else if (!$this->isValidReferer($referer, $allowedReferers)) {
            return response()->json(['status' => 403,
                                    'error' => 'Access denied.'],
                                    Response::HTTP_FORBIDDEN);
        }

        // try {
            // dd($request->all());
            $token = SafeToken::decode($request->token);
            $module = $token['a'];
            $id = $token['b'];
            if ($module == 'PO_FORM') {
                $getHeader = DB::table('doc_approval_header')
                            ->select('doc_type_id', 'doc_approval_id')
                            ->where('doc_approval_id', $id)
                            ->where('is_active', '1')
                            ->first();
                if($getHeader) {
                    $getForm = DB::table('doc_approval_order_application')
                                        ->select('path_detail', 'filename')
                                        ->where('doc_approval_id', $getHeader->doc_approval_id)
                                        // ->where('application_form_status', '8') // 8 = APPROVED
                                        ->where('is_active', '1')
                                        ->orderBy('application_id', 'desc')
                                        ->first();
                    if($getForm) {
                        $path = '/doc_approval'.$getForm->path_detail.$getForm->filename.'_po.pdf';
                        $tempPath = 'temp/temp_'.$getForm->filename.'_'.uniqid().'_po.pdf';
                        Storage::disk('private')->copy($path, $tempPath);

                        return new StreamedResponse(function () use ($tempPath) {
                            $stream = Storage::disk('private')->readStream($tempPath);
                            fpassthru($stream);
                            fclose($stream);
                            Storage::disk('private')->delete($tempPath);
                        }, 200, [
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => 'inline; filename="'.$getForm->filename.'.pdf"',
                        ]);
                    }
                    else {
                        return response()->json(['error' => '404 Not found c'], Response::HTTP_BAD_REQUEST);
                    }
                }
            }
            else if ($module == 'DOC_APPROVAL') {
                $getAttachments = DB::table('doc_approval_attachment')
                    ->select('path_detail', 'mime_type', 'filename', 'converted', 'filename_converted')
                    ->where('attachment_id', $id)
                    ->where('is_active', '1')
                    ->first();

                if ($getAttachments) {
                    // $path = 'uploads/doc_approval/' . $getAttachments->path_detail . $getAttachments->filename;
                    // // Check if the file exists
                    // if (Storage::disk('private')->exists($path)) {
                    //     // Return the PDF response with correct headers
                    //     return Storage::disk('private')->response($path, $getAttachments->filename, [
                    //         'Content-Type' => 'application/pdf',
                    //     ]);
                    // } else {
                    //     return response()->json(['error' => 'File not found'], Response::HTTP_NOT_FOUND);
                    // }

                    $mimeType = $getAttachments->mime_type;
                    if($getAttachments->converted == '1'){
                        $fileName = $getAttachments->filename_converted;
                    }
                    else{
                        $fileName = $getAttachments->filename;
                    }

                    $checkExtension = function (array $extensions) use ($fileName) {
                        return collect($extensions)->contains(function ($ext) use ($fileName) {
                            return str_ends_with(strtolower($fileName), $ext);
                        });
                    };

                    // if($getAttachments->converted == '1'){
                    //     $path = '/doc_approval' . $getAttachments->path_detail . $getAttachments->filename_converted;
                    // }
                    // else {
                    //     $path = '/doc_approval' . $getAttachments->path_detail . $getAttachments->filename;
                    // }

                    $path = '/doc_approval'.$getAttachments->path_detail.$fileName;
                    // dd($path);
                    if (!Storage::disk('private')->exists($path)) {
                        return response()->json(['error' => 'File not found'], 404);
                    }

                    if (str_contains($mimeType, 'pdf') || $getAttachments->converted == '1') {
                        return new StreamedResponse(function () use ($path) {
                            $stream = Storage::disk('private')->readStream($path);
                            fpassthru($stream);
                            fclose($stream);
                        }, 200, [
                            'Content-Type' => $getAttachments->mime_type,
                            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
                        ]);
                    }
                    else if (str_contains($mimeType, 'image')) {
                        $filePath = Storage::disk('private')->path($path);
                        $manager = new ImageManager(new Driver());
                        $image = $manager->read($filePath);

                        $widthPx = $image->width();
                        $heightPx = $image->height();
                        $orientation = $widthPx > $heightPx ? 'L' : 'P';

                        $tempImage = Storage::disk('public')->path('temp_img_'.time().'.jpg');
                        $image->save($tempImage, 90);

                        $mpdf = new Mpdf([
                            'mode' => 'utf-8',
                            'format' => $orientation === 'P' ? 'A4' : 'A4-L',
                            'dpi' => 150,
                            'img_dpi' => 150,
                        ]);

                        $mpdf->AddPage($orientation);

                        // Hitung dimensi halaman dalam mm
                        $pageWidth = $orientation === 'P' ? 210 : 297;
                        $pageHeight = $orientation === 'P' ? 297 : 210;
                        $mpdf->Image($tempImage, 0, 0, $pageWidth, $pageHeight, 'JPEG', '', true, false);
                        unlink($tempImage);

                        $pdfFilename = 'image_pdf_' . time() . '.pdf';
                        $pdfPath = Storage::disk('public')->path($pdfFilename);
                        $mpdf->SetCompression(true);
                        $mpdf->Output($pdfPath, 'F');

                        return response()->download($pdfPath)->deleteFileAfterSend();
                    }
                    else if (str_contains($mimeType, 'wordprocessingml')) {
                        $filePath = Storage::disk('private')->path($path);
                        // $phpWord = WordIOFactory::load($filePath);
                        // $pdfWriter = new \PhpOffice\PhpWord\Writer\PDF\MPDF($phpWord);

                        $pdfFilename = 'word_pdf_' . time() . '.pdf';
                        $pdfPath = Storage::disk('public')->path($pdfFilename);
                        // $pdfWriter->save($pdfPath);

                        $domPdfPath = base_path('vendor/dompdf/dompdf');

                        \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
                        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
                        $Content = \PhpOffice\PhpWord\IOFactory::load($filePath);
                        $PDFWriter = \PhpOffice\PhpWord\IOFactory::createWriter($Content,'PDF');

                        $pdfFileName = time().'.pdf';
                        $PDFWriter->save($pdfPath);
                    }
                    else if (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheetml')) {

                    }
                    else if (
                        str_contains($mimeType, 'ms-outlook') ||
                        str_contains($mimeType, 'ms-tnef') ||
                        $mimeType === 'message/rfc822' ||
                        str_contains($mimeType, 'vnd.ms-outlook') ||
                        $checkExtension(['.msg', '.eml'])
                    ) {
                        $filePath = Storage::disk('private')->path($path);
                        // dd($filePath);



                        $docFactory = new DocumentFactory();
                        $ole = $docFactory->createFromFile($filePath);
                        $messageFactory = new MapiMessageFactory();
                        $message = $messageFactory->parseMessage($ole);
                        $mpdf = new Mpdf([
                            'mode' => 'utf-8',
                            'format' => 'A4',
                            'margin_left' => 10,
                            'margin_right' => 10,
                            'margin_top' => 10,
                            'margin_bottom' => 10,
                            'margin_header' => 5,
                            'margin_footer' => 5,
                        ]);

                        $htmlContent = $this->getEmailContent($message);
                        // dd($htmlContent);


                        // $htmlContent = $message->getBodyHTML();
                        $chunks = str_split($htmlContent, 500000); // 500KB per chunk

                        foreach ($chunks as $chunk) {
                            $mpdf->WriteHTML($chunk, \Mpdf\HTMLParserMode::HTML_BODY);
                        }

                        // $htmlContent = $this->getEmailContent($message);
                        // $mpdf->WriteHTML($this->getEmailStyles(), \Mpdf\HTMLParserMode::HEADER_CSS);
                        // $mpdf->WriteHTML($message->getBodyHTML(), \Mpdf\HTMLParserMode::HTML_BODY);

                        $pdfFilename = 'email_pdf_' . time() . '.pdf';
                        $pdfPath = Storage::disk('public')->path($pdfFilename);
                        $mpdf->Output($pdfPath, 'F');

                        return response()->download($pdfPath)->deleteFileAfterSend();
                    }
                }
                else {
                    return response()->json(['error' => 'Attachment not found'], Response::HTTP_NOT_FOUND);
                }
            }
            else {
                // dd($token);
                $tokenA = $token['a'];
                $tokenB = $token['b'];
                $tokenC = $token['c'];
                $tokenD = array_key_exists('d', $token) ? $token['d'] : null;
                $groupId = array_key_exists('g', $token) ? $token['g'] : null;

                $getHeader = DB::table('doc_approval_header')
                            ->select('doc_type_id', 'doc_approval_id')
                            ->where('doc_approval_id', $tokenA)
                            ->where('is_active', '1')
                            ->first();
                    // dd($getHeader);
                if($getHeader) {
                    // dd($getHeader);
                    if($getHeader->doc_type_id == '1') { // 1 = ORDER FORM
                        // GET FORM
                        if($tokenC != null) {
                            // dd($tokenC);
                            // COMPARiSON FORM
                            $getForm = DB::table('doc_approval_order_comparison')
                                            ->select('path_detail', 'filename')
                                            ->where('doc_approval_id', $getHeader->doc_approval_id)
                                            ->where('order_form_id', $tokenB)
                                            ->where('comparison_id', $tokenC)
                                            ->where('is_active', '1')
                                            ->orderBy('comparison_id', 'desc')
                                            ->first();
                            // dd($getForm);
                        }
                        else {
                            $getForm = DB::table('doc_approval_detail_order_form')
                                            ->select('path_detail', 'filename');

                            if($tokenB != null) {
                                // IF REFERENCE ID VALUE IS SET, GET BY REFERENCE ID, IF NOT GET LATEST FORM
                                $getForm = $getForm->where('order_form_id', $tokenB);
                            }

                            $getForm = $getForm->where('doc_approval_id', $getHeader->doc_approval_id)
                                                ->where('is_active', '1')
                                                ->orderBy('order_form_id', 'desc')
                                                ->first();
                        }
                    }
                    else if($getHeader->doc_type_id == '2') { // APPLICATION FORM, PO FORM
                        // dd($token);
                        $getForm = DB::table('doc_approval_order_application')
                                        ->select('path_detail', 'filename')
                                        ->where('doc_approval_id', $getHeader->doc_approval_id)
                                        ->where('application_id', $tokenB)
                                        ->where('comparison_id', $tokenC)
                                        ->where('is_active', '1')
                                        ->first();
                    }
                    else if($getHeader->doc_type_id == '4') { // INSPECTION FORM
                        // dd($token);
                        $getForm = DB::table('doc_approval_order_inspection')
                                        ->select('path_detail', 'filename')
                                        ->where('doc_approval_id', $getHeader->doc_approval_id)
                                        ->where('inspection_id', $tokenB)
                                        // ->where('comparison_id', $tokenC)
                                        ->where('is_active', '1')
                                        ->first();
                    }
                    else if($getHeader->doc_type_id == '7') { // OTHER DOCUMENT
                        $getForm = DB::table('doc_approval_other')
                                            ->select('path_detail', 'filename')
                                            ->where('doc_approval_id', $getHeader->doc_approval_id)
                                            ->where('document_id', $tokenB)
                                            ->where('is_active', '1')
                                            ->first();
                    }

                    if($getForm) {
                        $path = '/doc_approval'.$getForm->path_detail.$getForm->filename.'.pdf';
                        $tempPath = 'temp/temp_'.$getForm->filename.'_'.uniqid().'.pdf';
                        Storage::disk('private')->copy($path, $tempPath);

                        return new StreamedResponse(function () use ($tempPath) {
                            $stream = Storage::disk('private')->readStream($tempPath);
                            fpassthru($stream);
                            fclose($stream);
                            Storage::disk('private')->delete($tempPath);
                        }, 200, [
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => 'inline; filename="'.$getForm->filename.'.pdf"',
                        ]);
                    }
                    else {
                        return response()->json(['error' => '404 Not found c'], Response::HTTP_BAD_REQUEST);
                    }
                }
                else {
                    return response()->json(['error' => $token], Response::HTTP_BAD_REQUEST);
                }
            }
        // }
        // catch (\Exception $e) {
        //     return response()->json(['error' => 'Invalid token or decryption failed'], Response::HTTP_BAD_REQUEST);
        // }
    }

    public function generatePdf(Request $request) {
        $actionType = $request->actionType;
        $module = $request->module;
        $response = array();
        if($module == 'DOCUMENT_APPROVAL') {
            $model = new DocumentApprovalModel();
        }

        $pdfFiles = [];
        foreach($request->rowTableCheckbox as $row) {
            $token = SafeToken::decode($row);
            $validator = Validator::make($token, [
                'id' => 'required',
                'a' => 'required',
                'b' => 'required',
            ]);

            $docApprovalId = $token['a'];
            $referenceId = $token['b'];

            if(array_key_exists('t', $token)){
                $docType = $token['t'];
                if($docType === 'INSPECTION_FORM') {
                    $getInspectionForm = $model->docApprovalOrderInspection()
                                            ->from('doc_approval_order_inspection')
                                            ->select('path_detail', 'filename')
                                            ->where('application_id', $token['a'])
                                            ->where('inspection_id', $token['c'])
                                            ->where('is_active', '1')
                                            ->first();
                    if($getInspectionForm) {
                        $pdfFiles[] = storage_path('app/private/doc_approval'.$getInspectionForm->path_detail.$getInspectionForm->filename.'.pdf');
                    }
                }
                else if($docType === 'PO_FORM') {
                    $getApplicationForm = $model->docApprovalOrderApplication()
                                                ->from('doc_approval_order_application as a')
                                                ->where('a.doc_approval_id', $docApprovalId)
                                                ->where('a.application_id', $referenceId)
                                                ->where('a.is_active', '1')
                                                ->select('a.path_detail', 'a.filename', 'a.application_number')
                                                ->first();
                    if($getApplicationForm) {
                        $pdfFiles[] = storage_path('app/private/doc_approval'.$getApplicationForm->path_detail.$getApplicationForm->filename.'_po.pdf');
                    }
                }
                else if($docType === 'APPLICATION_FORM') {
                    $getApplicationForm = $model->docApprovalOrderApplication()
                                                ->from('doc_approval_order_application as a')
                                                ->where('a.doc_approval_id', $docApprovalId)
                                                ->where('a.application_id', $referenceId)
                                                ->where('a.is_active', '1')
                                                ->select('a.path_detail', 'a.filename', 'a.application_number')
                                                ->first();
                    if($getApplicationForm) {
                        $pdfFiles[] = storage_path('app/private/doc_approval'.$getApplicationForm->path_detail.$getApplicationForm->filename.'_app.pdf');
                    }
                }
                else if($docType === 'COMPARISON_FORM') {
                    $comparisonId = $token['c'];
                    $getComparisonForm = $model->docApprovalOrderComparison()
                                        ->from('doc_approval_order_comparison')
                                        ->select('path_detail', 'filename')
                                        ->where('order_form_id', $referenceId)
                                        ->where('comparison_id', $comparisonId)
                                        ->where('is_active', '1')
                                        ->first();
                    if ($getComparisonForm) {
                        $pdfFiles[] = storage_path('app/private/doc_approval'.$getComparisonForm->path_detail.$getComparisonForm->filename.'.pdf');
                    }
                }
                else if($docType === 'ORDER_FORM') {
                    $getOrderForm = $model->documentApprovalDetailOrder()
                                        ->from('doc_approval_detail_order_form as a')
                                        ->select('a.path_detail', 'a.filename')
                                        ->where('a.doc_approval_id', $docApprovalId)
                                        ->where('a.order_form_id', $referenceId)
                                        ->where('a.is_active', '1')
                                        ->first();
                    if($getOrderForm) {
                        $pdfFiles[] = storage_path('app/private/doc_approval'.$getOrderForm->path_detail.$getOrderForm->filename.'.pdf');
                    }
                }
                else if($docType === 'APPLICATION_FORM_ATTACHMENT' || $docType == 'COMPARISON_FORM_ATTACHMENT' || $docType === 'ORDER_FORM_ATTACHMENT' || $docType === 'INSPECTION_ATTACHMENT' || $docType === 'INVOICE_ATTACHMENT') {
                    $attachmentId = $token['c'];
                    $getAttachment = $model->docApprovalAttachment()
                                            ->select('path_detail', 'filename', 'converted', 'filename_converted')
                                            ->where('doc_approval_id', $docApprovalId)
                                            ->where('reference_id', $referenceId)
                                            ->where('attachment_id', $attachmentId)
                                            ->first();
                    if($getAttachment) {
                        $attachmentFileName = $getAttachment->filename;
                        if($getAttachment->converted == '1') {
                            $attachmentFileName = $getAttachment->filename_converted;
                        }
                        $pdfFiles[] = storage_path('app/private/doc_approval'.$getAttachment->path_detail.$attachmentFileName);
                    }
                }
            }
        }

        if(count($pdfFiles) > 0) {
            // $pdf = new TCPDI(); // TCPDI instance tanpa konfigurasi default
            // foreach ($pdfFiles as $file) {
            //     $pdfdata = file_get_contents($file);
            //     $pagecount = $pdf->setSourceData($pdfdata);

            //     for ($i = 1; $i <= $pagecount; $i++) {
            //         $tplidx = $pdf->importPage($i);
            //         $size = $pdf->getTemplateSize($tplidx); // Ambil ukuran asli halaman

            //         // Tentukan orientasi: Lebar lebih besar dari tinggi -> Landscape
            //         $orientation = ($size['w'] > $size['h']) ? 'L' : 'P';

            //         // Buat halaman baru dengan ukuran dan orientasi yang sesuai
            //         $pdf->AddPage($orientation, [$size['w'], $size['h']]);

            //         // Set margin ke nol agar tidak ada perubahan posisi konten
            //         $pdf->SetMargins(0, 0, 0);
            //         $pdf->SetAutoPageBreak(false);

            //         // Gunakan template (halaman dari PDF asli)
            //         $pdf->useTemplate($tplidx);
            //     }
            // }
            // $fileName = 'merged_test.pdf';
            // $pdf->Output(storage_path('app/temp/'.$fileName), 'F');

            /////////////////////
            $fileName = 'merged_'.time().'.pdf';
            $tempPath = storage_path("app/temp/$fileName");
            Storage::delete("temp/$fileName");

            foreach ($pdfFiles as $file) {
                try {
                    $merger = new Merger(new TcpdiDriver);
                    if (Storage::exists("temp/$fileName")) {
                        $merger->addFile($tempPath);
                    }
                    $merger->addFile($file);
                    $mergedPdf = $merger->merge();
                }
                catch (\Exception $e) {
                    $merger = new Merger();
                    if (Storage::exists("temp/$fileName")) {
                        $merger->addFile($tempPath);
                    }
                    $merger->addFile($file);
                    $mergedPdf = $merger->merge();
                }

                // $merger = new Merger(new TcpdiDriver);
                // // $merger = new Merger();
                // if (Storage::exists("temp/$fileName")) {
                //     $merger->addFile($tempPath);
                // }
                // $merger->addFile($file);

                // $mergedPdf = $merger->merge();
                Storage::put("temp/$fileName", $mergedPdf);
            }

            // $merger = new Merger(new TcpdiDriver);
            // // $merger = new Merger();
            // foreach ($pdfFiles as $file) {
            //     $merger->addFile($file);
            // }

            // $mergedPdf = $merger->merge();
            // $fileName = 'merged_'.time().'.pdf';
            // Storage::put("temp/$fileName", $mergedPdf);
            ///////////

            $token = ['a' => 'temp/', 'b' => $fileName];
            $encodedToken = SafeToken::encode($token);
            $response['token'] = $encodedToken;

            // return response()->download(storage_path("app/public/pdf/$fileName"));
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $response,
        ], 200);
    }

    public function printPdf(Request $request) {
        // $filename = "inspection_form_IT_0002_6786f34ea3c75.pdf"; // Sesuaikan dengan logika Anda

        // if (!Storage::disk('private')->exists($filename)) {
        //     abort(404, 'File tidak ditemukan');
        // }

        // return response()->streamDownload(function() use ($filename) {
        //     echo Storage::disk('private')->get($filename);
        // }, $filename, [
        //     'Content-Type' => 'application/pdf',
        //     'Content-Disposition' => 'inline; filename="' . $filename . '"',
        // ]);

        $path = '/doc_approval/2025/purchasing/IT_0001/comparison_form_IT_0001_1_677d9985e2cf2.pdf';
        // $tempPath = 'temp/temp_inspection_form_IT_0002_6786f34ea3c75.pdf';
        // Storage::disk('private')->copy($path, $tempPath);

        // if (!Storage::disk('private')->exists($tempPath)) {
        //     abort(404, 'File tidak ditemukan');
        // }

        // return response()->streamDownload(function () use ($tempPath) {
        //     $stream = Storage::disk('private')->readStream($tempPath);
        //     fpassthru($stream); // Mengirimkan konten file
        //     if (is_resource($stream)) {
        //         fclose($stream); // Tutup resource stream
        //         Storage::disk('private')->delete($tempPath);
        //     }
        // }, basename($tempPath), [
        //     'Content-Type' => 'application/pdf',
        //     'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        //     'Access-Control-Allow-Origin' => '*', // Tambahkan header ini
        //     'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        // ]);

        // latest
        // return response()->stream(function () use ($path) {
        //     $stream = Storage::disk('private')->readStream($path);
        //     fpassthru($stream);
        //     if (is_resource($stream)) {
        //         fclose($stream);
        //     }
        // }, 200, [
        //     'Content-Type' => 'application/pdf',
        //     'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        // ]);
        // end latest

        // abort(404, 'File not found.');
    //     return response('File not found.', 404)
    // ->header('Content-Type', 'text/plain');


        // if (Storage::disk('private')->exists($path)) {
        //     return response()->file(Storage::disk('private')->path($path), [
        //         'Content-Type' => 'application/pdf',
        //         'Content-Disposition' => 'filename="' . basename($path) . '"',
        //         'Cache-Control' => 'no-store, no-cache, must-revalidate',
        //         'Pragma' => 'no-cache',
        //         // 'Content-Type' => 'text/plain',
        //         // 'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        //         // 'Cache-Control' => 'no-store, no-cache, must-revalidate',
        //         // 'Pragma' => 'no-cache',
        //         // 'Access-Control-Allow-Origin' => '*',
        //         // 'Access-Control-Allow-Methods' => 'GET, POST',
        //         // 'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
        //     ]);
        // }

        // abort(404, 'File not found.');

        // // try {
        // //     // $path = decrypt($token);

        // //     if (!Storage::disk('private')->exists($path)) {
        // //         abort(404, 'File tidak ditemukan');
        // //     }

        // //     $filename = basename($path);

        // //     return response()->streamDownload(function() use ($path) {
        // //         $stream = Storage::disk('private')->readStream($path);
        // //         while (!feof($stream)) {
        // //             echo fread($stream, 8192);
        // //             flush();
        // //         }
        // //         fclose($stream);
        // //     }, $filename, [
        // //         'Content-Type' => 'application/pdf',
        // //         'Content-Disposition' => 'inline; filename="' . $filename . '"', // Pastikan ini inline
        // //         'Accept-Ranges' => 'bytes',
        // //         'Cache-Control' => 'private, no-store, no-cache, must-revalidate, max-age=0',
        // //         'Pragma' => 'no-cache'
        // //     ]);
        // // } catch (\Exception $e) {
        // //     abort(404, 'Invalid token');
        // // }



        // return new StreamedResponse(function () use ($tempPath) {
        //     $stream = Storage::disk('private')->readStream($tempPath);
        //     fpassthru($stream);
        //     fclose($stream);
        //     Storage::disk('private')->delete($tempPath);
        // }, 200, [
        //     'Content-Type' => 'application/pdf',
        //     'Content-Disposition' => 'inline; filename="inspection_form_IT_0002_6786f34ea3c75.pdf"',
        // ]);

        // $url = Storage::temporaryUrl(
        //     $path,
        //     now()->addMinutes(5)
        // );

        // Cek keberadaan file di private storage
        // $path = '/doc_approval/2025/purchasing/IT_0001/comparison_form_IT_0001_1_677d9985e2cf2.pdf';

        // $filename = 'application_form_IT_001_1_678f403d8f174.pdf';
        // $path = 'private/doc_approval/2025/purchasing/IT_001/' . $filename;

        $token = SafeToken::decode($request->token);
        $validator = Validator::make($token, [
            'a' => 'required',
            'b' => 'required',
        ]);

        $path = $token['a'];
        $fileName = $token['b'];

        if (!Storage::exists($path.$fileName)) {
            abort(404, 'File not found');
        }

        return new Response(Storage::get($path.$fileName), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    public function downloadPdf(Request $request) {
        $token = SafeToken::decode($request->token);
        $validator = Validator::make($token, [
            'a' => 'required',
            'b' => 'required',
        ]);

        $path = $token['a'];
        $fileName = $token['b'];

        if (!Storage::exists($path.$fileName)) {
            abort(404, 'File not found');
        }

        $documentName = 'DOCUMENTS_'.time().'.pdf';
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$documentName.'"',
            'Content-Description' => 'File Transfer',
            'Content-Transfer-Encoding' => 'binary',
            'X-Content-Type-Options' => 'nosniff',
            'X-Download-Options' => 'noopen',
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        return Storage::download($path.$fileName, $documentName, $headers);
    }

    private function isValidReferer($referer, $allowedReferers){
        if (empty($referer)) {
            return false;
        }

        foreach ($allowedReferers as $allowedReferer) {
            if (strpos($referer, $allowedReferer) === 0) {
                return true;
            }
        }

        return false;
    }

    private function getEmailContent($message) {
        $from = $to = $cc = $bcc = '';
        foreach ($message->getRecipients() as $recipient) {
            $email = $recipient->getEmail();
            $name = $recipient->getName();
            if ($recipient->getType() == 'To') {
                $separator = $to != '' ? '; ' : '';
                $to .= ($name) ? $separator.'<a href="mailto:' . $email . '">' . $name . '</a>' : $separator.'<a href="mailto:' . $email . '">' . $email . '</a>';
            }
            elseif ($recipient->getType() == 'Cc') {
                $separator = $cc != '' ? '; ' : '';
                $cc .= ($name) ? $separator.'<a href="mailto:' . $email . '">' . $name . '</a>' : $separator.'<a href="mailto:' . $email . '">' . $email . '</a>';
            }
            elseif ($recipient->getType() == 'Bcc') {
                $separator = $bcc != '' ? '; ' : '';
                $bcc .= ($name) ? $separator.'<a href="mailto:' . $email . '">' . $name . '</a>' : $separator.'<a href="mailto:' . $email . '">' . $email . '</a>';
            }
        }

        $subject = $message->properties['subject'];
        $htmlContent = $message->getBodyHTML();
        $from = $message->properties['sender_name'];
        $sendTime = $message->getSendTime();
        $sendTimeParse = Carbon::parse($sendTime);
        $sendTimeParse->setTimezone('Asia/Jakarta');
        $sendTime = $sendTimeParse->format('l, F j, Y H:i');

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,b,i,u,strong,em,br,span,div,table,tr,td,th,tbody,thead,a[href],ul,ol,li');
        // $config = HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $purifier = new HTMLPurifier($config);

        // $htmlContent = $this->cleanHtml($htmlContent);
        // $htmlContent = $message->getBody();

        $attachments = $message->getAttachments();
        foreach ($attachments as $attachment) {
            $contentId = $attachment->getContentId();
            if ($contentId) {
                // Remove angle brackets if present
                $contentId = trim($contentId, '<>');
                $tempFilePath = tempnam(sys_get_temp_dir(), 'inline_');
                file_put_contents($tempFilePath, $attachment->getData());

                // Replace "cid:" references in HTML with base64 encoded image data
                $imageData = base64_encode(file_get_contents($tempFilePath));
                $mimeType = mime_content_type($tempFilePath);
                $replacement = "data:$mimeType;base64,$imageData";
                $htmlContent = str_replace("cid:$contentId", $replacement, $htmlContent);

                unlink($tempFilePath); // Remove temporary file
            }
        }
        $htmlContent = $purifier->purify($htmlContent);

        $headerHtml = '<div class="email-header">
                            <p>From: '.trim($from).'</p>
                            <p>Sent: '.$sendTime.'</p>
                            <p>To: '.trim($to).'</p>
                            <p>Cc: '.trim($cc).'</p>
                            <p>Subject: '.trim($subject).'</p>
                        </div>
                        <hr>
                        <div class="email-body">
                            '.$htmlContent.'
                        </div>';
        return $headerHtml;
    }

    private function cleanHtml($html) {
        $html = strip_tags($html, '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><table><tr><td><th><img><a><span><div>');
        // $html = str_replace('white-space: pre-wrap;', '', $html);
        // $html = str_replace('white-space: -moz-pre-wrap !important;', '', $html);
        // $html = str_replace('white-space: -pre-wrap;', '', $html);
        // $html = str_replace('white-space: -o-pre-wrap;', '', $html);
        // $html = str_replace('word-wrap: break-word;', '', $html);
        // $html = str_replace('pre {', '', $html);
        // $html = str_replace('/* css-3 */', '', $html);
        // $html = str_replace('/* Mozilla, since 1999 */', '', $html);
        // $html = str_replace('/* Opera 4-6 */', '', $html);
        // $html = str_replace('/* Opera 7 */', '', $html);
        // $html = str_replace('/* Internet Explorer 5.5+ */', '', $html);
        // $html = str_replace('}', '', $html);

        return $html;
    }

    private function getEmailStyles() {
        return "
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .email-header { margin-bottom: 3px; }
                .email-header p { margin: 2px 0; }
                .email-body { margin-top: 3px; }
                img { max-width: 100%; height: auto; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #ddd; padding: 8px; }
                @page { margin: 15mm; }
            </style>
        ";
    }

    // public function renderEmailDocument($filePath) {
    //     $parser = new Parser();
    //     $parser->setPath($filePath);

    //     $subject = $parser->getHeader('subject');
    //     $from = $parser->getHeader('from');
    //     $body = $parser->getMessageBody('html');

    //     return view('emailViewer', compact('subject', 'from', 'body'));
    // }
}
