<?php

use OEModule\OESysEvent\events\EventTypeEventCreated;
use OEModule\OESysEvent\listeners\LogEventTypeEventCreation;

return [
    'components' => [
        'event' => [
            'class' => \OEModule\OESysEvent\components\Manager::class,
            'observers' => [
                [
                    'event' => EventTypeEventCreated::class,
                    'listener' => LogEventTypeEventCreation::class
                ]
            ]
        ]
    ]
];
