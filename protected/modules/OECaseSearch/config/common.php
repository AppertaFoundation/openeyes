<?php

/**
 * Created by PhpStorm.
 * User: andre
 * Date: 13/06/2017
 * Time: 10:42 AM
 */

return array(
    'components' => array(
        'searchProvider' => array(
            'class' => 'DBProvider',
        ),
    ),
    'params' => array(
        'menu_bar_items' => array(
            'casesearch' => array(
                'title' => 'Advanced Search',
                'uri' => '/OECaseSearch/caseSearch/index',
                'restricted' => array('TaskCaseSearch'),
            ),
        ),
        'CaseSearch' => array(
            'parameters' => array(
                'OECaseSearch' => array(
                    'PatientAge',
                    'PatientDiagnosis',
                    'PatientMedication',
                    'PatientAllergy',
                    'FamilyHistory',
                    'PatientName',
                    'PatientIdentifier',
                    'PreviousProcedures',
                    'PatientVision',
                    'PatientDeceased',
                    'Institution',
                ),
            ),
            'variables' => array(
                'OECaseSearch' => array(
                    'age' => 'AgeVariable',
                    'iop_first' => array(
                        'class' => 'IOPVariable',
                        'field_name' => 'iop_first',
                        'label' => 'IOP (first)',
                        'query_flags' => array('first'),
                    ),
                    'iop_last' => array(
                        'class' => 'IOPVariable',
                        'field_name' => 'iop_last',
                        'label' => 'IOP (last)',
                        'query_flags' => array('last'),
                    ),
                    'va' => 'VAVariable',
                    'cct' => 'CCTVariable',
                    'crt' => 'CRTVariable',
                    'refraction' => 'RefractionVariable'
                ),
            ),
            'providers' => array(
                'mysql' => 'DBProvider',
            ),
            /// Template strings function like shortcodes,
            /// allowing substitution of application state values into search parameters. These will take the form of
            /// selectable options in the frontend.
            /// Format: 'template_string' => 'Template display string'
            'enabled_template_strings' => array(
                'institution',
                'site',
                'firm',
                'user',
            ),
            'template_string_regex' => "/{[A-za-z_]+}/",
        ),
    ),
);
