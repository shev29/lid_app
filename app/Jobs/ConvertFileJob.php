<?php

namespace App\Jobs;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Mpdf\Mpdf;
use Hfig\MAPI\MAPI;
use Hfig\MAPI\MapiMessageFactory;
use Hfig\MAPI\OLE\Pear\DocumentFactory;
use Hfig\MAPI\Message\Message;
use HTMLPurifier;
use HTMLPurifier_Config;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ConvertFileJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileId;

    public function __construct($fileId) {
        $this->fileId = $fileId;
    }

    public function handle(): void {
        $getAttachments = DB::table('doc_approval_attachment')
                            ->select('attachment_id', 'path_detail', 'mime_type', 'filename', 'converted', 'filename_converted')
                            ->where('attachment_id', $this->fileId)
                            ->where('converted', '0')
                            ->where('is_active', '1')
                            ->first();
		Log::info('[Convert File] Started');

        if ($getAttachments) {
            if($getAttachments->converted == '0') {
                $mimeType = $getAttachments->mime_type;
                $fileName = $getAttachments->filename;
                $path = '/doc_approval'.$getAttachments->path_detail.$getAttachments->filename;

                $checkExtension = function (array $extensions) use ($fileName) {
                    return collect($extensions)->contains(function ($ext) use ($fileName) {
                        return str_ends_with(strtolower($fileName), $ext);
                    });
                };

                if (str_contains($mimeType, 'pdf') || str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheetml') || str_contains($mimeType, 'wordprocessingml')) {
                    // Ambil kredensial dari database
                    $getDriveCredentials = DB::table('setup_drive_credentials')
                                                    ->select('*')
                                                    ->where('is_active', '1')
                                                    ->orderBy('id', 'asc')
                                                    ->first();

                    // Path ke file Service Account JSON
                    $credentialsPath = Storage::disk('private')->path($getDriveCredentials->credential_path.$getDriveCredentials->credential_filename);

                    // Inisialisasi Google Client
                    $client = new Client();
                    $client->setAuthConfig($credentialsPath);
                    $client->addScope(Drive::DRIVE);
                    $client->addScope(Drive::DRIVE_FILE);

                    // Inisialisasi Drive Service
                    $driveService = new Drive($client);

                    $getDriveFolders = DB::table('setup_drive_folders')
                                                ->select('*')
                                                ->where('is_active', '1')
                                                ->orderBy('id', 'asc')
                                                ->first();

                    $filePath = Storage::disk('private')->path($path);

                    $folderId = $getDriveFolders->folder_id; // Folder ID Google Drive

                    // Upload file .doc atau .docx ke Google Drive dan konversi ke Google Docs
                    $fileMetadata = new Drive\DriveFile([
                        'name' => $getAttachments->filename,
                        'mimeType' => 'application/vnd.google-apps.document', // Konversi ke Google Docs
                        'parents' => [$folderId]
                    ]);

                    $content = file_get_contents($filePath);
                    $uploadedFile = $driveService->files->create($fileMetadata, [
                        'data' => $content,
                        'mimeType' => $mimeType,
                        'uploadType' => 'multipart',
                    ]);

                    // Eksport file yang dikonversi ke format PDF
                    $pdfFileContent = $driveService->files->export($uploadedFile->id, 'application/pdf', ['alt' => 'media']);

                    // Simpan PDF yang dihasilkan ke storage
                    $pdfFileName = pathinfo($getAttachments->filename, PATHINFO_FILENAME) . '.pdf';
                    $pdfPath = '/doc_approval'.$getAttachments->path_detail.$pdfFileName;

                    if (Storage::disk('private')->put($pdfPath, $pdfFileContent->getBody()->getContents())) {
                        // Hapus file dari Google Drive jika penyimpanan berhasil
                        $dataUpdate = [
                            'converted' => '1',
                            'filename_converted' => $pdfFileName
                        ];

                        DB::table('doc_approval_attachment')
                                ->where('attachment_id', '=', $getAttachments->attachment_id)
                                ->update($dataUpdate);

                        Storage::disk('private')->delete($path);

                        try {
                            $driveService->files->delete($uploadedFile->id);
                        } catch (Exception $e) {
                            Log::error('Error deleting file from Google Drive: ' . $e->getMessage());
                        }
                    }
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

                    $pdfFileName = pathinfo($getAttachments->filename, PATHINFO_FILENAME).'.pdf';
                    $pathDetail = '/doc_approval'.$getAttachments->path_detail.$pdfFileName;

                    $pdfPath = Storage::disk('private')->path($pathDetail);
                    $mpdf->SetCompression(true);
                    $mpdf->Output($pdfPath, 'F');

                    $dataUpdate = [
                        'converted' => '1',
                        'filename_converted' => $pdfFileName
                    ];

                    DB::table('doc_approval_attachment')
                            ->where('attachment_id', '=', $getAttachments->attachment_id)
                            ->update($dataUpdate);

                    Storage::disk('private')->delete($path);
                }
                else if (
                    str_contains($mimeType, 'ms-outlook') ||
                    str_contains($mimeType, 'ms-tnef') ||
                    $mimeType === 'message/rfc822' ||
                    str_contains($mimeType, 'vnd.ms-outlook') ||
                    $checkExtension(['.msg', '.eml'])
                ) {
                    $filePath = Storage::disk('private')->path($path);
                    try {
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

                        $mpdf->WriteHTML($this->getEmailStyles(), \Mpdf\HTMLParserMode::HEADER_CSS);
                        // $mpdf->WriteHTML($htmlContent, \Mpdf\HTMLParserMode::HTML_BODY);

                        $chunks = str_split($htmlContent, 500000); // 500KB per chunk
                        foreach ($chunks as $chunk) {
                            $mpdf->WriteHTML($chunk, \Mpdf\HTMLParserMode::HTML_BODY);
                        }

                        $pdfFileName = pathinfo($getAttachments->filename, PATHINFO_FILENAME).'.pdf';
                        $pathDetail = '/doc_approval'.$getAttachments->path_detail.$pdfFileName;

                        $pdfPath = Storage::disk('private')->path($pathDetail);
                        $mpdf->Output($pdfPath, 'F');

                        $dataUpdate = [
                            'converted' => '1',
                            'filename_converted' => $pdfFileName
                        ];

                        DB::table('doc_approval_attachment')
                                    ->where('attachment_id', '=', $getAttachments->attachment_id)
                                    ->update($dataUpdate);

                        Storage::disk('private')->delete($path);
                    }
                    catch (Exception $e) {
                        Log::error('Error : ' . $e->getMessage());
                    }

                }

                // if (Storage::disk('private')->exists($path)) {
                //     Storage::disk('private')->delete($path);
                // }
            }
        }
		Log::info('[Convert File] Completed');

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
        // $html = preg_replace('/<pre[^>]*style=["\'].*?["\'][^>]*>/i', '<pre>', $html);
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
}
