<?php

namespace App\Exports;

use App\Models\Income;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IncomeReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private int $userId,
        private string $from,
        private string $to,
        private string $currency = 'PKR',
    ) {
    }

    public function collection(): Enumerable
    {
        return Income::query()
            ->where('user_id', $this->userId)
            ->whereBetween('date', [$this->from, $this->to])
            ->with(['category', 'paymentMethod'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Title',
            'Category',
            'Payment Method',
            'Amount (' . $this->currency . ')',
            'Recurring',
            'Description',
        ];
    }

    public function map($income): array
    {
        return [
            $income->date->format('Y-m-d'),
            $income->title,
            $income->category?->name ?? '',
            $income->paymentMethod?->name ?? '',
            (float) $income->amount,
            $income->is_recurring
                ? ($income->recurring_frequency?->label() ?? 'Yes')
                : 'No',
            $income->description ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Income Report';
    }
}