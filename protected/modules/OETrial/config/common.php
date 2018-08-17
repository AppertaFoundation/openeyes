<?php

return array(
    'params' => array(
        'menu_bar_items' => array(
            'trials' => array(
                'title' => 'Trials',
                'uri' => 'OETrial',
                'position' => 6,
                'restricted' => array('TaskCreateTrial', 'TaskViewTrial'),
            ),
        ),
        'module_partials' => array(
            'patient_summary_column1' => array(
                'OETrial' => array(
                    25 => '_patient_trials',
                ),
            ),
        ),
        'CaseSearch' => array(
            'parameters' => array(
                'OETrial' => array(
                    'PreviousTrial'
                )
            ),
        ),
    ),
);
