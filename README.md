# Recurring Tickets (FreeScout Module)

This module adds **recurring ticket templates** that generate new FreeScout conversations on a schedule.

## What it does
- Lets agents create *templates* (subject/body/mailbox + a recurrence rule)
- A scheduled command finds templates that are due and generates a new ticket
- Updates `next_run_at` after each run

## Scheduler
The module registers a scheduled command using FreeScout's scheduling hook (see FreeScout Modules Development Guide):
- `freescout:recurringtickets-process`

## RRULE support
The included command supports a minimal subset of iCalendar RRULE:
- `FREQ=DAILY|WEEKLY|MONTHLY`
- `INTERVAL=N`

Example quarterly schedule:
- `FREQ=MONTHLY;INTERVAL=3`

## Idempotency
This version includes a run log table `recurring_ticket_runs` with a unique key on (template_id, scheduled_for) to prevent duplicate ticket creation.

## Notes
FreeScout internal models may differ between versions. The ticket-creation logic is conservative and may require adjustment for your FreeScout version.

## License
AGPL-3.0 (match FreeScout module ecosystem)
