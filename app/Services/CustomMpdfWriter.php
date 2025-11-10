<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf as BaseMpdf;

class CustomMpdfWriter extends BaseMpdf
{
    protected $mpdfConfig;

    public function __construct($spreadsheet, $mpdfConfig) 
    {
        parent::__construct($spreadsheet);
        $this->mpdfConfig = $mpdfConfig;
    }

    public function createExternalWriterInstance($orientation, $unit, $paperSize) 
    {
        return new \Mpdf\Mpdf($this->mpdfConfig);
    }
}