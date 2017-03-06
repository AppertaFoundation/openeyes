<?php

return array(
    'import' => array(
        'application.modules.Genetics.models.*',
        'application.modules.Genetics.components.*',
    ),
    'params' => array(
        'menu_bar_items' => array(
            'pedigrees' => array(
                'title' => 'Genetics',
                'uri' => 'Genetics/default/index',
                'position' => 40,
                'restricted' => array('Genetics User'),
            ),
        ),
        'module_partials' => array(
            'patient_summary_column1' => array(
                'Genetics' => array(
                    '_patient_genetics',
                ),
            ),
        ),
        'admin_structure' => array(
        ),
        'admin_menu' => array(
        ),
        'additional_rulesets' => array(
            array(
                'namespace' => 'Genetics',
                'class' => 'Genetics_AuthRules'
            ),
        ),
    ),
);
