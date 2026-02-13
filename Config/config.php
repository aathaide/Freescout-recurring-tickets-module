<?php

return [
    'name' => 'RecurringTickets',
    'options' => [
        'enabled' => ['default' => true],
        'process_cron' => ['default' => '* * * * *'],
        'system_requester_email' => ['default' => 'noreply@example.com'],
        // Optional API settings (if you later choose to create tickets via API):
        'api_url' => ['default' => ''],
        'api_key' => ['default' => ''],
    ],
];
