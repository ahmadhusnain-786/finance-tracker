<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class ScanNotifications extends Command
{
    protected $signature = 'notifications:scan';

    protected $description = 'Scan budgets and goals for warning/exceeded/deadline conditions and create notifications.';

    public function handle(NotificationService $service): int
    {
        $this->info('Scanning...');

        $result = $service->scanAll();

        $this->info(sprintf(
            'Created %d warning(s), %d exceeded alert(s), %d goal deadline(s).',
            $result['warnings'],
            $result['exceeded'],
            $result['deadlines'],
        ));

        return self::SUCCESS;
    }
}