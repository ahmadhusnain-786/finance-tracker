<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminStatsService;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(private AdminStatsService $stats)
    {
    }

    public function index(): View
    {
        return view('admin.dashboard', [
            'stats'          => $this->stats->systemStats(),
            'recentUsers'    => $this->stats->recentUsers(8),
            'recentLogins'   => $this->stats->recentLogins(8),
            'topUsers'       => $this->stats->topActiveUsersByRecordCount(5),
        ]);
    }
}