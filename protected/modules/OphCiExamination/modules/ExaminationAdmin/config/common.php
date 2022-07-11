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
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$config = [
    'params' => [
        'admin_menu' => [
            'OphCiExamination' => [
                'Element Attributes' => ['uri' => '/oeadmin/ExaminationElementAttributes/list'],
                'Workflows' => '/OphCiExamination/admin/viewWorkflows',
                'Workflow rules' => '/OphCiExamination/admin/viewWorkflowRules',
                'Allergies' => ['uri' => '/OphCiExamination/admin/Allergies/index', 'restricted' => array('admin')],
                'Required Allergy Assignment' => '/OphCiExamination/admin/AllergyAssignment/index',
                'Pupillary Abnormalities' => '/OphCiExamination/admin/PupillaryAbnormalities/index',
                'Required Pupillary Abnormalities' => '/OphCiExamination/admin/PupillaryAbnormalityAssignment/index',
                'Risks' => '/OphCiExamination/risksAdmin/list',
                'Required Risk Assignment' => '/OphCiExamination/admin/RisksAssignment/index',
                'Required Systemic Diagnoses Assignment' => '/OphCiExamination/admin/systemicDiagAssignment/index',
                'Required Ophthalmic Surgical History Assignment' => '/OphCiExamination/admin/SurgicalHistoryAssignment/index',
                'Required Systemic Surgical History Assignment' => '/OphCiExamination/admin/SystemicSurgicalHistoryAssignment/index',
                'Ophthalmic Surgical History' => ['uri' => '/admin/editpreviousoperation', 'restricted' => array('OprnInstitutionAdmin')],
                'Systemic Surgical History' => ['uri' => '/admin/editPreviousSystemicOperation', 'restricted' => array('OprnInstitutionAdmin')],
                'Social History' => ['uri' => '/OphCiExamination/admin/SocialHistory', 'restricted' => array('admin')],
                'Family History' => ['uri' => '/OphCiExamination/admin/FamilyHistory', 'restricted' => array('admin')],
                'IOP Instruments' => '/OphCiExamination/admin/ViewIOPInstruments',
                'Drop-related Problems' => ['uri' => '/OphCiExamination/admin/manageDropRelProbs', 'restricted' => array('admin')],
                'Drops Options' => ['uri' => '/OphCiExamination/admin/manageDrops', 'restricted' => array('admin')],
                'Surgery Management Options' => ['uri' => '/OphCiExamination/admin/manageManagementSurgery', 'restricted' => array('admin')],
                'Follow-up Statuses' => '/OphCiExamination/admin/manageClinicOutcomesStatus',
                'Follow-up Roles' =>  '/OphCiExamination/admin/ClinicOutcomeRoles/index',
                'Follow-up Risk Status' =>  '/OphCiExamination/admin/ClinicOutcomeRiskStatus/edit',
                'Cataract surgery reasons' => ['uri' => '/OphCiExamination/admin/primaryReasonForSurgery', 'restricted' => array('admin')],
                'Common Post-Op Complications' => '/OphCiExamination/admin/postOpComplications',
                'Medication Stop Reasons' => ['uri' => '/OphCiExamination/admin/MedicationStopReason/index', 'restricted' => array('admin')],
                'Overall Periods' => ['uri' => '/OphCiExamination/admin/manageOverallPeriods', 'restricted' => array('admin')],
                'Visit Intervals' => '/OphCiExamination/admin/manageVisitIntervals',
                'Glaucoma Statuses' => ['uri' => '/OphCiExamination/admin/manageGlaucomaStatuses', 'restricted' => array('admin')],
                'Target IOP Values' => ['uri' => '/OphCiExamination/admin/manageTargetIOPs', 'restricted' => array('admin')],
                'Inject. Mgmt - No Treatment Reasons' => ['uri' => '/OphCiExamination/admin/viewAllOphCiExamination_InjectionManagementComplex_NoTreatmentReason', 'restricted' => array('admin')],
                'Inject. Mgmt - Diagnosis Questions' => ['uri' => '/OphCiExamination/admin/viewOphCiExamination_InjectionManagementComplex_Question', 'restricted' => array('admin')],
                'Optom Invoice Statuses' => ['uri' => '/OphCiExamination/admin/InvoiceStatusList', 'restricted' => array('admin')],
                'Manage Drops' => '/OphCiExamination/admin/Drug/dilationDrugs',
                'Correction Types' => ['uri' => '/OphCiExamination/admin/CorrectionTypes', 'restricted' => array('admin')],
                'Stereo Acuity - Methods' => ['uri' => '/OphCiExamination/admin/StereoAcuityMethods', 'restricted' => array('admin')],
                'Sensory Function - Test Types' => ['uri' => '/OphCiExamination/admin/SensoryFunctionEntryTypes', 'restricted' => array('admin')],
                'Sensory Function - Distances' => ['uri' => '/OphCiExamination/admin/SensoryFunctionDistances', 'restricted' => array('admin')],
                'Sensory Function - Results' => ['uri' => '/OphCiExamination/admin/SensoryFunctionResults', 'restricted' => array('admin')],
                'Colour Vision - Methods' => ['uri' => '/OphCiExamination/admin/ColourVisionMethods', 'restricted' => array('admin')],
                'Colour Vision - Values' => ['uri' => '/OphCiExamination/admin/ColourVisionValues', 'restricted' => array('admin')],
                'Visual Acuity - Fixations' => ['uri' => '/OphCiExamination/admin/VisualAcuityFixations', 'restricted' => array('admin')],
                'Visual Acuity - Occluders' => ['uri' => '/OphCiExamination/admin/VisualAcuityOccluders', 'restricted' => array('admin')],
                'Visual Acuity - Sources' => ['uri' => '/OphCiExamination/admin/VisualAcuitySources', 'restricted' => array('admin')],
                'Cover And Prism Cover - Distance' => ['uri' => '/OphCiExamination/admin/CoverAndPrismCoverDistance', 'restricted' => array('admin')],
                'Cover And Prism Cover - Horizontal Prism' => ['uri' => '/OphCiExamination/admin/CoverAndPrismCoverHorizontalPrism', 'restricted' => array('admin')],
                'Cover And Prism Cover - Vertical Prism' => ['uri' => '/OphCiExamination/admin/CoverAndPrismCoverVerticalPrism', 'restricted' => array('admin')],
                'Dioptre Prism - Prism Base' => ['uri' => '/OphCiExamination/admin/DioptrePrismPrismBase', 'restricted' => array('admin')],
                'Dioptre Prism - Prism Dioptre' => ['uri' => '/OphCiExamination/admin/DioptrePrismPrismDioptre', 'restricted' => array('admin')],
                'Contrast Sensitivity - Type' => ['uri' => '/OphCiExamination/admin/ContrastSensitivityType', 'restricted' => array('admin')],
                'Synoptophore - Direction' => ['uri' => '/OphCiExamination/admin/SynoptophoreDirection', 'restricted' => array('admin')],
                'Synoptophore - Deviation' => ['uri' => '/OphCiExamination/admin/SynoptophoreDeviation', 'restricted' => array('admin')],
                'Refraction - Type' => ['uri' => '/OphCiExamination/admin/RefractionType', 'restricted' => array('admin')],
                'Nine Positions - Ocular Movement' => ['uri' => '/OphCiExamination/admin/NinePositionsMovement', 'restricted' => array('admin')],
                'Nine Positions - Horizontal E Deviation' => ['uri' => '/OphCiExamination/admin/NinePositionsHorizontalEDeviation', 'restricted' => array('admin')],
                'Nine Positions - Horizontal X Deviation' => ['uri' => '/OphCiExamination/admin/NinePositionsHorizontalXDeviation', 'restricted' => array('admin')],
                'Nine Positions - Vertical Deviation' => ['uri' => '/OphCiExamination/admin/NinePositionsVerticalDeviation', 'restricted' => array('admin')],
                'Strab Mgmt - Treatments' => ['uri' => '/OphCiExamination/admin/StrabismusManagementTreatments', 'restricted' => array('admin')],
                'Strab Mgmt - Treatment Options' => ['uri' => '/OphCiExamination/admin/StrabismusManagementTreatmentOptions', 'restricted' => array('admin')],
                'Strab Mgmt - Reasons' => ['uri' => '/OphCiExamination/admin/StrabismusManagementReasons', 'restricted' => array('admin')]
            ]
        ],
        'menu_bar_items' => [
            'ofm' => [
                'title' => 'Optom Invoice Manager',
                'position' => 9,
                'uri' => '/OphCiExamination/OptomFeedback/list',
                'restricted' => [ ['Optom co-ordinator', 'user_id'] ],
            ]
        ],
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
