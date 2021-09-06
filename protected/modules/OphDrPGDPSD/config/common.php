<?php

return array(
    'import' => [
        'application.modules.OphDrPGDPSD.components.*',
    ],
    'components' => [
        'urlManager' => [
            'rules' => [
                'OphDrPGDPSD/admin/<controller:\w+>/<action:\w+>' => '/OphDrPGDPSD/admin/<controller>/<action>',
                'OphDrPGDPSD/admin/<controller:\w+>/<action:\w+>/<id:\d+>' => '/OphDrPGDPSD/admin/<controller>/<action>',
            ]
        ],
        'event' => [
            'observers' => array(
                'step_created' => [
                    'create_psd' => [
                        'class' => 'PSDObserver',
                        'method' => 'createPSD',
                    ]
                ],
                'step_deleted' => [
                    'delete_psd' => [
                        'class' => 'PSDObserver',
                        'method' => 'removePSD'
                    ]
                ],
                'step_started' => [
                    'unlock_psd' => [
                        'class' => 'PSDObserver',
                        'method' => 'unlockPSD',
                    ]
                ],
                'step_completed' => [
                    'complete_drug_admin' => [
                        'class' => 'PSDObserver',
                        'method' => 'confirmAdministration',
                    ]
                ],
            ),
        ]
    ],
    'params' => array(
        'admin_structure' => [
            'PGD/PSD' => [
                'PGD/PSD Settings' => array(
                    'module' => 'OphDrPGDPSD',
                    'uri' => '/OphDrPGDPSD/admin/PGDPSDSettings/list',
                ),
            ]
        ],
        'reports' => array(
            'PSD' => '/OphDrPGDPSD/report/psdReport'
        ),
    ),
);
