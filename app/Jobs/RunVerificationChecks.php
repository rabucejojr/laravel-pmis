<?php

namespace App\Jobs;

use App\Models\FinancialTarget;
use App\Models\PhysicalAccomplishment;
use App\Models\SetupAccomplishment;
use App\Services\VerificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RunVerificationChecks implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public FinancialTarget|PhysicalAccomplishment|SetupAccomplishment $entry
    ) {}

    public function handle(VerificationService $service): void
    {
        $flags = match(true) {
            $this->entry instanceof FinancialTarget        => $service->checkFinancialTarget($this->entry),
            $this->entry instanceof PhysicalAccomplishment => $service->checkPhysicalAccomplishment($this->entry),
            $this->entry instanceof SetupAccomplishment    => $service->checkSetupAccomplishment($this->entry),
        };

        if (empty($flags)) {
            // All automated checks passed — leave status as 'pending' for human review.
            return;
        }

        $this->entry->update([
            'verified_status'    => 'flagged',
            'verification_notes' => implode("\n", $flags),
        ]);
    }
}
