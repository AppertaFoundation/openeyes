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
            'Element Attributes' => ['OphCiExamination' => '/oeadmin/ExaminationElementAttributes/list'],
            'Workflows' => '/OphCiExamination/admin/viewWorkflows',
            'Workflow rules' => '/OphCiExamination/admin/viewWorkflowRules',
            'Allergies' => '/OphCiExamination/admin/Allergies/index',
            'Required Allergy Assignment' => '/OphCiExamination/admin/AllergyAssignment/index',
            'Pupillary Abnormalities' => '/OphCiExamination/admin/PupillaryAbnormalities/index',
            'Required Pupillary Abnormalities' => '/OphCiExamination/admin/PupillaryAbnormalityAssignment/index',
            'Risks' => '/OphCiExamination/risksAdmin/list',
            'Required Risk Assignment' => '/OphCiExamination/admin/RisksAssignment/index',
            'Required Systemic Diagnoses Assignment' => '/OphCiExamination/admin/systemicDiagAssignment/index',
            'Required Ophthalmic Surgical History Assignment' => '/OphCiExamination/admin/SurgicalHistoryAssignment/index',
            'Required Systemic Surgical History Assignment' => '/OphCiExamination/admin/SystemicSurgicalHistoryAssignment/index',
            'Ophthalmic Surgical History' => ['OphCiExamination' => '/admin/editpreviousoperation'],
            'Systemic Surgical History' => ['OphCiExamination' => '/admin/editPreviousSystemicOperation'],
            'Social History' => '/OphCiExamination/admin/SocialHistory',
            'Family History' => '/OphCiExamination/admin/FamilyHistory',
            'Comorbidities' => '/OphCiExamination/admin/manageComorbidities',
            'IOP Instruments' => '/OphCiExamination/admin/EditIOPInstruments',
            'Drop-related Problems' => '/OphCiExamination/admin/manageDropRelProbs',
            'Drops Options' => '/OphCiExamination/admin/manageDrops',
            'Surgery Management Options' => '/OphCiExamination/admin/manageManagementSurgery',
            'Follow-up Statuses' => '/OphCiExamination/admin/manageClinicOutcomesStatus',
            'Follow-up Roles' =>  '/OphCiExamination/admin/ClinicOutcomeRoles/index',
            'Cataract surgery reasons' => '/OphCiExamination/admin/primaryReasonForSurgery',
            'Common Post-Op Complications' => '/OphCiExamination/admin/postOpComplications',
            'Medication Stop Reasons' => '/OphCiExamination/admin/MedicationStopReason/index',
            'Overall Periods' => '/OphCiExamination/admin/manageOverallPeriods',
            'Visit Intervals' => '/OphCiExamination/admin/manageVisitIntervals',
            'Glaucoma Statuses' => '/OphCiExamination/admin/manageGlaucomaStatuses',
            'Target IOP Values' => '/OphCiExamination/admin/manageTargetIOPs',
            'Inject. Mgmt - No Treatment Reasons' => '/OphCiExamination/admin/viewAllOphCiExamination_InjectionManagementComplex_NoTreatmentReason',
            'Inject. Mgmt - Diagnosis Questions' => '/OphCiExamination/admin/viewOphCiExamination_InjectionManagementComplex_Question',
            'Optom Invoice Statuses' => '/OphCiExamination/admin/InvoiceStatusList',
            'Manage Drops' => '/OphCiExamination/admin/Drug/dilationDrugs',
            'Correction Types' => '/OphCiExamination/admin/CorrectionTypes',
            'Stereo Acuity - Methods' => '/OphCiExamination/admin/StereoAcuityMethods',
            'Sensory Function - Test Types' => '/OphCiExamination/admin/SensoryFunctionEntryTypes',
            'Sensory Function - Distances' => '/OphCiExamination/admin/SensoryFunctionDistances',
            'Sensory Function - Results' => '/OphCiExamination/admin/SensoryFunctionResults',
            'Colour Vision - Methods' => '/OphCiExamination/admin/ColourVisionMethods',
            'Colour Vision - Values' => '/OphCiExamination/admin/ColourVisionValues',
            'Visual Acuity - Fixations' => '/OphCiExamination/admin/VisualAcuityFixations',
            'Visual Acuity - Occluders' => '/OphCiExamination/admin/VisualAcuityOccluders',
            'Visual Acuity - Sources' => '/OphCiExamination/admin/VisualAcuitySources',
            'Cover And Prism Cover - Correction' => '/OphCiExamination/admin/CoverAndPrismCoverCorrection',
            'Cover And Prism Cover - Distance' => '/OphCiExamination/admin/CoverAndPrismCoverDistance',
            'Cover And Prism Cover - Horizontal Prism' => '/OphCiExamination/admin/CoverAndPrismCoverHorizontalPrism',
            'Cover And Prism Cover - Vertical Prism' => '/OphCiExamination/admin/CoverAndPrismCoverVerticalPrism',
            'Dioptre Prism - Prism Base' => '/OphCiExamination/admin/DioptrePrismPrismBase',
            'Dioptre Prism - Prism Dioptre' => '/OphCiExamination/admin/DioptrePrismPrismDioptre',
            'Contrast Sensitivity - Type' => '/OphCiExamination/admin/ContrastSensitivityType',
            'Synoptophore - Direction' => '/OphCiExamination/admin/SynoptophoreDirection',
            'Synoptophore - Deviation' => '/OphCiExamination/admin/SynoptophoreDeviation',
            'Refraction - Type' => '/OphCiExamination/admin/RefractionType',
            'Nine Positions - Ocular Movement' => '/OphCiExamination/admin/NinePositionsMovement',
            'Nine Positions - Horizontal E Deviation' => '/OphCiExamination/admin/NinePositionsHorizontalEDeviation',
            'Nine Positions - Horizontal X Deviation' => '/OphCiExamination/admin/NinePositionsHorizontalXDeviation',
            'Nine Positions - Vertical Deviation' => '/OphCiExamination/admin/NinePositionsVerticalDeviation',
            'Strab Mgmt - Treatments' => '/OphCiExamination/admin/StrabismusManagementTreatments',
            'Strab Mgmt - Treatment Options' => '/OphCiExamination/admin/StrabismusManagementTreatmentOptions',
            'Strab Mgmt - Reasons' => '/OphCiExamination/admin/StrabismusManagementReasons'
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
