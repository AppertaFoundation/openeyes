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
        'advanced_search' => array(
            'Genetics' => array(
                'Advanced Patient Search' => 'geneticPatients',
            ),
        ),
        'admin_structure' => array(
            'Studies' => array(
                'Genetics' => '/Genetics/studyAdmin/list',
            ),
        ),
        'admin_menu' => array(
            'Base Change Type' => '/Genetics/baseChangeAdmin/list',
            'Amino Acid Change Type' => '/Genetics/aminoAcidChangeAdmin/list',
            'DNA Sample Change' => '/OphInDnasample/DnaSampleAdmin/list',
        ),
    ),
    'components' => array(
    'event' => array(
        'observers' => array(
            'patient_add_diagnosis' => array(
                array(
                    'class' => 'DiagnosisObserver',
                    'method' => 'patientAddDiagnosis',
                ),
            ),
            'patient_remove_diagnosis' => array(
                array(
                    'class' => 'DiagnosisObserver',
                    'method' => 'patientRemoveDiagnosis',
                ),
            ),
        ),
    ),
),
);
