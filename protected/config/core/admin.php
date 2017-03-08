<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

return array(
    'params' => array(
        'admin_structure' => array(
            'System' => array(
                'Settings' => '/admin/settings',
                'Logo' => array('parameter' => 'letter_logo_upload', 'uri' => '/admin/logo'),
                'DICOM Log Viewer' => '/DicomLogViewer/list',
            ),
            'Core' => array(
                'Users' => '/admin/users',
                'Firms' => '/admin/firms',
                'Contacts' => '/admin/contacts',
                'Contact labels' => '/admin/contactlabels',
                'Data sources' => '/admin/datasources',
                'Allergies' => '/admin/allergies',
                'Institutions' => '/admin/institutions',
                'Sites' => '/admin/sites',
                'Commissioning bodies' => '/admin/commissioning_bodies',
                'Commissioning body types' => '/admin/commissioning_body_types',
                'Commissioning body services' => '/admin/commissioning_body_services',
                'Commissioning body service types' => '/admin/commissioning_body_service_types',
                'Event deletion requests' => '/admin/eventDeletionRequests',
                'Custom episode summaries' => '/admin/episodeSummaries',
                'Medication Stop Reason' => '/admin/editmedicationstopreason',
                'Previous Ophthalmic Surgery' => '/admin/editpreviousoperation',
                'Social History' => '/admin/socialhistory',
                'Findings' => '/admin/managefindings',
                'Anaesthetic Agent' => '/admin/viewAnaestheticAgent',
                'Anaesthetic Agent Mapping' => '/oeadmin/AnaestheticAgentMapping/list',
                'Anaesthetic Agent Defaults' => '/oeadmin/AnaestheticAgentDefaults/list',
                'Risks' => '/oeadmin/risk/list',
                'Unique Codes' => '/oeadmin/uniqueCodes/list',
                'Examination Event Logs' => '/oeadmin/eventLog/list',
            ),
            'Worklists' => array(
                'Automatic Worklists Definitions' => '/worklistAdmin/definitions',
            ),
            'Procedure Management' => array(
                'Procedures' => '/oeadmin/procedure/list',
                'Benefits' => '/oeadmin/benefit/list',
                'Complications' => '/oeadmin/complication/list',
                'OPCS Codes' => '/oeadmin/opcsCode/list',
            ),
            'Drugs' => array(
                'Common Drugs List' => array('module' => 'OphDrPrescription', 'uri' => '/OphDrPrescription/commonDrugAdmin/list'),
                'Drug Sets' => array('module' => 'OphDrPrescription', 'uri' => '/OphDrPrescription/DrugSetAdmin/list'),
                'Common Medications List ' => '/oeadmin/commonMedications/list',
                'Medication List' => '/oeadmin/medication/list',
                'Formulary Drugs' => '/oeadmin/formularyDrugs/list',
                'Per Op Drugs' => array('module' => 'OphTrOperationnote', 'uri' => '/OphTrOperationnote/admin/viewPostOpDrugs'),
                'Per Op Drug Mappings' => array('module' => 'OphTrOperationnote', 'uri' => '/oeadmin/PostOpDrugMappings/list'),
                'Prescription Edit Options' => array('module'=> 'OphDrPrescription', 'uri' => '/OphDrPrescription/Admin/PrescriptionEditOptions')
            ),
            'Disorders' => array(
                'Common Ophthalmic Disorder Groups' => '/admin/editcommonophthalmicdisordergroups',
                'Common Ophthalmic Disorders' => '/admin/editcommonophthalmicdisorder',
                'Secondary Common Ophthalmic Disorders' => '/admin/editsecondarytocommonophthalmicdisorder',
                'Common Systemic Disorders' => '/oeadmin/CommonSystemicDisorder/list',
            ),
            'Consent' => array(
                'Leaflets' => array('module' => 'OphTrConsent', 'uri' => '/oeadmin/Leaflets/list'),
                'Leaflet Subspecialty and Firm Assignment' => array('module' => 'OphTrConsent', 'uri' => '/oeadmin/LeafletSubspecialtyFirm/list'),
            ),
        ),
    ),
);
