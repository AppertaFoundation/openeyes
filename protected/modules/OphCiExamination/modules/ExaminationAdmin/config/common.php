<?php
/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$config = [
    'params' => [
        'admin_menu' => array(
            'Element Attributes' => array('OphCiExamination' => '/oeadmin/ExaminationElementAttributes/list'),
            'Workflows' => '/OphCiExamination/admin/viewWorkflows',
            'Workflow rules' => '/OphCiExamination/admin/viewWorkflowRules',
            'Allergies' => '/OphCiExamination/admin/Allergies',

            'Risks' => '/OphCiExamination/admin/Risks',
            'Surgical History' => array('OphCiExamination' => '/admin/editpreviousoperation'),
            'Social History' => '/OphCiExamination/admin/SocialHistory',
            'Family History' => '/OphCiExamination/admin/FamilyHistory',
            'Comorbidities' => '/OphCiExamination/admin/manageComorbidities',
            'IOP Instruments' => '/OphCiExamination/admin/EditIOPInstruments',
            'Drop-related Problems' => '/OphCiExamination/admin/manageDropRelProbs',
            'Drops Options' => '/OphCiExamination/admin/manageDrops',
            'Surgery Management Options' => '/OphCiExamination/admin/manageManagementSurgery',
            'Follow-up Statuses' => '/OphCiExamination/admin/manageClinicOutcomesStatus',
            'Cataract surgery reasons' => '/OphCiExamination/admin/primaryReasonForSurgery',
            'Common Post-Op Complications' => '/OphCiExamination/admin/postOpComplications',
            'Medication Stop Reasons' => '/OphCiExamination/admin/HistoryMedicationsStopReason',
            'Overall Periods' => '/OphCiExamination/admin/manageOverallPeriods',
            'Visit Intervals' => '/OphCiExamination/admin/manageVisitIntervals',
            'Glaucoma Statuses' => '/OphCiExamination/admin/manageGlaucomaStatuses',
            'Target IOP Values' => '/OphCiExamination/admin/manageTargetIOPs',
            'Inject. Mgmt - No Treatment Reasons' => '/OphCiExamination/admin/viewAllOphCiExamination_InjectionManagementComplex_NoTreatmentReason',
            'Inject. Mgmt - Diagnosis Questions' => '/OphCiExamination/admin/viewOphCiExamination_InjectionManagementComplex_Question',
            'Optom Invoice Statuses' => '/OphCiExamination/admin/InvoiceStatusList',

            'Required Risk Assignment' => '/OphCiExamination/oeadmin/RisksAssignment',
            'Required Systemic Diagnoses Assignment' => '/OphCiExamination/oeadmin/systemicDiagAssignment',
            'Required Surgical History Assignment' => '/OphCiExamination/oeadmin/SurgicalHistoryAssignment',
            'Required Allergy Assignment' => '/OphCiExamination/oeadmin/AllergyAssignment',
        ),
        'menu_bar_items' => array(
            'ofm' => array(
                'title' => 'Optom Invoice Manager',
                'position' => 9,
                'uri' => '/OphCiExamination/OptomFeedback/list',
                'restricted' => array(array('Optom co-ordinator', 'user_id')),
            )
        ),
    ],

    'components' => [
        'urlManager' => [
            'rules' => [
                'OphCiExamination/admin/<controller:\w+>/<action:\w+>' => '/OphCiExamination/ExaminationAdmin/<controller>/<action>',
                'OphCiExamination/admin/<controller:\w+>/<action:\w+>/<id:\d+>' => '/OphCiExamination/ExaminationAdmin/<controller>/<action>',
            ]
        ]
    ]
];

return $config;