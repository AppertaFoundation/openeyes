<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 13/06/2017
 * Time: 10:42 AM
 */

return array(
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
                'core' => array(
                    'PatientAge',
                    'PatientDiagnosis',
                    'PatientMedication',
                    'PatientAllergy',
                    'FamilyHistory',
                    'PatientName',
                    'PatientNumber',
                    'PreviousProcedures',
                    'PatientVision',
                    'PatientIdentifier'
                ),
            ),
            'fixedParameters' => array(
                'core' => array(
                    'PatientDeceased',
                ),
            ),
            'providers' => array(
                'mysql' => 'DBProvider',
            ),
        ),
    ),
);