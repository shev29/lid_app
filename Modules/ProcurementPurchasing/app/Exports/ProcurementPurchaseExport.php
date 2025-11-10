<?php

namespace Modules\ProcurementPurchasing\Exports;

use App\Services\SafeToken;
use Modules\ProcurementPurchasing\Models\ProcurementPurchasingModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProcurementPurchaseExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            new PoSheet($this->filters),
            new OrderFormSheet($this->filters)
        ];
    }
}

class PoSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithTitle, WithMapping, WithStyles, WithEvents
{
    protected $filters;
    protected $modelProcurement;
    protected $csvSettings;
    protected $rowNumber = 0; // Untuk nomor urut
    protected $dataCollection;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        $this->modelProcurement = new ProcurementPurchasingModel();
        // Set CSV config
        $this->csvSettings = [
            'delimiter' => ';',
            'enclosure' => '"',
            'line_ending' => PHP_EOL,
            'use_bom' => true,
            'include_separator_line' => false,
            'excel_compatibility' => false,
        ];
    }

    public function title(): string
    {
        return 'PO Invoiced';
    }

    public function collection()
    {
        $this->dataCollection = $this->query()->get();
        return $this->dataCollection;
    }

    protected function query()
    {
        $companyId = $this->filters['exportCompany'];
        $employeeId = $this->filters['exportApplicant'] ?? 'ALL';
        if ($employeeId !== 'ALL') {
            $decryptedEmployee = SafeToken::decode($employeeId);
            $employeeId = $decryptedEmployee['id'] ?? null;
        }

        // $companyId = '316';
        // $employeeId = '314010004';

        $query = $this->modelProcurement->docApprovalOrderApplication()
            ->from('doc_approval_order_application as a')
            ->leftJoin('master_company as b', function ($join) {
                $join->on('b.company_id', 'a.company_id');
            })
            ->leftJoin('doc_approval_order_inspection as d', function ($join) {
                $join->on('d.application_id', 'a.application_id')
                    ->where('d.inspection_form_status', 8) // 8 = APPROVED
                    ->where('d.is_active', 1);
            })
            ->leftJoin('doc_approval_order_invoice as e', function ($join) {
                $join->on('e.application_id', 'a.application_id')
                    ->where('e.is_active', 1);
            })
            ->leftJoin('vw_master_employee_all as c', function ($join) {
                $join->on('c.employee_id', 'a.created_by');
            });

        $query->where('a.is_active', 1);
        if (!empty($companyId) && $companyId != 'ALL') {
            $query->where('a.company_id', $companyId);
        }

        if ($employeeId && $employeeId !== 'ALL') {
            $query->where('a.created_by', $employeeId);
        }

        // if ($this->filters['exportPoStatus'] == 'ALL') {
        //     $query->whereIn('a.application_form_status', ['22','9','12']);
        // }
        // else {
        //     $query->where('a.application_form_status', '22'); // 22 = INVOICED
        // }

        if ($this->filters['exportPoStatus'] == 'ALL') {
            $query->whereIn('a.application_form_status', ['22', '21', '24', '8']);
        } else {
            $query->where('a.application_form_status', '22'); // 22 = INVOICED
        }

        if (!empty($this->filters['exportOrderDate']) && !empty($this->filters['date-range-picker-end-date-exportOrderDate'])) {
            $startDate = Carbon::parse($this->filters['exportOrderDate'])->toDateString();
            $endDate   = Carbon::parse($this->filters['date-range-picker-end-date-exportOrderDate'])->toDateString();
            $query->whereBetween('a.open_order_at', [$startDate, $endDate]);
        }

        if (!empty($this->filters['exportReceivedDate']) && !empty($this->filters['date-range-picker-end-date-exportReceivedDate'])) {
            $startDate = Carbon::parse($this->filters['exportReceivedDate'])->toDateString();
            $endDate   = Carbon::parse($this->filters['date-range-picker-end-date-exportReceivedDate'])->toDateString();
            $query->whereBetween('d.incoming_date', [$startDate, $endDate]);
        }

        if (!empty($this->filters['exportInvDate']) && !empty($this->filters['date-range-picker-end-date-exportInvDate'])) {
            $startDate = Carbon::parse($this->filters['exportInvDate'])->toDateString();
            $endDate   = Carbon::parse($this->filters['date-range-picker-end-date-exportInvDate'])->toDateString();
            $query->whereBetween('e.invoice_date', [$startDate, $endDate]);
        }

        if ($this->filters['exportSortBy'] === 'LATEST') {
            $query->latest('a.final_decision_at')
                ->oldest('e.invoice_id');
        } else if ($this->filters['exportSortBy'] === 'OLDEST') {
            $query->oldest('a.final_decision_at')
                ->oldest('e.invoice_id');
        }

        $query->select([
            'a.application_id',
            'a.application_title',
            'a.application_number',
            'b.company_name',
            'c.employee_name',
            'a.open_order_at',
            'd.incoming_date',
            'e.invoice_date',
            'a.application_grand_total',
            'a.application_currency',
            'a.vendor_account',
            'a.vendor_name',
            'a.purchase_type',
            'a.budget_no',
            'e.invoice_id',
            'e.invoice_number',
            'e.invoice_amount'
        ]);

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Company',
            'PO Applied By',
            'Application Title',
            'PO Number',
            'Purchase Type',
            'Budget Number',
            'Vendor Account',
            'Vendor Name',
            'Order Date',
            'Received Date',
            'Invoice Date',
            'Invoice Number',
            'Amount',
            'Currency',
        ];
    }

    public function map($purchase): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber, // Nomor urut
            $purchase->company_name ?? '',
            $purchase->employee_name ?? '',
            $purchase->application_title ?? '',
            $purchase->application_number,
            $purchase->purchase_type ?? '',
            $purchase->budget_no ?? '',
            $purchase->vendor_account ?? '',
            $purchase->vendor_name ?? '',
            $purchase->open_order_at ? date('d-m-Y', strtotime($purchase->open_order_at)) : '',
            $purchase->incoming_date ? date('d-m-Y', strtotime($purchase->incoming_date)) : '',
            $purchase->invoice_date ? date('d-m-Y', strtotime($purchase->invoice_date)) : '',
            $purchase->invoice_number ?? '',
            $this->formatNumber($purchase->invoice_amount),
            $purchase->application_currency ?? '',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Sisipkan 5 baris kosong di awal
                $sheet->insertNewRowBefore(1, 5);

                // Set informasi filter pada baris 1-4
                $sheet->mergeCells('A1:C1');
                $sheet->setCellValue('A1', 'Purchase Order');
                $sheet->mergeCells('D1:E1');
                $sheet->setCellValue('D1', 'Generate at ' . Carbon::now()->format('Y-m-d H:i:s'));
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
                $sheet->getStyle('A1:A5')->getAlignment()->setHorizontal('left');
                $sheet->getStyle('D1')->getFont()->setItalic(true);

                // with coordinates:
                // $sheet->freezePaneByColumnAndRow(1,6);

                // with cell name:
                $sheet->freezePane('A7');

                // Order Date
                $sheet->mergeCells('A2:C2');
                $orderDate = '';
                if (!empty($this->filters['exportOrderDate']) && !empty($this->filters['date-range-picker-end-date-exportOrderDate'])) {
                    $orderDate = Carbon::parse($this->filters['exportOrderDate'])->format('d-m-Y') . ' to ' . Carbon::parse($this->filters['date-range-picker-end-date-exportOrderDate'])->format('d-m-Y');
                } else {
                    $orderDate = '-- ALL DATE --';
                }
                $sheet->setCellValue('A2', 'Order Date : ' . $orderDate);

                // Received Date
                $sheet->mergeCells('A3:C3');
                $receivedDate = '';
                if (!empty($this->filters['exportReceivedDate']) && !empty($this->filters['date-range-picker-end-date-exportReceivedDate'])) {
                    $receivedDate = Carbon::parse($this->filters['exportReceivedDate'])->format('d-m-Y') . ' to ' . Carbon::parse($this->filters['date-range-picker-end-date-exportReceivedDate'])->format('d-m-Y');
                } else {
                    $receivedDate = '-- ALL DATE --';
                }
                $sheet->setCellValue('A3', 'Received Date : ' . $receivedDate);

                // Invoice Date
                $sheet->mergeCells('A4:C4');
                $invoiceDate = '';
                if (!empty($this->filters['exportInvDateStart']) && !empty($this->filters['date-range-picker-end-date-exportInvDate'])) {
                    $invoiceDate = Carbon::parse($this->filters['exportInvDateStart'])->format('d-m-Y') . ' to ' . Carbon::parse($this->filters['date-range-picker-end-date-exportInvDate'])->format('d-m-Y');
                } else {
                    $invoiceDate = '-- ALL DATE --';
                }
                $sheet->setCellValue('A4', 'Invoice Date : ' . $invoiceDate);

                // Baris 5 dibiarkan kosong

                // Style untuk header di baris 6
                $sheet->getRowDimension('6')->setRowHeight(22);
                $sheet->getStyle('A6:O6')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFD9D9D9'],
                    ],
                    // 'borders' => [
                    //     'allBorders' => [
                    //         'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    //     ],
                    // ],
                ]);

                $this->mergeInvoiceNumbers($sheet);
            },
        ];
    }

    private function mergeInvoiceNumbers($sheet)
    {
        if (!$this->dataCollection || $this->dataCollection->isEmpty()) {
            return;
        }

        $row = 7;
        $arrApplicationId = [];
        $prevApplicationId = $prevInvoiceId = $prevInvoiceNo = null;
        foreach ($this->dataCollection as $item) {
            if ($prevApplicationId == $item->application_id && ($prevInvoiceId == $item->invoice_id || $prevInvoiceNo == $item->invoice_number) && $prevInvoiceId != null) {
                if (!array_key_exists($item->application_id, $arrApplicationId)) {
                    $arrApplicationId[$item->application_id][] = $row - 1;
                }

                $arrApplicationId[$item->application_id][] = $row;
            }

            $prevApplicationId = $item->application_id;
            $prevInvoiceId = $item->invoice_id;
            $prevInvoiceNo = $item->invoice_number;
            $row++;
        }

        foreach ($arrApplicationId as $rowApplicationId) {
            if (count($rowApplicationId) > 1) {
                $collection = collect($rowApplicationId);
                $startRow = $collection->first();
                $endRow = $collection->last();
                $sheet->mergeCells('N' . $startRow . ':N' . $endRow);
                $sheet->getStyle('N' . $startRow . ':N' . $endRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            }
        }
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getColumnDimension('D')->setWidth(60);
        $sheet->getColumnDimension('D')->setAutoSize(false);
        $sheet->getColumnDimension('F')->setWidth(35);
        $sheet->getColumnDimension('F')->setAutoSize(false);
        $sheet->getColumnDimension('I')->setWidth(35);
        $sheet->getColumnDimension('I')->setAutoSize(false);
        $sheet->getStyle('D')->getAlignment()->setWrapText(true);
        $sheet->getStyle('F')->getAlignment()->setWrapText(true);
        $sheet->getStyle('I')->getAlignment()->setWrapText(true);

        $lastRow = $sheet->getHighestRow(); // Ambil jumlah baris terakhir
        $rangeRow7AndBelow = 'A7:' . $sheet->getHighestColumn() . $lastRow;
        $sheet->getStyle($rangeRow7AndBelow)
            ->getAlignment()
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        for ($row = 1; $row <= $lastRow; $row++) {
            $color = ($row % 2 == 0) ? 'FFFFFF' : 'EFEFEF';
            $range = 'A' . $row . ':' . $sheet->getHighestColumn() . $row;
            $sheet->getStyle($range)
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB($color);
        }

        return [
            1 => ['font' => ['bold' => true]],
            'A' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'G' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'H' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
            'N' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]],
        ];
    }

    private function formatNumber($number)
    {
        return $number == round($number) ?
            number_format($number, 0, '.', ',') :
            number_format($number, 2, '.', ',');
    }
}

class OrderFormSheet implements FromCollection, WithTitle, WithHeadings
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Order Form Details';
    }

    public function headings(): array
    {
        return [
            'Test Column',
            'Test Value'
        ];
    }

    public function collection()
    {
        // Contoh data summary
        return collect([
            ['Test Order Form sheet', 150],
            ['Test Order Form sheet', 'Rp 4.250.000'],
            ['Test Order Form sheet', 'PT. Test']
        ]);
    }
}
