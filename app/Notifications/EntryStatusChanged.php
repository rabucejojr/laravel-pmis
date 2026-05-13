<?php

namespace App\Notifications;

use App\Models\FinancialTarget;
use App\Models\PhysicalAccomplishment;
use App\Models\SetupAccomplishment;
use App\Models\VerificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EntryStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly FinancialTarget|PhysicalAccomplishment|SetupAccomplishment $entry,
        public readonly VerificationLog $log,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toDatabase(object $notifiable): array
    {
        if ($this->entry instanceof SetupAccomplishment) {
            return [
                'action'     => $this->log->action,
                'entry_type' => 'setup',
                'entry_id'   => $this->entry->id,
                'label'      => "SETUP KPI {$this->entry->year}",
                'project_id' => $this->entry->project_id,
                'project'    => $this->entry->project->title,
                'period'     => (string) $this->entry->year,
                'verifier'   => $this->log->verifier->name,
                'notes'      => $this->log->notes,
            ];
        }

        $entryType = $this->entry instanceof FinancialTarget ? 'financial' : 'physical';

        $label = $this->entry instanceof FinancialTarget
            ? $this->entry->line_item
            : $this->entry->indicator_name;

        return [
            'action'     => $this->log->action,
            'entry_type' => $entryType,
            'entry_id'   => $this->entry->id,
            'label'      => $label,
            'project_id' => $this->entry->project_id,
            'project'    => $this->entry->project->title,
            'period'     => "Q{$this->entry->quarter} {$this->entry->year} / Mo.{$this->entry->month}",
            'verifier'   => $this->log->verifier->name,
            'notes'      => $this->log->notes,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->entry instanceof SetupAccomplishment) {
            $badge = match($this->log->action) {
                'verified' => '✅ Verified',
                'flagged'  => '⚠️ Flagged',
                'rejected' => '❌ Rejected',
                default    => ucfirst($this->log->action),
            };

            $mail = (new MailMessage)
                ->subject("PMIS — SETUP KPI {$badge}")
                ->greeting("Hello {$notifiable->name},")
                ->line("Your SETUP KPI entry has been reviewed.")
                ->line("**Project:** {$this->entry->project->title}")
                ->line("**Year:** {$this->entry->year}")
                ->line("**Status:** {$badge}")
                ->line("**Reviewed by:** {$this->log->verifier->name}");

            if ($this->log->notes) {
                $mail->line("**Notes:** {$this->log->notes}");
            }

            return $mail->line('Please log in to the PMIS to view the full entry details.')
                        ->salutation('DOST-SDN PMIS');
        }

        $type   = $this->entry instanceof FinancialTarget ? 'Financial Target' : 'Physical Accomplishment';
        $action = ucfirst($this->log->action);
        $badge  = match($this->log->action) {
            'verified' => '✅ Verified',
            'flagged'  => '⚠️ Flagged',
            'rejected' => '❌ Rejected',
            default    => $action,
        };

        $label = $this->entry instanceof FinancialTarget
            ? $this->entry->line_item
            : $this->entry->indicator_name;

        $project = $this->entry->project;

        $mail = (new MailMessage)
            ->subject("PMIS — {$type} {$badge}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your {$type} entry has been reviewed.")
            ->line("**Project:** {$project->title}")
            ->line("**Entry:** {$label}")
            ->line("**Period:** Q{$this->entry->quarter} {$this->entry->year} (Month {$this->entry->month})")
            ->line("**Status:** {$badge}")
            ->line("**Reviewed by:** {$this->log->verifier->name}");

        if ($this->log->notes) {
            $mail->line("**Notes:** {$this->log->notes}");
        }

        return $mail->line('Please log in to the PMIS to view the full entry details.')
                    ->salutation('DOST-SDN PMIS');
    }
}
