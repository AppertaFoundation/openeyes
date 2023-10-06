<?php

return [
    'components' => [
        'request' => [
            'noCsrfValidationRoutes' => [
                'CypressHelper/',
            ],
        ],
        'urlManager' => [
            'rules' => [
                ['CypressHelper/Default/createEvent', 'pattern' => 'CypressHelper/<controller:\w+>/createEvent/<moduleName:\w+>'],
                ['CypressHelper/Default/getEventCreationUrl', 'pattern' => 'CypressHelper/<controller:\w+>/getEventCreationUrl/<patientId:\d+>/<moduleName:\w+>/<firmId:\d+>'],
            ]
        ]
    ]
];
