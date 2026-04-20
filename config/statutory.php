<?php

return [
    'items' => [
        [
            'key' => 'paye',
            'label' => 'PAYE',
            'day' => 10,
            'tone' => 'accent',
        ],
        [
            'key' => 'turnover_tax',
            'label' => 'Turnover Tax',
            'day' => 14,
            'tone' => 'default',
        ],
        [
            'key' => 'napsa',
            'label' => 'NAPSA',
            'day' => 10,
            'tone' => 'default',
        ],
    ],

    'email' => [
        'enabled' => (bool) env('STATUTORY_EMAIL_REMINDERS_ENABLED', true),
        'days_before' => [5, 0],
        'recipient_emails' => array_values(array_filter(array_map('trim', explode(',', (string) env('STATUTORY_REMINDER_EMAILS', ''))))),
    ],
];