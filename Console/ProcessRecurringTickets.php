<?php

namespace Modules\RecurringTickets\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Modules\RecurringTickets\Entities\RecurringTicketTemplate;
use Modules\RecurringTickets\Entities\RecurringTicketRun;

class ProcessRecurringTickets extends Command
{
    protected $signature = 'freescout:recurringtickets-process {--dry-run : Do not create tickets, only log what would happen}';
    protected $description = 'Generate tickets from due recurring ticket templates.';

    public function handle(): int
    {
        $now = Carbon::now('UTC');

        $due = RecurringTicketTemplate::query()
            ->where('active', true)
            ->whereNotNull('next_run_at')
            ->where('next_run_at', '<=', $now)
            ->orderBy('next_run_at', 'asc')
            ->limit(200)
            ->get();

        $this->info('Due templates: '.$due->count());

        foreach ($due as $tpl) {
            try {
                $this->processTemplate($tpl);
            } catch (\Throwable $e) {
                Log::error('RecurringTickets: template '.$tpl->id.' failed: '.$e->getMessage(), ['exception' => $e]);
                $this->error('Template '.$tpl->id.' failed: '.$e->getMessage());
            }
        }

        return 0;
    }

    protected function processTemplate(RecurringTicketTemplate $tpl): void
    {
        $dry = (bool)$this->option('dry-run');

        // End conditions
        if ($tpl->ends_at && Carbon::parse($tpl->ends_at, 'UTC')->lt(Carbon::now('UTC'))) {
            $tpl->active = false;
            $tpl->save();
            $this->warn('Template '.$tpl->id.' ended; deactivated.');
            return;
        }
        if ($tpl->max_runs && $tpl->run_count >= $tpl->max_runs) {
            $tpl->active = false;
            $tpl->save();
            $this->warn('Template '.$tpl->id.' reached max runs; deactivated.');
            return;
        }

        // Idempotency: insert a run record for this scheduled time.
        $scheduledFor = Carbon::parse($tpl->next_run_at, 'UTC');
        $run = null;
        try {
            $run = RecurringTicketRun::create([
                'template_id'   => $tpl->id,
                'scheduled_for' => $scheduledFor->toDateTimeString(),
                'result'        => $dry ? 'dry_run' : 'pending',
            ]);
        } catch (\Throwable $e) {
            $this->warn('Skipping duplicate run for template '.$tpl->id.' at '.$scheduledFor->toDateTimeString());
            return;
        }

        $created = false;
        $conversationId = null;
        $message = null;

        if ($dry) {
            $this->line('[dry-run] Would create ticket for template '.$tpl->id.' (mailbox '.$tpl->mailbox_id.')');
            $created = true;
        } else {
            $created = $this->createConversationIfPossible($tpl, $conversationId);
            $this->line(($created ? 'Created' : 'Failed').' ticket for template '.$tpl->id);
            if (!$created) {
                $message = 'Unable to create conversation using internal models';
            }
        }

        if ($run) {
            $run->conversation_id = $conversationId;
            $run->result = $dry ? 'dry_run' : ($created ? 'created' : 'failed');
            $run->message = $message;
            $run->save();
        }

        // Compute next run time and persist.
        $tpl->run_count = (int)$tpl->run_count + 1;
        $tpl->next_run_at = $this->computeNextRunUtc($tpl);
        $tpl->save();
    }

    protected function createConversationIfPossible(RecurringTicketTemplate $tpl, &$conversationId = null): bool
    {
        // NOTE: FreeScout internal model APIs can differ by version.
        // This implementation is conservative and only attempts minimal fields.
        if (!class_exists('\App\Conversation') || !class_exists('\App\Thread')) {
            Log::warning('RecurringTickets: FreeScout models not found; cannot create conversation.');
            return false;
        }

        $Conversation = '\App\Conversation';
        $Thread = '\App\Thread';

        $convo = new $Conversation();
        $convo->mailbox_id = $tpl->mailbox_id;
        $convo->subject = $tpl->subject;

        if (property_exists($convo, 'type')) {
            $convo->type = $convo->type ?? 'email';
        }

        $convo->save();
        $conversationId = $convo->id;

        $thread = new $Thread();
        if (property_exists($thread, 'conversation_id')) {
            $thread->conversation_id = $convo->id;
        }
        if (property_exists($thread, 'body')) {
            $thread->body = $tpl->body ?: '';
        }
        if (property_exists($thread, 'type')) {
            $thread->type = $thread->type ?? 'note';
        }
        $thread->save();

        return true;
    }

    protected function computeNextRunUtc(RecurringTicketTemplate $tpl): ?string
    {
        // Minimal RRULE subset: FREQ=DAILY|WEEKLY|MONTHLY ; INTERVAL=N.
        $rrule = strtoupper(trim($tpl->rrule ?? ''));
        $parts = [];
        foreach (explode(';', $rrule) as $kv) {
            if (strpos($kv, '=') === false) continue;
            [$k,$v] = explode('=', $kv, 2);
            $parts[$k] = $v;
        }
        $freq = $parts['FREQ'] ?? 'MONTHLY';
        $interval = (int)($parts['INTERVAL'] ?? 1);
        if ($interval < 1) $interval = 1;

        $current = $tpl->next_run_at ? Carbon::parse($tpl->next_run_at, 'UTC') : Carbon::now('UTC');

        if ($freq === 'DAILY') {
            $next = $current->copy()->addDays($interval);
        } elseif ($freq === 'WEEKLY') {
            $next = $current->copy()->addWeeks($interval);
        } else {
            $next = $current->copy()->addMonths($interval);
        }

        return $next->toDateTimeString();
    }
}
