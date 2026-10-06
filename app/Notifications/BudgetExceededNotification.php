<?php

namespace App\Notifications;

use App\Models\Budget;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BudgetExceededNotification extends Notification
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
            'type'       => 'budget_exceeded',
            'icon'       => 'bi-x-octagon-fill',
            'icon_class' => 'text-danger',
            'title'      => 'Budget exceeded: ' . $this->budget->category?->name,
            'message'    => sprintf(
                'You have exceeded your %s budget (%.2f spent of %.2f).',
                $this->budget->category?->name ?? 'category',
                $this->spent,
                $this->limit,
            ),
            'url'        => route('budgets.index'),
            'budget_id'  => $this->budget->id,
        ];
    }
}