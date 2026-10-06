<?php

namespace App\Notifications;

use App\Models\FinancialGoal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GoalDeadlineNotification extends Notification
{
    use Queueable;

    public function __construct(
        public FinancialGoal $goal,
        public int $daysLeft,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'goal_deadline',
            'icon'       => 'bi-alarm-fill',
            'icon_class' => 'text-info',
            'title'      => 'Goal deadline approaching',
            'message'    => sprintf(
                '"%s" is due in %d day(s). You have saved %.2f of %.2f.',
                $this->goal->name,
                $this->daysLeft,
                (float) $this->goal->current_amount,
                (float) $this->goal->target_amount,
            ),
            'url'        => route('goals.show', $this->goal),
            'goal_id'    => $this->goal->id,
        ];
    }
}