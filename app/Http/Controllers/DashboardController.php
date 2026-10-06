<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboard)
    {
    }

    public function index(Request $request): View
    {
        $user   = $request->user();
        $preset = (string) $request->input('range', 'month');

        $range = $this->dashboard->resolveDateRange(
            $preset,
            $request->input('from'),
            $request->input('to')
        );

        $metrics = $this->dashboard->getMetrics($user->id, $range['from'], $range['to']);
        $recent  = $this->dashboard->recentTransactions($user->id, 8);

        // Chart data
        $incomeVsExpense = $this->dashboard->getIncomeVsExpenseSeries($user->id, $range['from'], $range['to']);
        $expenseByCat    = $this->dashboard->getExpenseByCategory($user->id, $range['from'], $range['to']);
        $savingsTrend    = $this->dashboard->getSavingsTrend($user->id, $range['from'], $range['to']);

        $profile = $user->profile;

        return view('dashboard.index', [
            'user'            => $user,
            'profile'         => $profile,
            'range'           => $range,
            'metrics'         => $metrics,
            'recent'          => $recent,
            'currency'        => $profile->currency ?? 'PKR',
            'incomeVsExpense' => $incomeVsExpense,
            'expenseByCat'    => $expenseByCat,
            'savingsTrend'    => $savingsTrend,
        ]);
    }
}