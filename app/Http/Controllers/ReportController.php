<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exports\ExpenseReportExport;
use App\Exports\IncomeReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(private ReportService $reports, private \App\Services\BudgetService $budgets,) {
    }

    public function index(): View
    {
        return view('reports.index');
    }

    public function income(Request $request): View
    {
        $range = $this->reports->resolveRange(
            (string) $request->input('range', 'this_month'),
            $request->input('from'),
            $request->input('to')
        );

        $data = $this->reports->incomeReport($request->user()->id, $range['from'], $range['to']);

        return view('reports.income', [
            'range'    => $range,
            'data'     => $data,
            'currency' => $request->user()->profile->currency ?? 'PKR',
        ]);
    }

    public function expense(Request $request): View
    {
        $range = $this->reports->resolveRange(
            (string) $request->input('range', 'this_month'),
            $request->input('from'),
            $request->input('to')
        );

        $data = $this->reports->expenseReport($request->user()->id, $range['from'], $range['to']);

        return view('reports.expense', [
            'range'    => $range,
            'data'     => $data,
            'currency' => $request->user()->profile->currency ?? 'PKR',
        ]);
    }

        public function monthly(Request $request): View
    {
        $range = $this->resolve($request);
        $data  = $this->reports->monthlyReport($request->user()->id, $range['from'], $range['to']);

        return view('reports.monthly', [
            'range'    => $range,
            'data'     => $data,
            'currency' => $this->currency($request),
        ]);
    }

    public function yearly(Request $request): View
    {
        $range = $this->resolve($request, 'this_year');
        $data  = $this->reports->yearlyReport($request->user()->id, $range['from'], $range['to']);

        return view('reports.yearly', [
            'range'    => $range,
            'data'     => $data,
            'currency' => $this->currency($request),
        ]);
    }

    public function category(Request $request): View
    {
        $range = $this->resolve($request);
        $data  = $this->reports->categoryReport($request->user()->id, $range['from'], $range['to']);

        return view('reports.category', [
            'range'    => $range,
            'data'     => $data,
            'currency' => $this->currency($request),
        ]);
    }

    public function budget(Request $request): View
    {
        $range = $this->resolve($request);
        $data  = $this->reports->budgetReport(
            $request->user()->id,
            $range['from'],
            $range['to'],
            $this->budgets,
        );

        return view('reports.budget', [
            'range'    => $range,
            'data'     => $data,
            'currency' => $this->currency($request),
        ]);
    }

    public function savings(Request $request): View
    {
        $range = $this->resolve($request);
        $data  = $this->reports->savingsReport($request->user()->id, $range['from'], $range['to']);

        return view('reports.savings', [
            'range'    => $range,
            'data'     => $data,
            'currency' => $this->currency($request),
        ]);
    }

    public function summary(Request $request): View
    {
        $range = $this->resolve($request);
        $data  = $this->reports->financialSummary($request->user()->id, $range['from'], $range['to']);

        return view('reports.summary', [
            'range'    => $range,
            'data'     => $data,
            'currency' => $this->currency($request),
        ]);
    }

    private function resolve(Request $request, string $default = 'this_month'): array
    {
        return $this->reports->resolveRange(
            (string) $request->input('range', $default),
            $request->input('from'),
            $request->input('to'),
        );
    }

    private function currency(Request $request): string
    {
        return $request->user()->profile->currency ?? 'PKR';
    }

        public function exportIncome(Request $request, string $format)
    {
        $range = $this->resolve($request);
        $user  = $request->user();

        $filename = 'income-report-'
            . $range['from']->format('Y-m-d')
            . '-to-'
            . $range['to']->format('Y-m-d');

        $export = new IncomeReportExport(
            $user->id,
            $range['from']->toDateString(),
            $range['to']->toDateString(),
            $this->currency($request),
        );

        return match ($format) {
            'csv'   => Excel::download($export, $filename . '.csv',  \Maatwebsite\Excel\Excel::CSV),
            'xlsx'  => Excel::download($export, $filename . '.xlsx', \Maatwebsite\Excel\Excel::XLSX),
            'pdf'   => $this->pdfIncome($request, $range, $filename),
            default => abort(404),
        };
    }

    public function exportExpense(Request $request, string $format)
    {
        $range = $this->resolve($request);
        $user  = $request->user();

        $filename = 'expense-report-'
            . $range['from']->format('Y-m-d')
            . '-to-'
            . $range['to']->format('Y-m-d');

        $export = new ExpenseReportExport(
            $user->id,
            $range['from']->toDateString(),
            $range['to']->toDateString(),
            $this->currency($request),
        );

        return match ($format) {
            'csv'   => Excel::download($export, $filename . '.csv',  \Maatwebsite\Excel\Excel::CSV),
            'xlsx'  => Excel::download($export, $filename . '.xlsx', \Maatwebsite\Excel\Excel::XLSX),
            'pdf'   => $this->pdfExpense($request, $range, $filename),
            default => abort(404),
        };
    }

    private function pdfIncome(Request $request, array $range, string $filename)
    {
        $data = $this->reports->incomeReport(
            $request->user()->id,
            $range['from'],
            $range['to'],
        );

        $pdf = Pdf::loadView('reports.pdf.income', [
            'range'    => $range,
            'data'     => $data,
            'currency' => $this->currency($request),
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename . '.pdf');
    }

    private function pdfExpense(Request $request, array $range, string $filename)
    {
        $data = $this->reports->expenseReport(
            $request->user()->id,
            $range['from'],
            $range['to'],
        );

        $pdf = Pdf::loadView('reports.pdf.expense', [
            'range'    => $range,
            'data'     => $data,
            'currency' => $this->currency($request),
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename . '.pdf');
    }
}