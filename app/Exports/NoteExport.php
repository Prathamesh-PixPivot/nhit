<?php

namespace App\Exports;

use App\Helpers\Helper;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;

class NoteExport implements FromCollection, WithHeadings, WithStyles, WithCustomStartCell, WithEvents, WithMapping
{
    protected $notes;

    public function __construct($notes)
    {
        $this->notes = $notes;
    }

    public function collection()
    {
        return $this->notes;
    }
    public function map($note): array
    {
        static $serial = 1;

        $lastLog = $note->approvalLogs->last();
        $nextApprover = '';

        if ($note->status !== 'A' && $lastLog && $lastLog->status === 'A') {
            $nextStep = $lastLog->approvalStep->nextOnApprove ?? null;
            if ($nextStep) {
                $nextApprover = $nextStep->name;
            }
        }
        $getSteps = function ($logs, $count = 10) {
            return collect($logs)
                ->sortBy('created_at')
                ->take($count)
                ->map(function ($log) {
                    $statusMap = [
                        'A' => 'Approved',
                        'P' => 'Pending',
                        'R' => 'Rejected',
                        'PMPL' => 'Sent for PMC',
                    ];

                    $name = optional($log->reviewer)->name ?? '-';
                    $status = $statusMap[$log->status] ?? 'Unknown';
                    $date = optional($log->created_at)?->setTimezone('Asia/Kolkata')->format('d-m-Y H:i') ?? '-';

                    return "$name ($status) $date";
                })
                ->values()
                ->pad($count, '-')
                ->toArray();
        };

        // Expense Steps (10), Payment Steps (4), Bank Letter Steps (3)
        $expenseSteps = $getSteps($note->approvalLogs ?? [], 10);
        $paymentSteps = $getSteps($note->paymentOneNotes?->paymentApprovalLogs ?? [], 4);
        $bankLetterSteps = $getSteps($note->paymentOneNotes?->bankLetter ?? [], 3);
        $array = array_merge(
            [
                $serial++,
                'NWPPL', //
                $note->vendor->project ?? '-',
                $note->department->name ?? '-',
                $note->supplier->vendor_name ?? '-',
                $note->msme_classification ?? '-',
                $note->activity_type ?? '-',
                Carbon::parse($note->invoice_date)->format('d-m-Y') ?? '-',
                $note->invoice_number ?? '-',
                Helper::formatIndianNumber($note->invoice_value) ?? '-',
                Carbon::parse($note->supply_period_start)->format('d-m-Y') . ' - ' . Carbon::parse($note->supply_period_end)->format('d-m-Y'),
                $note->nature_of_expenses ?? '-',
            ],
            $expenseSteps,
            $paymentSteps,
            $bankLetterSteps,
            [$note->paymentOneNotes?->bank_payment_date ?? '-', $nextApprover],
        );

        return $array;
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function headings(): array
    {
        return [
            [
                'S. No.', //
                'Entity',
                'Project Name',
                'User Department',
                'Name of Supplier',
                'MSME Classification',
                'Activity Type',
                'Invoice Date',
                'Invoice Number',
                'Invoice Value',
                'Period of Supply of services/goods invoiced',
                'Nature of Work (Cost Code)',
                'Expense Approval Note',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'Payment Approval Note',
                '',
                '',
                '',
                'Bank Letter/RTGS Letter',
                '',
                '',
                'Bank Payment Date',
                'Next Approver',
            ],
            [
                '', //
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'Step 1',
                'Step 2',
                'Step 3',
                'Step 4',
                'Step 5',
                'Step 6',
                'Step 7',
                'Step 8',
                'Step 9',
                'Step 10',
                'Step 1',
                'Step 2',
                'Step 3',
                'Step 4',
                'Step 1',
                'Step 2',
                'Step 3',
                'Reviewer: Name Date Time',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:B2');
                $sheet->mergeCells('C1:C2');
                $sheet->mergeCells('D1:D2');
                $sheet->mergeCells('E1:E2');
                $sheet->mergeCells('F1:F2');
                $sheet->mergeCells('G1:G2'); // Activity Type
                $sheet->mergeCells('H1:H2'); // Invoice Date
                $sheet->mergeCells('I1:I2'); // Invoice Number
                $sheet->mergeCells('J1:J2'); // Invoice Value
                $sheet->mergeCells('K1:K2'); // Period of Supply
                $sheet->mergeCells('L1:L2'); // Nature of Work

                $sheet->mergeCells('M1:V1'); // Expense Approval Note (10 steps)
                $sheet->mergeCells('W1:Z1'); // Payment Approval Note (4 steps)
                $sheet->mergeCells('AA1:AC1'); // Bank Letter/RTGS Letter (3 steps)
                $sheet->mergeCells('AD1:AD2'); // Bank Payment Date

                // Center align merged cells
                $sheet->getStyle('A1:AD2')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A1:AD2')->getAlignment()->setVertical('center');

                // Optionally: Autosize columns
                foreach (range('A', 'AD') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
