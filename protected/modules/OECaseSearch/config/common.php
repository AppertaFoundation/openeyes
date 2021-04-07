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
                'position' => 4,
                'restricted' => array('Advanced Search'),
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
        ),
    ),
);
