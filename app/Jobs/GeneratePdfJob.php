<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mpdf\Mpdf as MpdfLibrary;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneratePdfJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $documentType;
    protected $dataForm;

    /**
     * Create a new job instance.
     */
    public function __construct($documentType, array $dataForm)
    {
        $this->documentType = $documentType;
        $this->dataForm = $dataForm;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
		Log::info('[Generate PDF] Started');

        $documentType = $this->documentType;
        if($documentType == '1') { // ORDER FORM
            $dataForm = $this->dataForm['dataForm'];
            // $templatePath = 'private/template/F.PUR-02.01_ORDER_FORM_FINAL.xlsx';
            if(count($dataForm['itemForm']) <= 15) {
                if($dataForm['signer'][2]['flowAs'] == 'CHECKER') {
                    $templatePath = 'private/template/F.PUR-02.01_ORDER_FORM_4_SIGN.xlsx';
                }
                else {
                    $templatePath = 'private/template/F.PUR-02.01_ORDER_FORM_3_SIGN.xlsx';
                }
            }
            else {
                if($dataForm['signer'][2]['flowAs'] == 'CHECKER') {
                    $templatePath = 'private/template/F.PUR-02.01_ORDER_FORM_4_SIGN_25_ROW.xlsx';
                }
                else {
                    $templatePath = 'private/template/F.PUR-02.01_ORDER_FORM_3_SIGN_25_ROW.xlsx';
                }
            }

            $filePath = 'private/doc_approval'.$dataForm['filePath'];

            if (!Storage::exists($filePath)) {
                Storage::makeDirectory($filePath);
            }

            Storage::copy($templatePath, $filePath.$dataForm['filename'].'.xlsx');

            $tempXlsxFile = Storage::path($filePath.$dataForm['filename'].'.xlsx');
            $tempPdfFile = Storage::path($filePath.$dataForm['filename'].'.pdf');

            $spreadsheet = IOFactory::load($tempXlsxFile);
            $spreadsheet->getDefaultStyle()->getFont()->setName('arial');
            $spreadsheet->getDefaultStyle()->getAlignment()->setWrapText(true);

            $worksheet = $spreadsheet->getActiveSheet();
            $worksheet->getPageSetup()
                        ->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
                        ->setPaperSize(PageSetup::PAPERSIZE_A4);

            $worksheet->getPageMargins()
                        ->setTop(0.2)
                        ->setLeft(0.2)
                        ->setRight(0.2)
                        ->setBottom(0.2);

            $worksheet->getPageSetup()->setFitToPage(true)
                                    ->setFitToWidth(1)
                                    ->setFitToHeight(1);

            $worksheet->setCellValue('E5', ': '.$dataForm['docNumber']);
            $worksheet->setCellValue('E6', ': '.$dataForm['requestDate']);
            $worksheet->setCellValue('Y5', ': '.$dataForm['departmentName']);
            $worksheet->setCellValue('Y6', ': '.$dataForm['locationName']);
            $worksheet->setCellValue('AA9', $dataForm['currency']);
            $worksheet->setCellValue('AF9', $dataForm['currency']);

            if(count($dataForm['itemForm']) <= 15) {
                if($dataForm['purpose'][0]['description'] == 'New purchase') {
                    $worksheet->setCellValue('K29', $dataForm['purpose'][0]['reason']);
                    $worksheet->setCellValue('AB29', $dataForm['purpose'][0]['remarks']);
                }
                else if($dataForm['purpose'][0]['description'] == 'Replacement purchase') {
                    $worksheet->setCellValue('K30', $dataForm['purpose'][0]['reason']);
                    $worksheet->setCellValue('AB30', $dataForm['purpose'][0]['remarks']);
                }
                else if($dataForm['purpose'][0]['description'] == 'Routine purchase') {
                    $worksheet->setCellValue('K31', $dataForm['purpose'][0]['reason']);
                    $worksheet->setCellValue('AB31', $dataForm['purpose'][0]['remarks']);
                }
                else if($dataForm['purpose'][0]['description'] == 'Repair') {
                    $worksheet->setCellValue('K32', $dataForm['purpose'][0]['reason']);
                    $worksheet->setCellValue('AB32', $dataForm['purpose'][0]['remarks']);
                }
                else if($dataForm['purpose'][0]['description'] == 'Routine maintenance/service') {
                    // $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    // $drawing->setName('Circle');
                    // $drawing->setDescription('Circle Shape');
                    // $circleImagePath = public_path('assets/images/circle.png');
                    // $drawing->setPath($circleImagePath);
                    // $drawing->setOffsetX(50);
                    // $drawing->setHeight(50); // Sesuaikan ukuran lingkaran
                    // $drawing->setWidth(20);
                    // $drawing->setCoordinates('E34'); // Posisi lingkaran
                    // $drawing->setWorksheet($worksheet);

                    $richText = new RichText();
                    $checkmark = $richText->createTextRun('✓ ');
                    $checkmark->getFont()->setBold(true)->setSize(11);

                    $richText->createTextRun($dataForm['purpose'][0]['reason']);
                    if($dataForm['purpose'][0]['reason'] == 'Monthly') {
                        $worksheet->getCell('K33')->setValue($richText);
                    }
                    else if($dataForm['purpose'][0]['reason'] == 'Yearly') {
                        $worksheet->getCell('P33')->setValue($richText);
                    }
                    else if($dataForm['purpose'][0]['reason'] == 'Others') {
                        $worksheet->getCell('V33')->setValue($richText);
                    }

                    $worksheet->setCellValue('AB33', $dataForm['purpose'][0]['remarks']);
                }
                else if($dataForm['purpose'][0]['description'] == 'Fixed asset') {
                    $richText = new RichText();
                    $checkmark = $richText->createTextRun('✓ ');
                    $checkmark->getFont()->setBold(true)->setSize(11);

                    $richText->createTextRun($dataForm['purpose'][0]['reason']);
                    if($dataForm['purpose'][0]['reason'] == 'Budget (Y)' || $dataForm['purpose'][0]['reason'] == 'Budget (N)') {
                        $worksheet->getCell('K34')->setValue($richText);
                    }
                    else if($dataForm['purpose'][0]['reason'] == 'New Investment') {
                        $worksheet->getCell('P34')->setValue($richText);
                    }
                    else if($dataForm['purpose'][0]['reason'] == 'Replacement') {
                        $worksheet->getCell('V34')->setValue($richText);
                    }

                    $worksheet->setCellValue('AB34', $dataForm['purpose'][0]['remarks']);
                }

                $row = 10;
                $no = 1;
                foreach($dataForm['itemForm'] as $rowItem){
                    $worksheet->setCellValue('A'.$row, $no);
                    $worksheet->setCellValue('B'.$row, $rowItem['costCenter']);
                    $worksheet->setCellValue('E'.$row, $rowItem['applianceItem']);
                    $worksheet->setCellValue('M'.$row, $rowItem['brandType']);
                    $worksheet->setCellValue('U'.$row, $rowItem['unitQuantity']);
                    $worksheet->setCellValue('X'.$row, $rowItem['unitName']);
                    $worksheet->setCellValue('AA'.$row, number_format($rowItem['unitPriceEst'], 0, '.', ','));
                    $worksheet->setCellValue('AG'.$row, $this->formatNumber($rowItem['totalPriceEst']));
                    $worksheet->setCellValue('AL'.$row, $rowItem['requiredDate']);

                    $row++;
                    $no++;
                }

                $worksheet->setCellValue('AF25', '('.$dataForm['currency'].')');
                $worksheet->setCellValue('AG25', $this->formatNumber($dataForm['grandTotal']));

                if($dataForm['signer'][2]['flowAs'] == 'CHECKER') {
                    $worksheet->setCellValue('AJ38', 'Submitted at '.$dataForm['signer'][0]['decisionAtTime']);
                    $worksheet->setCellValue('AJ40', $dataForm['signer'][0]['employeeName']);
                    $worksheet->setCellValue('AJ41', 'Date: '.$dataForm['signer'][0]['decisionAt']);

                    if($dataForm['signer'][1]['decisionAtTime'] != '') {
                        $worksheet->setCellValue('AD38', 'Checked at '.$dataForm['signer'][1]['decisionAtTime']);
                    }
                    $worksheet->setCellValue('AD40', $dataForm['signer'][1]['employeeName']);
                    $worksheet->setCellValue('AD41', 'Date: '.$dataForm['signer'][1]['decisionAt']);

                    if($dataForm['signer'][2]['employeeName'] != '' && $dataForm['signer'][2]['employeeName'] != null) {
                        if($dataForm['signer'][2]['decisionAtTime'] != '') {
                            $worksheet->setCellValue('X38', 'Checked at '.$dataForm['signer'][2]['decisionAtTime']);
                        }
                        $worksheet->setCellValue('X40', $dataForm['signer'][2]['employeeName']);
                        $worksheet->setCellValue('X41', 'Date: '.$dataForm['signer'][2]['decisionAt']);
                    }

                    if($dataForm['signer'][3]['decisionAtTime'] != '') {
                        $worksheet->setCellValue('R38', 'Approved at '.$dataForm['signer'][3]['decisionAtTime']);
                    }
                    $worksheet->setCellValue('R40', $dataForm['signer'][3]['employeeName']);
                    $worksheet->setCellValue('R41', 'Date: '.$dataForm['signer'][3]['decisionAt']);
                }
                else {
                    $worksheet->setCellValue('AI38', 'Submitted at '.$dataForm['signer'][0]['decisionAtTime']);
                    $worksheet->setCellValue('AI40', $dataForm['signer'][0]['employeeName']);
                    $worksheet->setCellValue('AI41', 'Date: '.$dataForm['signer'][0]['decisionAt']);

                    if($dataForm['signer'][1]['decisionAtTime'] != '') {
                        $worksheet->setCellValue('AB38', 'Checked at '.$dataForm['signer'][1]['decisionAtTime']);
                    }
                    $worksheet->setCellValue('AB40', $dataForm['signer'][1]['employeeName']);
                    $worksheet->setCellValue('AB41', 'Date: '.$dataForm['signer'][1]['decisionAt']);

                    if($dataForm['signer'][2]['decisionAtTime'] != '') {
                        $worksheet->setCellValue('T38', 'Approved at '.$dataForm['signer'][2]['decisionAtTime']);
                    }
                    $worksheet->setCellValue('T40', $dataForm['signer'][2]['employeeName']);
                    $worksheet->setCellValue('T41', 'Date: '.$dataForm['signer'][2]['decisionAt']);
                }
            }
            else {
                if($dataForm['purpose'][0]['description'] == 'New purchase') {
                    $worksheet->setCellValue('K40', $dataForm['purpose'][0]['reason']);
                    $worksheet->setCellValue('AB40', $dataForm['purpose'][0]['remarks']);
                }
                else if($dataForm['purpose'][0]['description'] == 'Replacement purchase') {
                    $worksheet->setCellValue('K41', $dataForm['purpose'][0]['reason']);
                    $worksheet->setCellValue('AB41', $dataForm['purpose'][0]['remarks']);
                }
                else if($dataForm['purpose'][0]['description'] == 'Routine purchase') {
                    $worksheet->setCellValue('K42', $dataForm['purpose'][0]['reason']);
                    $worksheet->setCellValue('AB42', $dataForm['purpose'][0]['remarks']);
                }
                else if($dataForm['purpose'][0]['description'] == 'Repair') {
                    $worksheet->setCellValue('K43', $dataForm['purpose'][0]['reason']);
                    $worksheet->setCellValue('AB43', $dataForm['purpose'][0]['remarks']);
                }
                else if($dataForm['purpose'][0]['description'] == 'Routine maintenance/service') {
                    $richText = new RichText();
                    $checkmark = $richText->createTextRun('✓ ');
                    $checkmark->getFont()->setBold(true)->setSize(11);

                    $richText->createTextRun($dataForm['purpose'][0]['reason']);
                    if($dataForm['purpose'][0]['reason'] == 'Monthly') {
                        $worksheet->getCell('K44')->setValue($richText);
                    }
                    else if($dataForm['purpose'][0]['reason'] == 'Yearly') {
                        $worksheet->getCell('P44')->setValue($richText);
                    }
                    else if($dataForm['purpose'][0]['reason'] == 'Others') {
                        $worksheet->getCell('V44')->setValue($richText);
                    }

                    $worksheet->setCellValue('AB44', $dataForm['purpose'][0]['remarks']);
                }
                else if($dataForm['purpose'][0]['description'] == 'Fixed asset') {
                    $richText = new RichText();
                    $checkmark = $richText->createTextRun('✓ ');
                    $checkmark->getFont()->setBold(true)->setSize(11);

                    $richText->createTextRun($dataForm['purpose'][0]['reason']);
                    if($dataForm['purpose'][0]['reason'] == 'Budget (Y)' || $dataForm['purpose'][0]['reason'] == 'Budget (N)') {
                        $worksheet->getCell('K45')->setValue($richText);
                    }
                    else if($dataForm['purpose'][0]['reason'] == 'New Investment') {
                        $worksheet->getCell('P45')->setValue($richText);
                    }
                    else if($dataForm['purpose'][0]['reason'] == 'Replacement') {
                        $worksheet->getCell('V45')->setValue($richText);
                    }

                    $worksheet->setCellValue('AB45', $dataForm['purpose'][0]['remarks']);
                }

                $row = 10;
                $no = 1;
                foreach($dataForm['itemForm'] as $rowItem){
                    $worksheet->setCellValue('A'.$row, $no);
                    $worksheet->setCellValue('B'.$row, $rowItem['costCenter']);
                    $worksheet->setCellValue('E'.$row, $rowItem['applianceItem']);
                    $worksheet->setCellValue('M'.$row, $rowItem['brandType']);
                    $worksheet->setCellValue('U'.$row, $rowItem['unitQuantity']);
                    $worksheet->setCellValue('X'.$row, $rowItem['unitName']);
                    $worksheet->setCellValue('AA'.$row, number_format($rowItem['unitPriceEst'], 0, '.', ','));
                    $worksheet->setCellValue('AG'.$row, $this->formatNumber($rowItem['totalPriceEst']));
                    $worksheet->setCellValue('AL'.$row, $rowItem['requiredDate']);

                    $row++;
                    $no++;
                }

                $worksheet->setCellValue('AF36', '('.$dataForm['currency'].')');
                $worksheet->setCellValue('AG36', $this->formatNumber($dataForm['grandTotal']));

                if($dataForm['signer'][2]['flowAs'] == 'CHECKER') {
                    // 2 CHECKER
                    $worksheet->setCellValue('AJ49', 'Submitted at '.$dataForm['signer'][0]['decisionAtTime']);
                    $worksheet->setCellValue('AJ51', $dataForm['signer'][0]['employeeName']);
                    $worksheet->setCellValue('AJ52', 'Date: '.$dataForm['signer'][0]['decisionAt']);

                    if($dataForm['signer'][1]['decisionAtTime'] != '') {
                        $worksheet->setCellValue('AD46', 'Checked at '.$dataForm['signer'][1]['decisionAtTime']);
                    }
                    $worksheet->setCellValue('AD51', $dataForm['signer'][1]['employeeName']);
                    $worksheet->setCellValue('AD52', 'Date: '.$dataForm['signer'][1]['decisionAt']);

                    if($dataForm['signer'][2]['employeeName'] != '' && $dataForm['signer'][2]['employeeName'] != null) {
                        if($dataForm['signer'][2]['decisionAtTime'] != '') {
                            $worksheet->setCellValue('X49', 'Checked at '.$dataForm['signer'][2]['decisionAtTime']);
                        }
                        $worksheet->setCellValue('X51', $dataForm['signer'][2]['employeeName']);
                        $worksheet->setCellValue('X52', 'Date: '.$dataForm['signer'][2]['decisionAt']);
                    }

                    if($dataForm['signer'][3]['decisionAtTime'] != '') {
                        $worksheet->setCellValue('R49', 'Approved at '.$dataForm['signer'][3]['decisionAtTime']);
                    }
                    $worksheet->setCellValue('R51', $dataForm['signer'][3]['employeeName']);
                    $worksheet->setCellValue('R52', 'Date: '.$dataForm['signer'][3]['decisionAt']);
                }
                else {
                    // 1 CHECKER
                    $worksheet->setCellValue('AI49', 'Submitted at '.$dataForm['signer'][0]['decisionAtTime']);
                    $worksheet->setCellValue('AI51', $dataForm['signer'][0]['employeeName']);
                    $worksheet->setCellValue('AI52', 'Date: '.$dataForm['signer'][0]['decisionAt']);

                    if($dataForm['signer'][1]['decisionAtTime'] != '') {
                        $worksheet->setCellValue('AB49', 'Checked at '.$dataForm['signer'][1]['decisionAtTime']);
                    }
                    $worksheet->setCellValue('AB51', $dataForm['signer'][1]['employeeName']);
                    $worksheet->setCellValue('AB52', 'Date: '.$dataForm['signer'][1]['decisionAt']);

                    if($dataForm['signer'][2]['decisionAtTime'] != '') {
                        $worksheet->setCellValue('T49', 'Approved at '.$dataForm['signer'][2]['decisionAtTime']);
                    }
                    $worksheet->setCellValue('T51', $dataForm['signer'][2]['employeeName']);
                    $worksheet->setCellValue('T52', 'Date: '.$dataForm['signer'][2]['decisionAt']);
                }
            }

            $writer = new Xlsx($spreadsheet);
            // $filePath = 'private/doc_approval'.$dataForm['filePath'];
            // Storage::makeDirectory($filePath);
            // $tempFile = Storage::path($filePath.$dataForm['filename'].'.xlsx');
            // $writer->save($tempFile);

            // IOFactory::registerWriter('Pdf', Mpdf::class);
            // $pdfWriter = IOFactory::createWriter($spreadsheet, 'Pdf');
            // $tempPdfFile = Storage::path($filePath . $dataForm['filename'] . '.pdf');
            // $pdfWriter->save($tempPdfFile);

            $writer->save($tempXlsxFile);
                // $spreadsheetNew = IOFactory::load(Storage::path($tempXlsxFile));
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
                'default_font' => 'arial' // Atur Arial Narrow sebagai default
            ]);

            $pdfWriter = new Mpdf($spreadsheet);
            $pdfWriter->setFont('arial');
            $pdfWriter->save($tempPdfFile);
        }
        else if($documentType == '2') { // APPLICATION & PO FORM
            $dataForm = $this->dataForm['dataForm'];
            // dd($this->dataForm['dataForm']);
            // $headerPath = Storage::path('private/template/header_bml_pakuwon.png');
            $headerPath = storage_path('app/private/template/header_bml_pakuwon_final.png');
            $templatePath = 'private/template/F.PUR-02.02-04_APPLICATION_FINAL.xlsx';
            // $templatePathPo = 'private/template/F.PUR-02.02-04_PO_FINAL.xlsx';
            $templatePathPo = 'private/template/F.PUR-02.02-04_PO_NEW.xlsx';

            // APPLICATION FORM
            $spreadsheet = IOFactory::load(Storage::path($templatePath));
            $spreadsheet->getDefaultStyle()->getFont()->setName('arial');
            $spreadsheet->getDefaultStyle()->getAlignment()->setWrapText(true);
            $worksheet = $spreadsheet->getActiveSheet();
            // $worksheet = $spreadsheet->setActiveSheetIndex(0);

            // $drawing = new Drawing();
            // $drawing->setName('Header');
            // $drawing->setDescription('Header Image');
            // $drawing->setPath($headerPath);

            // Positioning
            // $drawing->setCoordinates('A1');

            // // Ukuran dan skala yang tepat
            // $drawing->setWidthAndHeight(1000, 200); // Sesuaikan dengan kebutuhan

            // // Positioning lebih presisi
            // $drawing->setOffsetX(0);
            // $drawing->setOffsetY(0);

            // Pastikan gambar menutupi sel secara penuh
            // $drawing->setResizeProportional(false);

            // $drawing->setWorksheet($worksheet);

            $worksheet->getPageSetup()
                        ->setFitToPage(true)
                        ->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
                        ->setPaperSize(PageSetup::PAPERSIZE_A4)
                        ->setFitToWidth(1)
                        ->setFitToHeight(1);

            $worksheet->getPageMargins()
                        ->setTop(0.8)
                        ->setRight(0.4)
                        ->setLeft(0.4)
                        ->setBottom(0);

            $worksheet->setCellValue('A2', $dataForm['applicationHeader']);
            $worksheet->getStyle('A2')->getFont()->setSize(19);

            $worksheet->setCellValue('F4', $dataForm['applicationNumber']);
            $worksheet->setCellValue('F6', $dataForm['applicationTitle']);
            $worksheet->setCellValue('F8', $dataForm['applicationEstimate']);
            $worksheet->setCellValue('F10', Carbon::parse($dataForm['applicationSubmittedDate'])->format('d-M-Y'));
            $worksheet->setCellValue('F12', $dataForm['apllicationVendorPic']);
            $worksheet->setCellValue('P14', $dataForm['applicationCurrency']);
            $worksheet->setCellValue('Y4', $dataForm['purchaseType']);
            $worksheet->setCellValue('Y6', $dataForm['budgetNo']);
            $worksheet->setCellValue('Y8', $dataForm['budgetAmount']);
            $worksheet->setCellValue('AC8', $dataForm['budgetWithinOver']);
            $worksheet->setCellValue('AF8', $dataForm['budgetWithin']);
            $worksheet->setCellValue('X30', $dataForm['applicationCurrency']);
            $worksheet->setCellValue('AD30', $dataForm['applicationCurrency']);
            $worksheet->setCellValue('Y18', Carbon::parse($dataForm['shipDate'])->format('d-M-Y'));
            $worksheet->setCellValue('AF18', $dataForm['formType']);

            $worksheet->setCellValue('A16', $dataForm['vendorName']);
            $arrVendorAddress = preg_split('/\r\n|\r|\n/', $dataForm['vendorAddress']);

            // dd($arrVendorAddress);
            if(count($arrVendorAddress) > 1) {
                $worksheet->setCellValue('A18', $arrVendorAddress[0]);
                $worksheet->setCellValue('A20', $arrVendorAddress[1]);
            }
            else {
                $worksheet->setCellValue('A16', $arrVendorAddress[0]);
            }

            $arrAppDelivery = preg_split('/\r\n|\r|\n/', $dataForm['applicationDelivery']);
            if(count($arrAppDelivery) == 3) {
                $worksheet->setCellValue('A23', $arrAppDelivery[0]);
                $worksheet->setCellValue('A24', $arrAppDelivery[1]);
                $worksheet->setCellValue('A25', $arrAppDelivery[2]);
            }
            else if(count($arrAppDelivery) == 2) {
                $worksheet->setCellValue('A23', $arrAppDelivery[0]);
                $worksheet->setCellValue('A24', $arrAppDelivery[1]);
            }
            else {
                $worksheet->setCellValue('A23', $arrAppDelivery[0]);
            }

            $arrAppInvoice = preg_split('/\r\n|\r|\n/', $dataForm['applicationInvoice']);
            if(count($arrAppInvoice) == 3) {
                $worksheet->setCellValue('A27', $arrAppInvoice[0]);
                $worksheet->setCellValue('A28', $arrAppInvoice[1]);
                $worksheet->setCellValue('A29', $arrAppInvoice[2]);
            }
            else if(count($arrAppInvoice) == 2) {
                $worksheet->setCellValue('A27', $arrAppInvoice[0]);
                $worksheet->setCellValue('A28', $arrAppInvoice[1]);
            }
            else {
                $worksheet->setCellValue('A27', $arrAppInvoice[0]);
            }

            $arrAppRemark = preg_split('/\r\n|\r|\n/', $dataForm['applicationRemark']);
            if(count($arrAppRemark) == 4) {
                $worksheet->setCellValue('T22', $arrAppRemark[0]);
                $worksheet->setCellValue('T23', $arrAppRemark[1]);
                $worksheet->setCellValue('T24', $arrAppRemark[2]);
                $worksheet->setCellValue('T25', $arrAppRemark[3]);
            }
            else if(count($arrAppRemark) == 3) {
                $worksheet->setCellValue('T22', $arrAppRemark[0]);
                $worksheet->setCellValue('T23', $arrAppRemark[1]);
                $worksheet->setCellValue('T24', $arrAppRemark[2]);
            }
            else if(count($arrAppRemark) == 2) {
                $worksheet->setCellValue('T22', $arrAppRemark[0]);
                $worksheet->setCellValue('T23', $arrAppRemark[1]);
            }
            else if(count($arrAppRemark) == 1) {
                $worksheet->setCellValue('T22', $arrAppRemark[0]);
            }

            $arrAppReason = preg_split('/\r\n|\r|\n/', $dataForm['applicationReason']);
            if(count($arrAppReason) == 3) {
                $worksheet->setCellValue('T27', $arrAppReason[0]);
                $worksheet->setCellValue('T28', $arrAppReason[1]);
                $worksheet->setCellValue('T29', $arrAppReason[2]);
            }
            else if(count($arrAppReason) == 2) {
                $worksheet->setCellValue('T27', $arrAppReason[0]);
                $worksheet->setCellValue('T28', $arrAppReason[1]);
            }
            else if(count($arrAppReason) == 1) {
                $worksheet->setCellValue('T27', $arrAppReason[0]);
            }

            for($x = 0; $x < count($dataForm['costCenter']); $x++) {
                if($x == 0) {
                    $worksheet->setCellValue('Y10', $dataForm['costCenter'][$x]['code']);
                    $worksheet->setCellValue('AA10', $dataForm['costCenter'][$x]['percentage'].'%');
                }
                else if($x == 1) {
                    $worksheet->setCellValue('AD10', $dataForm['costCenter'][$x]['code']);
                    $worksheet->setCellValue('AF10', $dataForm['costCenter'][$x]['percentage'].'%');
                }
                else if($x == 2) {
                    $worksheet->setCellValue('Y12', $dataForm['costCenter'][$x]['code']);
                    $worksheet->setCellValue('AA12', $dataForm['costCenter'][$x]['percentage'].'%');
                }
                else if($x == 3) {
                    $worksheet->setCellValue('AD12', $dataForm['costCenter'][$x]['code']);
                    $worksheet->setCellValue('AF12', $dataForm['costCenter'][$x]['percentage'].'%');
                }
            }

            if ($dataForm['reimburseToNext'] > 0)  {
                $dataForm['reimburseToNext'] = $dataForm['reimburseToNext'].'%';
            }
            else {
                $dataForm['reimburseToNext'] = '-';
            }

            $worksheet->setCellValue('Y14', $dataForm['reimburseTo']);
            $worksheet->setCellValue('AH14', $dataForm['reimburseToNext']);

            if($dataForm['paymentType'] == 'TRANSFER') {
                $worksheet->setCellValue('Y16', 'X');
                $worksheet->setCellValue('AC16', '');
            }
            else {
                $worksheet->setCellValue('Y16', '');
                $worksheet->setCellValue('AC16', 'X');
            }

            $startItemRow = 32;
            $row = $startItemRow;
            for($x = 0; $x < count($dataForm['itemForm']); $x++) {
                if($dataForm['itemForm'][$x]['subtotalPrice'] == '' || $dataForm['itemForm'][$x]['subtotalPrice'] == '-' || $dataForm['itemForm'][$x]['subtotalPrice'] == '0' || $dataForm['itemForm'][$x]['subtotalPrice'] == '0.00') {
                    continue;
                }

                $rate = '';
                // if($dataForm['itemForm'][$x]['applianceItem'] == 'VAT' && ($dataForm['itemForm'][$x]['unitPrice'] != null && $dataForm['itemForm'][$x]['unitPrice'] != '' && $dataForm['itemForm'][$x]['unitPrice'] != '-' && $dataForm['itemForm'][$x]['unitPrice'] != '0')) {
                //     $rate = ' '.(int)$dataForm['unitPrice'][$x].'%';
                // }
                // else if($dataForm['itemForm'][$x]['applianceItem'] == 'Discount' && ($dataForm['itemForm'][$x]['unitPrice'] != null && $dataForm['itemForm'][$x]['unitPrice'] != '' && $dataForm['itemForm'][$x]['unitPrice'] != '-' && $dataForm['itemForm'][$x]['unitPrice'] != '0')) {
                //     $rate = ' '.(int)$dataForm['unitPrice'][$x].'%';
                // }

                if($dataForm['itemForm'][$x]['applianceItem'] == 'VAT') {
                    // $rate = ' '.(int)$dataForm['itemForm'][$x]['unitPrice'].'%';
                    // $dppOtherValue = $dataForm['itemForm'][$x]['dppOtherValue'] != null ? ' x '.$dataForm['itemForm'][$x]['dppOtherValue'] : '';
                    //     $rate =  ' '.$dppOtherValue;
                }

                $worksheet->setCellValue('A'.$row, $dataForm['itemForm'][$x]['itemNo']);
                $worksheet->setCellValue('C'.$row, $dataForm['itemForm'][$x]['applianceItem'].$rate);

                // if($dataForm['itemForm'][$x]['itemNo'] != '' && $dataForm['itemForm'][$x]['itemNo'] != null){
                if($dataForm['itemForm'][$x]['applianceItem'] != 'Delivery Fee' && $dataForm['itemForm'][$x]['applianceItem'] != 'Discount' && $dataForm['itemForm'][$x]['applianceItem'] != 'Total Price' && $dataForm['itemForm'][$x]['applianceItem'] != 'VAT' && $dataForm['itemForm'][$x]['applianceItem'] != 'Total Price + VAT'){
                    $worksheet->setCellValue('U'.$row, $dataForm['itemForm'][$x]['quantity']);
                    $worksheet->setCellValue('X'.$row, $dataForm['itemForm'][$x]['unitPrice']);
                }

                if($dataForm['itemForm'][$x]['applianceItem'] == 'Discount') {
                    $worksheet->setCellValue('AD'.$row, '-'.$dataForm['itemForm'][$x]['subtotalPrice']);
                }
                else{
                    $worksheet->setCellValue('AD'.$row, $dataForm['itemForm'][$x]['subtotalPrice']);
                }

                $row++;
            }

            $worksheet->setCellValue('G65', date('n/d/Y H:i'));
            $worksheet->setCellValue('AA64', $dataForm['applicationCurrency']);
            $worksheet->setCellValue('AD64', $dataForm['applicationGrandTotal']);

            $worksheet->setCellValue('AD68', 'Submitted at '.Carbon::parse($dataForm['submittedAt'])->format('d-M-Y H:i:s'));
            $worksheet->setCellValue('AD71', $dataForm['signer'][0]['employeeName']);

            $worksheet->setCellValue('Y71', $dataForm['signer'][1]['employeeName']); // PURCHASING STAFF
            $worksheet->setCellValue('S71', $dataForm['signer'][2]['employeeName']);
            $worksheet->setCellValue('N71', $dataForm['signer'][3]['employeeName']);
            $worksheet->setCellValue('G71', $dataForm['signer'][4]['employeeName']); // GENERAL MANAGER
            $worksheet->setCellValue('A71', $dataForm['signer'][5]['employeeName']); // PRESIDENT DIRECTOR

            $writer = new Xlsx($spreadsheet);
            $filePath = 'private/doc_approval'.$dataForm['filePath'];
            Storage::makeDirectory($filePath);
            $tempFile = Storage::path($filePath.$dataForm['filename'].'_app.xlsx');
            $writer->save($tempFile);

            IOFactory::registerWriter('Pdf', Mpdf::class);
            $pdfWriter = IOFactory::createWriter($spreadsheet, 'Pdf');
            $tempPdfFile1 = Storage::path($filePath . $dataForm['filename'] . '_app.pdf');
            $tempFile = storage_path('app/temp/temp_' . uniqid() . '.pdf');
            $pdfWriter->save($tempFile);

            $pdfContent = file_get_contents($tempFile);
            $mpdfConfig = [
                'tempDir' => storage_path('app/temp'),
                'fontDir' => [storage_path('fonts')],
                'fontdata' => [
                    'arial' => [
                        'R' => 'arial.ttf',
                        'B' => 'arial_Bold.ttf',
                        'I' => 'arial_Italic.ttf',
                        'BI' => 'arial_Bold_Italic.ttf',
                    ]
                ],
                'default_font' => 'arial',
                'margin_header' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
                'margin_left' => 0,
                'margin_right' => 0
            ];

            $mpdf = new \Mpdf\Mpdf($mpdfConfig);
            $headerHtml = '
            <div style="text-align: center;">
                <img src="' . $headerPath . '" style="width: 100%; height: 14mm; margin-top: 5mm; margin-left: 10mm; margin-right: 10mm; margin-bottom: 2mm" />
            </div>';

            $mpdf->SetHTMLHeader($headerHtml);
            $mpdf->SetFont('arial');
            $pagecount = $mpdf->SetSourceFile($tempFile);

            for ($i = 1; $i <= $pagecount; $i++) {
                $tplId = $mpdf->ImportPage($i);
                $mpdf->AddPage();
                $mpdf->UseTemplate($tplId);
            }

            $mpdf->Output($tempPdfFile1, 'F');
            // unlink($tempFile);
            Storage::delete($tempFile);

            // === PO ===
            $spreadsheet2 = IOFactory::load(Storage::path($templatePathPo));
            $worksheet2 = $spreadsheet2->getActiveSheet();

            $worksheet2->getPageSetup()
                        ->setFitToPage(true)
                        ->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
                        ->setPaperSize(PageSetup::PAPERSIZE_A4)
                        ->setFitToWidth(1)
                        ->setFitToHeight(1);

            $worksheet2->getPageMargins()
                        ->setTop(0.8)
                        ->setRight(0.4)
                        ->setLeft(0.4)
                        ->setBottom(0);

            $worksheet2->setCellValue('C4', $dataForm['applicationNumber']);
            $worksheet2->setCellValue('AB4', Carbon::parse($dataForm['applicationSubmittedDate'])->format('d-M-Y'));
            $worksheet2->setCellValue('W8', $dataForm['apllicationVendorPic']);

            $worksheet2->setCellValue('B7', $dataForm['vendorName']);
            $arrVendorAddress = preg_split('/\r\n|\r|\n/', $dataForm['vendorAddress']);
            if(count($arrVendorAddress) == 3) {
                $worksheet2->setCellValue('B8', $arrVendorAddress[0]);
                $worksheet2->setCellValue('B10', $arrVendorAddress[1]);
                $worksheet2->setCellValue('B12', $arrVendorAddress[2]);
            }
            else if(count($arrVendorAddress) == 2) {
                $worksheet2->setCellValue('B8', $arrVendorAddress[0]);
                $worksheet2->setCellValue('B10', $arrVendorAddress[1]);
            }
            else {
                $worksheet2->setCellValue('B8', $arrVendorAddress[0]);
            }

            $arrAppDelivery = preg_split('/\r\n|\r|\n/', $dataForm['applicationDelivery']);
            if(count($arrAppDelivery) == 3) {
                $worksheet2->setCellValue('B16', $arrAppDelivery[0]);
                $worksheet2->setCellValue('B17', $arrAppDelivery[1]);
                $worksheet2->setCellValue('B19', $arrAppDelivery[2]);
            }
            else if(count($arrAppDelivery) == 2) {
                $worksheet2->setCellValue('B16', $arrAppDelivery[0]);
                $worksheet2->setCellValue('B17', $arrAppDelivery[1]);
            }
            else if(count($arrAppDelivery) == 1) {
                $worksheet2->setCellValue('B16', $arrAppDelivery[0]);
            }

            $arrAppInvoice = preg_split('/\r\n|\r|\n/', $dataForm['applicationInvoice']);
            if(count($arrAppInvoice) == 3) {
                $worksheet2->setCellValue('W16', $arrAppInvoice[0]);
                $worksheet2->setCellValue('W17', $arrAppInvoice[1]);
                $worksheet2->setCellValue('W19', $arrAppInvoice[2]);
            }
            else if(count($arrAppInvoice) == 2) {
                $worksheet2->setCellValue('W16', $arrAppInvoice[0]);
                $worksheet2->setCellValue('W17', $arrAppInvoice[1]);
            }
            else if(count($arrAppInvoice) == 1) {
                $worksheet2->setCellValue('W16', $arrAppInvoice[0]);
            }

            $arrAppRemark = preg_split('/\r\n|\r|\n/', $dataForm['applicationRemark']);
            if(count($arrAppRemark) == 4) {
                $worksheet2->setCellValue('B65', $arrAppRemark[0]);
                $worksheet2->setCellValue('B66', $arrAppRemark[1]);
                $worksheet2->setCellValue('B67', $arrAppRemark[2]);
                $worksheet2->setCellValue('B68', $arrAppRemark[3]);
            }
            else if(count($arrAppRemark) == 3) {
                $worksheet2->setCellValue('B65', $arrAppRemark[0]);
                $worksheet2->setCellValue('B66', $arrAppRemark[1]);
                $worksheet2->setCellValue('B67', $arrAppRemark[2]);
            }
            else if(count($arrAppRemark) == 2) {
                $worksheet2->setCellValue('B65', $arrAppRemark[0]);
                $worksheet2->setCellValue('B66', $arrAppRemark[1]);
            }
            else if(count($arrAppRemark) == 1) {
                $worksheet2->setCellValue('B65', $arrAppRemark[0]);
            }

            $startItemRow = 25;
            $row = $startItemRow;
            for($x = 0; $x < count($dataForm['itemForm']); $x++) {
                if($dataForm['itemForm'][$x]['subtotalPrice'] == '' || $dataForm['itemForm'][$x]['subtotalPrice'] == '-' || $dataForm['itemForm'][$x]['subtotalPrice'] == '0') {
                    continue;
                }

                $rate = '';
                // if($dataForm['itemForm'][$x]['applianceItem'] == 'VAT' && ($dataForm['itemForm'][$x]['unitPrice'] != null && $dataForm['itemForm'][$x]['unitPrice'] != '' && $dataForm['itemForm'][$x]['unitPrice'] != '-' && $dataForm['itemForm'][$x]['unitPrice'] != '0')) {
                //     $rate = ' '.(int)$dataForm['itemForm'][$x].'%';
                // }
                if ($dataForm['itemForm'][$x]['applianceItem'] == 'VAT') {
                    // $rate = ' '.(int)$dataForm['itemForm'][$x]['unitPrice'].'%';
                }

                $worksheet2->setCellValue('A'.$row, $dataForm['itemForm'][$x]['itemNo']);
                $worksheet2->setCellValue('C'.$row, $dataForm['itemForm'][$x]['applianceItem'].$rate);

                if($dataForm['itemForm'][$x]['itemNo'] != '' && $dataForm['itemForm'][$x]['itemNo'] != null){
                    $worksheet2->setCellValue('U'.$row, $dataForm['itemForm'][$x]['quantity']);
                    $worksheet2->setCellValue('X'.$row, $dataForm['itemForm'][$x]['unitPrice']);
                }

                if($dataForm['itemForm'][$x]['applianceItem'] == 'Discount') {
                    $worksheet2->setCellValue('AD'.$row, '-'.$dataForm['itemForm'][$x]['subtotalPrice']);
                }
                else {
                    $worksheet2->setCellValue('AD'.$row, $dataForm['itemForm'][$x]['subtotalPrice']);
                }

                $row++;
            }

            $worksheet2->setCellValue('D68', date('n/d/Y H:i'));
            $worksheet2->setCellValue('X23', $dataForm['applicationCurrency']);
            $worksheet2->setCellValue('AD23', $dataForm['applicationCurrency']);
            $worksheet2->setCellValue('AA57', $dataForm['applicationCurrency']);
            $worksheet2->setCellValue('AD57', $dataForm['applicationGrandTotal']);
            $worksheet2->setCellValue('C60', Carbon::parse($dataForm['shipDate'])->format('d-M-Y'));

            if($dataForm['paymentType'] == 'TRANSFER') {
                $worksheet2->setCellValue('U60', 'X');
                $worksheet2->setCellValue('Z60', '');
            }
            else {
                $worksheet2->setCellValue('U60', '');
                $worksheet2->setCellValue('Z60', 'X');
            }

            $worksheet2->setCellValue('U69', $dataForm['approverPo']);

            $writer2 = new Xlsx($spreadsheet2);
            $filePath = 'private/doc_approval'.$dataForm['filePath'];
            Storage::makeDirectory($filePath);
            $tempFile = Storage::path($filePath.$dataForm['filename'].'_po.xlsx');
            $writer2->save($tempFile);

            // IOFactory::registerWriter('Pdf', Mpdf::class);
            // $pdfWriter2 = IOFactory::createWriter($spreadsheet2, 'Pdf');
            // $tempPdfFile2 = Storage::path($filePath . $dataForm['filename'] . '_po.pdf');
            // $pdfWriter2->save($tempPdfFile2);

            IOFactory::registerWriter('Pdf', Mpdf::class);
            $pdfWriter2 = IOFactory::createWriter($spreadsheet2, 'Pdf');
            $tempPdfFile2 = Storage::path($filePath . $dataForm['filename'] . '_po.pdf');
            $tempFile2 = storage_path('app/temp/temp_' . uniqid() . '.pdf');
            $pdfWriter2->save($tempFile2);

            $pdfContent = file_get_contents($tempFile2);
            $mpdfConfig = [
                'tempDir' => storage_path('app/temp'),
                'fontDir' => [storage_path('fonts')],
                'fontdata' => [
                    'arial' => [
                        'R' => 'arial.ttf',
                        'B' => 'arial_Bold.ttf',
                        'I' => 'arial_Italic.ttf',
                        'BI' => 'arial_Bold_Italic.ttf',
                    ]
                ],
                'default_font' => 'arial',
                'margin_header' => 0,
                'margin_top' => 0,    // Margin atas lebih besar untuk header
                'margin_bottom' => 0,
                'margin_left' => 0,
                'margin_right' => 0
            ];
            $mpdf = new \Mpdf\Mpdf($mpdfConfig);
            $headerHtml = '
            <div style="text-align: center;">
                <img src="' . $headerPath . '" style="width: 100%; height: 14mm; margin-top: 5mm; margin-left: 10mm; margin-right: 10mm; margin-bottom: 2mm" />
            </div>';

            $mpdf->SetHTMLHeader($headerHtml);
            $mpdf->SetFont('arial');
            $pagecount = $mpdf->SetSourceFile($tempFile2);

            for ($i = 1; $i <= $pagecount; $i++) {
                $tplId = $mpdf->ImportPage($i);
                $mpdf->AddPage();
                $mpdf->UseTemplate($tplId);
            }

            $mpdf->Output($tempPdfFile2, 'F');
            // unlink($tempFile2);
            Storage::delete($tempFile2);
            // === END PO

            // Path ke file PDF yang akan digabungkan
            // $tempPdfFile1 = Storage::path($filePath . $dataForm['filename'] . '_app.pdf');
            // $tempPdfFile2 = Storage::path($filePath . $dataForm['filename'] . '_po.pdf');

            $combinedPdfFile = Storage::path($filePath . $dataForm['filename'] . '.pdf');
            $pdf = new Fpdi();

            $pageCount1 = $pdf->setSourceFile($tempPdfFile2);
            for ($i = 1; $i <= $pageCount1; $i++) {
                $template = $pdf->importPage($i);
                $pdf->AddPage();
                $pdf->useTemplate($template);
            }

            $pageCount2 = $pdf->setSourceFile($tempPdfFile1);
            for ($i = 1; $i <= $pageCount2; $i++) {
                $template = $pdf->importPage($i);
                $pdf->AddPage();
                $pdf->useTemplate($template);
            }

            $pdf->Output($combinedPdfFile, 'F');
            // unlink($tempPdfFile1);
            // unlink($tempPdfFile2);
            Storage::delete($tempPdfFile1);
            // Storage::delete($tempPdfFile2);
        }
        else if($documentType == '6') { // COMPARISON FORM
            // dd($this->all());
            $dataForm = $this->dataForm['dataForm'];
            $templatePath = 'private/template/TEMPLATE_COMPARISON_FORM.xlsx';
            $filePath = 'private/doc_approval'.$dataForm['filePath'];
            $tempPdfFile = Storage::path($filePath.$dataForm['filename'].'.pdf');

            $spreadsheet = IOFactory::load(Storage::path($templatePath));

            if (!Storage::exists($filePath)) {
                Storage::makeDirectory($filePath);
            }

            $validity = $dataForm['validity'] != null ? $dataForm['validity'] : '';

            $spreadsheet->getDefaultStyle()->getFont()->setName('arial');
            $spreadsheet->getDefaultStyle()->getAlignment()->setWrapText(true);

            $worksheet = $spreadsheet->getActiveSheet();
            $worksheet->getPageSetup()
                        ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                        ->setPaperSize(PageSetup::PAPERSIZE_A4);

            $worksheet->getPageMargins()
                        ->setTop(0.2)
                        ->setLeft(0.2)
                        ->setRight(0.2)
                        ->setBottom(0.2);

            $worksheet->getPageSetup()->setFitToPage(true)
                                    ->setFitToWidth(1)
                                    ->setFitToHeight(1);

            $worksheet->setCellValue('A3', 'Comparison for '.$dataForm['title']);
            $worksheet->setCellValue('A4', 'For The Month of '.$dataForm['description']);
            $worksheet->setCellValue('C5', Carbon::parse($dataForm['date'])->format('d-M-Y'));
            $worksheet->setCellValue('C6', $validity);
            $worksheet->setCellValue('E37', $dataForm['selectedVendorName']);

            $comparisonNote = preg_split('/\r\n|\r|\n/', $dataForm['comparisonNote']);
            if(count($comparisonNote) > 1) {
                $worksheet->setCellValue('E38', $comparisonNote[0]);
                $worksheet->setCellValue('E39', $comparisonNote[1]);
            }
            else {
                $worksheet->setCellValue('E38', $comparisonNote[0]);
            }

            $vendor = 1;
            foreach($dataForm['vendorComparison'] as $rowVendor){
                if($vendor == 1) {
                    $worksheet->setCellValue('H8', $rowVendor['vendorName']);
                }
                else if($vendor == 2) {
                    $worksheet->setCellValue('L8', $rowVendor['vendorName']);
                }
                else if($vendor == 3) {
                    $worksheet->setCellValue('P8', $rowVendor['vendorName']);
                }

                $vendor++;
            }

            $row = 10;
            $no = 1;
            $selectedCol = [];
            foreach($dataForm['itemForm'] as $rowItem){
                $rowComparison = false;
                $rate = '';
                foreach($rowItem['arrItemVendor'] as $rowItemVendor){
                    if($rowItemVendor['totalPrice'] != '' && $rowItemVendor['totalPrice'] != null) {
                        $rowComparison = true;
                    }

                    // if($rowItem['applianceItem'] == 'VAT' && ($rowItemVendor['unitPriceValue'] != null && $rowItemVendor['unitPriceValue'] != '')) {
                    //     $dppOtherValue = $rowItemVendor['dppOtherValue'] != null ? ' x '.$rowItemVendor['dppOtherValue'] : '';
                    //     $rate = ' '.(int)$rowItemVendor['unitPriceValue'].'%'.$dppOtherValue;
                    // }
                }

                if($rowComparison == false) {
                    continue;
                }


                $worksheet->setCellValue('C'.$row, $rowItem['applianceItem'].$rate);

                // if($rowItem['criteriaId'] != null && $rowItem['criteriaId'] != '') {
                if($rowItem['applianceItem'] != 'Delivery Fee' && $rowItem['applianceItem'] != 'Discount' && $rowItem['applianceItem'] != 'Total Price' && $rowItem['applianceItem'] != 'VAT' && $rowItem['applianceItem'] != 'Total Price + VAT'){
                    $worksheet->setCellValue('A'.$row, $no);
                    $worksheet->setCellValue('F'.$row, $rowItem['unitQty']);
                    $worksheet->setCellValue('G'.$row, $rowItem['unitName']);
                }

                // $colNum = Coordinate::columnIndexFromString($col);
                // $colNum++;
                // $col = Coordinate::stringFromColumnIndex($colNum);
                // $ccy = null;
                if($rowItem['currency'] == 'IDR') {
                    $ccy = 'Rp';
                }
                else if($rowItem['currency'] == 'USD') {
                    $ccy = '$';
                }

                $vendor = 1;
                foreach($rowItem['arrItemVendor'] as $rowItemVendor){
                    // if($rowItem['criteriaId'] == null || $rowItem['criteriaId'] == '') {
                    $rate = '';
                    if($rowItem['applianceItem'] == 'Delivery Fee' || $rowItem['applianceItem'] == 'Discount' || $rowItem['applianceItem'] == 'Total Price' || $rowItem['applianceItem'] == 'Total Price + VAT'){
                        $unitPriceValue = '';
                    }
                    else if($rowItem['applianceItem'] == 'VAT'){
                        // if($rowItem['applianceItem'] == 'VAT' && ($rowItemVendor['unitPriceValue'] != null && $rowItemVendor['unitPriceValue'] != '')) {
                        //     $dppOtherValue = $rowItemVendor['dppOtherValue'] != null ? ' x '.$rowItemVendor['dppOtherValue'] : '';
                        //     $rate =  '%'.$dppOtherValue;
                        // }

                        // $unitPriceValue = $rowItemVendor['unitPriceValue'].$rate;
                        $unitPriceValue = '';
                    }
                    else {
                        $unitPriceValue = $rowItemVendor['unitPriceValue'];
                    }

                    if($rowItem['applianceItem'] == 'Discount' && $rowItemVendor['totalPrice'] > 0) {
                        $totalPrice = '('.number_format($rowItemVendor['totalPrice'], 0, '.', ',').')';
                    }
                    else {
                        $totalPrice = $rowItemVendor['totalPrice'];
                    }

                    if($vendor == 1) {
                        $worksheet->setCellValue('H'.$row, ($unitPriceValue == '' || $unitPriceValue == '0' || $rowItem['applianceItem'] == 'VAT') ? '' : $ccy);
                        $worksheet->setCellValue('I'.$row, $unitPriceValue);
                        $worksheet->setCellValue('J'.$row, ($totalPrice == '' || $totalPrice == '0') ? '' : $ccy);
                        $worksheet->setCellValue('K'.$row, $totalPrice);

                        if($rowItemVendor['comparisonVendorId'] == $dataForm['selectedVendorComparison']) {
                            $selectedCol = ['J','K'];
                        }
                    }
                    else if($vendor == 2) {
                        $worksheet->setCellValue('L'.$row, ($unitPriceValue == '' || $unitPriceValue == '0' || $rowItem['applianceItem'] == 'VAT') ? '' : $ccy);
                        $worksheet->setCellValue('M'.$row, $unitPriceValue);
                        $worksheet->setCellValue('N'.$row, ($totalPrice == '' || $totalPrice == '0') ? '' : $ccy);
                        $worksheet->setCellValue('O'.$row, $totalPrice);

                        if($rowItemVendor['comparisonVendorId'] == $dataForm['selectedVendorComparison']) {
                            $selectedCol = ['N','O'];
                        }
                    }
                    else if($vendor == 3) {
                        $worksheet->setCellValue('P'.$row, ($unitPriceValue == '' || $unitPriceValue == '0' || $rowItem['applianceItem'] == 'VAT') ? '' : $ccy);
                        $worksheet->setCellValue('Q'.$row, $unitPriceValue);
                        $worksheet->setCellValue('R'.$row, ($totalPrice == '' || $totalPrice == '0') ? '' : $ccy);
                        $worksheet->setCellValue('S'.$row, $totalPrice);

                        if($rowItemVendor['comparisonVendorId'] == $dataForm['selectedVendorComparison']) {
                            $selectedCol = ['R','S'];
                        }
                    }

                    $vendor++;
                }

                $row++;
                $no++;
            }

            $worksheet->getStyle('J'.($row - 1))->getFont()->setBold(true);
            $worksheet->getStyle('K'.($row - 1))->getFont()->setBold(true);
            $worksheet->getStyle('N'.($row - 1))->getFont()->setBold(true);
            $worksheet->getStyle('O'.($row - 1))->getFont()->setBold(true);
            $worksheet->getStyle('R'.($row - 1))->getFont()->setBold(true);
            $worksheet->getStyle('S'.($row - 1))->getFont()->setBold(true);
            foreach ($selectedCol as $selectedCell) {
                // dd($selectedCol);
                // $worksheet->getStyle($selectedCell.($row - 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF00FF00');
                $worksheet->getStyle($selectedCell.($row - 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFA4FFA4');
            }

            // $worksheet->getStyle('K10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFA4FFA4');

            // $worksheet->getStyle('H10:H35')->getNumberFormat()->setFormatCode('_([$Rp-421]* #,##_);_([$Rp-421]* (#,##);_([$Rp-421]* "-"??_);_(@_)');
            // $worksheet->getStyle('I10:I35')->getNumberFormat()->setFormatCode('_([$Rp-421]* #,##_);_([$Rp-421]* (#,##);_([$Rp-421]* "-"??_);_(@_)');

            // $worksheet->getStyle('J10:J35')->getNumberFormat()->setFormatCode('_([$Rp-421]* #,##_);_([$Rp-421]* (#,##);_([$Rp-421]* "-"??_);_(@_)');
            // $worksheet->getStyle('K10:K35')->getNumberFormat()->setFormatCode('_([$Rp-421]* #,##_);_([$Rp-421]* (#,##);_([$Rp-421]* "-"??_);_(@_)');

            // $worksheet->getStyle('L10:L35')->getNumberFormat()->setFormatCode('_([$Rp-421]* #,##_);_([$Rp-421]* (#,##);_([$Rp-421]* "-"??_);_(@_)');
            // $worksheet->getStyle('M10:M35')->getNumberFormat()->setFormatCode('_([$Rp-421]* #,##_);_([$Rp-421]* (#,##);_([$Rp-421]* "-"??_);_(@_)');

            // $worksheet->getStyle('H10:M35')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // IOFactory::registerWriter('Pdf', Mpdf::class);
            // $writer = IOFactory::createWriter($spreadsheet, 'Pdf');
            // $filePath = 'private/doc_approval'.$dataForm['filePath'];
            // $tempFile = Storage::path($filePath);
            // $writer->save($tempFile);
            // return true;

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
                'default_font' => 'arial' // Atur Arial Narrow sebagai default
            ]);

            $pdfWriter = new Mpdf($spreadsheet);
            $pdfWriter->setFont('arial');
            $pdfWriter->save($tempPdfFile);
        }
        else if($documentType == '4') { // INSPECTION FORM
            // dd($this->all());
            $dataForm = $this->dataForm;
            $templatePath = 'private/template/F.PUR-02.02-04_INSPECTION_FINAL.xlsx';
            $filePath = 'private/doc_approval'.$dataForm['filePath'];
            $tempPdfFile = Storage::path($filePath.$dataForm['filename'].'.pdf');

            if (Storage::exists($tempPdfFile)) {
                Storage::delete($tempPdfFile);
            }

            $spreadsheet = IOFactory::load(Storage::path($templatePath));

            if (!Storage::exists($filePath)) {
                Storage::makeDirectory($filePath);
            }

            $spreadsheet->getDefaultStyle()->getFont()->setName('arial');
            $spreadsheet->getDefaultStyle()->getAlignment()->setWrapText(true);

            $worksheet = $spreadsheet->getActiveSheet();
            $worksheet->getPageSetup()
                        ->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
                        ->setPaperSize(PageSetup::PAPERSIZE_A4);

            $worksheet->getPageMargins()
                        ->setTop(0.3)
                        ->setLeft(0.3)
                        ->setRight(0.3)
                        ->setBottom(0.3);

            $worksheet->getPageSetup()->setFitToPage(true)
                                    ->setFitToWidth(1)
                                    ->setFitToHeight(1);

            $worksheet->setCellValue('E6', $dataForm['poNumber']);
            $worksheet->setCellValue('E8', Carbon::parse($dataForm['poDate'])->format('d-M-Y'));
            $worksheet->setCellValue('B11', $dataForm['vendorName']);

            $arrVendorAddress = preg_split('/\r\n|\r|\n/', $dataForm['vendorAddress']);
            if(count($arrVendorAddress) > 1) {
                $worksheet->setCellValue('B13', $arrVendorAddress[0]);
                $worksheet->setCellValue('B15', $arrVendorAddress[1]);
            }
            else {
                $worksheet->setCellValue('A13', $arrVendorAddress[0]);
            }

            $arrDelivery = preg_split('/\r\n|\r|\n/', $dataForm['inspectionDelivery']);
            if(count($arrDelivery) == 3) {
                $worksheet->setCellValue('B22', $arrDelivery[0]);
                $worksheet->setCellValue('B24', $arrDelivery[1]);
                $worksheet->setCellValue('B26', $arrDelivery[2]);
            }
            else if(count($arrDelivery) == 2) {
                $worksheet->setCellValue('B22', $arrDelivery[0]);
                $worksheet->setCellValue('B24', $arrDelivery[1]);
            }
            else if(count($arrDelivery) == 1) {
                $worksheet->setCellValue('B22', $arrDelivery[0]);
            }

            $worksheet->setCellValue('Y11', Carbon::parse($dataForm['shipDate'])->format('d-M-Y'));

            $startItemRow = 32;
            $row = $startItemRow;
            $no = 1;
            for($x = 0; $x < count($dataForm['itemForm']); $x++) {
                $worksheet->setCellValue('A'.$row, $no);
                $worksheet->setCellValue('C'.$row, $dataForm['itemForm'][$x]['applianceItem']);
                $worksheet->setCellValue('V'.$row, $dataForm['itemForm'][$x]['quantity']);
                $row++;
                $no++;
            }

            $arrRemark = preg_split('/\r\n|\r|\n/', $dataForm['inspectionRemarks']);
            if(count($arrRemark) == 4) {
                $worksheet->setCellValue('B65', $arrRemark[0]);
                $worksheet->setCellValue('B66', $arrRemark[1]);
                $worksheet->setCellValue('B67', $arrRemark[2]);
                $worksheet->setCellValue('B68', $arrRemark[3]);
            }
            else if(count($arrRemark) == 3) {
                $worksheet->setCellValue('B65', $arrRemark[0]);
                $worksheet->setCellValue('B66', $arrRemark[1]);
                $worksheet->setCellValue('B67', $arrRemark[2]);
            }
            else if(count($arrRemark) == 2) {
                $worksheet->setCellValue('B65', $arrRemark[0]);
                $worksheet->setCellValue('B66', $arrRemark[1]);
            }

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
                'default_font' => 'arial' // Atur Arial Narrow sebagai default
            ]);

            $pdfWriter = new Mpdf($spreadsheet);
            $pdfWriter->setFont('arial');
            $pdfWriter->save($tempPdfFile);
        }
		Log::info('[Generate PDF] Completed');
    }

    private function formatNumber($number) {
        return $number == round($number) ?
            number_format($number, 0, '.', ',') :
            number_format($number, 2, '.', ',');
    }
}
