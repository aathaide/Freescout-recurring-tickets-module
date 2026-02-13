<?php

namespace Modules\RecurringTickets\Entities;

use Illuminate\Database\Eloquent\Model;

class RecurringTicketTemplate extends Model
{
    protected $table = 'recurring_ticket_templates';

    protected $fillable = [
        'name','mailbox_id','subject','body','rrule','starts_at','timezone','next_run_at','ends_at','max_runs','run_count','active','payload_json'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'next_run_at' => 'datetime',
        'ends_at' => 'datetime',
        'payload_json' => 'array',
        'active' => 'boolean',
    ];
}
