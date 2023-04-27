<?php

return array(
    'import' => array(
        'application.modules.OETrial.models.*',
        'application.modules.OECaseSearch.models.*',
    ),
    'params' => array(
        'menu_bar_items' => array(
            'trials' => array(
                'title' => 'Trials',
                'uri' => 'OETrial',
                'restricted' => array('TaskCreateTrial', 'TaskViewTrial'),
            ),
        ),
        'CaseSearch' => array(
            'parameters' => array(
                'OETrial' => array(
                    'PreviousTrial',
                ),
            ),
        ),
    ),
);
