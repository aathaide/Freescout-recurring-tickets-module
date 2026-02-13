<?php

namespace Modules\RecurringTickets\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RecurringTickets\Entities\RecurringTicketTemplate;

class RecurringTicketsController extends Controller
{
    public function index()
    {
        $templates = RecurringTicketTemplate::query()->orderBy('id', 'desc')->limit(100)->get();
        return view('recurringtickets::index', ['templates' => $templates]);
    }

    public function create()
    {
        return view('recurringtickets::create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'mailbox_id' => 'required|integer',
            'subject' => 'required|string|max:255',
            'body' => 'nullable|string',
            'rrule' => 'required|string|max:255',
            'starts_at' => 'required|date',
            'timezone' => 'nullable|string|max:64',
            'requester_email' => 'nullable|email|max:190',
            'assignee_id' => 'nullable|integer',
        ]);

        $tz = $data['timezone'] ?? config('app.timezone');

        $template = new RecurringTicketTemplate();
        $template->name = $data['name'];
        $template->mailbox_id = (int)$data['mailbox_id'];
        $template->subject = $data['subject'];
        $template->body = $data['body'] ?? '';
        $template->rrule = $data['rrule'];
        $template->starts_at = $data['starts_at'];
        $template->timezone = $tz;
        $template->active = 1;
        $template->next_run_at = $data['starts_at'];

        $template->payload_json = [
            'requester_email' => $data['requester_email'] ?? null,
            'assignee_id' => isset($data['assignee_id']) ? (int)$data['assignee_id'] : null,
        ];

        $template->save();

        return redirect()->route('recurringtickets.index')->with('success', 'Recurring ticket template created.');
    }
}
