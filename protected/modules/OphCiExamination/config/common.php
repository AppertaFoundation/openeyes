<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

return array(
    'components' => array(
        'event' => array(
            'observers' => array(
                'after_medications_save' => array(
                    'update_patient_risks' => array(
                        'class' => 'OEModule\OphCiExamination\components\HistoryRisksManager',
                        'method' => 'addPatientMedicationRisks'
                    )
                )
            )
        )
    ),
    'params' => array(
        'admin_menu' => array(
            'Inject. Mgmt - No Treatment Reasons' => '/OphCiExamination/admin/viewAllOphCiExamination_InjectionManagementComplex_NoTreatmentReason',
            'Inject. Mgmt - Diagnosis Questions' => '/OphCiExamination/admin/viewOphCiExamination_InjectionManagementComplex_Question',
            'Edit IOP Instruments' => '/OphCiExamination/admin/EditIOPInstruments',
            'Workflows' => '/OphCiExamination/admin/viewWorkflows',
            'Workflow rules' => '/OphCiExamination/admin/viewWorkflowRules',
            'Element Attributes' => array('OphCiExamination' => '/oeadmin/ExaminationElementAttributes/list'),
            'Overall Periods' => '/OphCiExamination/admin/manageOverallPeriods',
            'Visit Intervals' => '/OphCiExamination/admin/manageVisitIntervals',
            'Glaucoma Statuses' => '/OphCiExamination/admin/manageGlaucomaStatuses',
            'Drop-related Problems' => '/OphCiExamination/admin/manageDropRelProbs',
            'Drops Options' => '/OphCiExamination/admin/manageDrops',
            'Surgery Management Options' => '/OphCiExamination/admin/manageManagementSurgery',
            'Target IOP Values' => '/OphCiExamination/admin/manageTargetIOPs',
            'Comorbidities' => '/OphCiExamination/admin/manageComorbidities',
            'Clinic Outcome Statuses' => '/OphCiExamination/admin/manageClinicOutcomesStatus',
            'Cataract surgery reasons' => '/OphCiExamination/admin/primaryReasonForSurgery',
            'Common Post-Op Complications' => '/OphCiExamination/admin/postOpComplications',
            'Invoice Statuses' => '/OphCiExamination/admin/InvoiceStatusList',
            'Allergies' => '/OphCiExamination/admin/Allergies',
            'Risks' => '/OphCiExamination/admin/Risks',
            'Social History' => '/OphCiExamination/admin/SocialHistory',
            'Family History' => '/OphCiExamination/admin/FamilyHistory',
            'Medication Stop Reasons' => '/OphCiExamination/admin/HistoryMedicationsStopReason'
        ),
        'ophciexamination_drgrading_type_required' => false,
        'ophciexamination_visualacuity_correspondence_unit' => 'Snellen Metre',
        'menu_bar_items' => array(
            'ofm' => array(
                'title' => 'OF Manager',
                'position' => 9,
                'uri' => '/OphCiExamination/OptomFeedback/list',
                'restricted' => array(array('Optom co-ordinator', 'user_id')),
            )
        )
    )
);
