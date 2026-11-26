<?php

declare(strict_types=1);

namespace Liberu\CRM\Core;

use Illuminate\Support\ServiceProvider;
use Liberu\Analytics\Core\Models\ReportBuilder;
use Liberu\CRM\AutomationPack\Models\Workflow;
use Liberu\CRM\AutomationPack\Models\WorkflowAction;
use Liberu\CRM\AutomationPack\Models\WorkflowCondition;
use Liberu\CRM\AutomationPack\Models\WorkflowTrigger;
use Liberu\CRM\ChatAndBots\Models\Chatbot;
use Liberu\CRM\Core\Contracts\RecordRepository;
use Liberu\CRM\Core\Models\AuditLog;
use Liberu\CRM\Core\Models\Contact;
use Liberu\CRM\Core\Models\CustomField;
use Liberu\CRM\Core\Models\Tag;
use Liberu\CRM\Core\Services\CrmTeamAuditLogger;
use Liberu\CRM\Core\Services\EloquentRecordRepository;
use Liberu\CRM\EmailProductivity\Models\EmailTemplate;
use Liberu\CRM\Knowledge\Models\KnowledgeBaseArticle;
use Liberu\CRM\SalesPipelines\Models\Pipeline;
use Liberu\CRM\SalesPipelines\Models\Stage;
use Liberu\CRM\WhiteLabel\Models\Menu;
use Liberu\Foundation\Organizations\Contracts\TeamAuditLogger;
use Liberu\Foundation\Organizations\Models\Team;

final class CRMCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RecordRepository::class, EloquentRecordRepository::class);
        $this->app->bind(TeamAuditLogger::class, CrmTeamAuditLogger::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Team::resolveRelationUsing('contacts', fn (Team $team) => $team->hasMany(Contact::class));

        $config = $this->app['config'];

        if ($config->get('organizations-teams.audit_log_model') === null) {
            $config->set('organizations-teams.audit_log_model', AuditLog::class);
        }

        if ($config->get('organizations-teams.cloneable_models', []) === []) {
            $config->set('organizations-teams.cloneable_models', [
                Pipeline::class,
                Stage::class,
                Workflow::class,
                WorkflowAction::class,
                WorkflowCondition::class,
                WorkflowTrigger::class,
                EmailTemplate::class,
                CustomField::class,
                Tag::class,
                KnowledgeBaseArticle::class,
                Chatbot::class,
                ReportBuilder::class,
                Menu::class,
            ]);
        }

        if ($config->get('organizations-teams.clone_fk_edges', []) === []) {
            $config->set('organizations-teams.clone_fk_edges', [
                Stage::class => ['pipeline_id' => Pipeline::class],
                WorkflowAction::class => ['workflow_id' => Workflow::class],
                WorkflowCondition::class => ['workflow_action_id' => WorkflowAction::class],
                WorkflowTrigger::class => ['workflow_id' => Workflow::class],
                Menu::class => ['parent_id' => Menu::class],
            ]);
        }
    }
}
