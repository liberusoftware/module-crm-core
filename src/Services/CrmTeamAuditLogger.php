<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Services;

use Illuminate\Database\Eloquent\Model;
use Liberu\Foundation\Organizations\Contracts\TeamAuditLogger;

final readonly class CrmTeamAuditLogger implements TeamAuditLogger
{
    public function __construct(private AuditLogService $auditLogs) {}

    public function record(string $action, string $description, ?Model $auditable = null): void
    {
        $this->auditLogs->record($action, $description, $auditable);
    }
}
