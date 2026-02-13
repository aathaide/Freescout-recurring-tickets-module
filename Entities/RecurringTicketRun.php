<?php

namespace Modules\RecurringTickets\Entities;

use Illuminate\Database\Eloquent\Model;

class RecurringTicketRun extends Model
{
    protected $table = 'recurring_ticket_runs';

    protected $fillable = [
        'template_id','scheduled_for','conversation_id','result','message'
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
    ];
}
