<?php

namespace App\Notifications;

use App\Models\Budget;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BudgetWarningNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Budget $budget,
        public float $spent,
        public float $limit,
        public float $percentage,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'budget_warning',
            'icon'       => 'bi-exclamation-triangle-fill',
            'icon_class' => 'text-warning',
            'title'      => 'Budget warning: ' . $this->budget->category?->name,
            'message'    => sprintf(
                'You have used %.1f%% of your %s budget (%.2f / %.2f).',
                $this->percentage,
                $this->budget->category?->name ?? 'category',
                $this->spent,
                $this->limit,
            ),
            'url'        => route('budgets.index'),
            'budget_id'  => $this->budget->id,
        ];
    }
}