<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Liberu\CRM\Core\Events\ContactUpdated;

class NotifyTeamMembers implements ShouldQueue
{
    public function handle(ContactUpdated $event): void
    {
        // Notify team members about the contact update
    }
}
