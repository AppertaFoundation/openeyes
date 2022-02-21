<?php

return array(
    'components' => [
        'urlManager' => [
            'rules' => [
                'OphDrPGDPSD/admin/<controller:\w+>/<action:\w+>' => '/OphDrPGDPSD/admin/<controller>/<action>',
                'OphDrPGDPSD/admin/<controller:\w+>/<action:\w+>/<id:\d+>' => '/OphDrPGDPSD/admin/<controller>/<action>',
            ]
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
