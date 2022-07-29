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
    'params' => array(
        'admin_structure' => array(
            'System' => array(
                'Settings' => '/admin/settings',
                'DICOM Log Viewer' => '/DicomLogViewer/list',
            ),
            'Core' => array(
                'Users' => '/admin/users',
                'Teams' => '/oeadmin/team/list',
                'context_firm_labels and service_firm_labels' => '/Admin/context/index',
                'Contacts' => '/admin/contacts',
                'Contact labels' => ['uri' => '/admin/contactlabels', 'restricted' => array('admin')],
                'Data sources' => ['uri' => '/admin/datasources', 'restricted' => array('admin')],
                'LDAP Configurations' => ['uri' => '/admin/ldapconfig', 'restricted' => array('admin')],
                'Institutions' => ['uri' => '/admin/institutions', 'restricted' => array('admin')],
                'Sites' => '/admin/sites',
                'Patient Identifier Types' => ['uri' => '/Admin/PatientIdentifierType/index', 'restricted' => array('admin')],
                'Commissioning bodies' => ['uri' => '/admin/commissioning_bodies', 'restricted' => array('admin')],
                'Commissioning body types' => ['uri' => '/admin/commissioning_body_types', 'restricted' => array('admin')],
                'Commissioning body services' => ['uri' => '/admin/commissioning_body_services', 'restricted' => array('admin')],
                'Commissioning body service types' => ['uri' => '/admin/commissioning_body_service_types', 'restricted' => array('admin')],
                'Event deletion requests' => '/admin/eventDeletionRequests',
                'Custom episode summaries' => ['uri' => '/admin/episodeSummaries', 'restricted' => array('admin')],
                'Unique Codes' => ['uri' => '/oeadmin/uniqueCodes/list', 'restricted' => array('admin')],
                'Examination Event Logs' => '/oeadmin/eventLog/list',
                'Patient Shortcodes' => ['uri' => '/admin/patientshortcodes', 'restricted' => array('admin')],
                'Subspecialty Subsections' => ['uri' => '/oeadmin/subspecialtySubsections/list', 'restricted' => array('admin')],
                'Event Type Custom Text' => ['uri' => '/admin/editEventTypeCustomText', 'restricted' => array('admin')],
                'Element Type Custom Text' => ['uri' => '/admin/editElementTypeCustomText', 'restricted' => array('admin')],
            ),
            'SSO Settings' => array(
                'Default SSO Permissions' => ['uri' => '/sso/defaultssopermissions', 'restricted' => array('admin')],
                'SSO Roles Mappings' => ['uri' => '/sso/ssorolesauthassignment', 'restricted' => array('admin')]
            ),
            'Worklist' => array(
                'Automatic Worklists Definitions' => '/Admin/worklist/definitions',
                'Clinical Pathway Presets' => '/Admin/worklist/presetPathways',
                'Worklist Wait Times' => ['uri' => '/Admin/worklist/waitTimes', 'restricted' => array('admin')],
                'Worklist custom path steps' => ['uri' => '/Admin/worklist/customPathSteps', 'restricted' => ['admin']],
                'Visual Field Test Types' => ['uri' => '/Admin/worklist/visualFieldTestTypes', 'restricted' => ['admin']],
                'Visual Field Test Options' => ['uri' => '/Admin/worklist/visualFieldTestOptions', 'restricted' => ['admin']],
                'Visual Field Test Presets' => '/Admin/worklist/visualFieldTestPresets',
            ),
            'Procedure Management' => array(
                'Procedures' => ['uri' => '/oeadmin/procedure/list', 'restricted' => array('admin')],
                'Benefits' => ['uri' => '/oeadmin/benefit/list', 'restricted' => array('admin')],
                'Complications' => ['uri' => '/oeadmin/complication/list', 'restricted' => array('admin')],
                'OPCS Codes' => ['uri' => '/oeadmin/opcsCode/list', 'restricted' => array('admin')],
                'Procedure - Subspecialty Assignment' =>  '/Admin/procedureSubspecialtyAssignment/edit',
                'Procedure - Subspecialty Subsection Assignment' =>  '/oeadmin/SubspecialtySubsectionAssignment/list',
                'Clinic Procedure Assignment' => ['uri' => '/oeadmin/ClinicProcedure/list', 'restricted' => array('admin')]
            ),
            'Drugs' => array(
                // Hidden for now
                'Anaesthetic Agent' => ['uri' => '/admin/viewAnaestheticAgent', 'restricted' => array('admin')],
                'Anaesthetic Agent Mapping' => '/oeadmin/AnaestheticAgentMapping/list',
                'Anaesthetic Agent Defaults' => '/oeadmin/AnaestheticAgentDefaults/list',
            ),
            'Disorders' => array(
                'Common Ophthalmic Disorder Groups' => '/admin/editcommonophthalmicdisordergroups',
                'Common Ophthalmic Disorders' => '/admin/editcommonophthalmicdisorder',
                'Secondary Common Ophthalmic Disorders' => '/admin/editsecondarytocommonophthalmicdisorder',
                'Common Systemic Disorders Groups' => '/oeadmin/CommonSystemicDisorderGroup/list',
                'Common Systemic Disorders' => '/oeadmin/CommonSystemicDisorder/list',
                'Findings' => ['uri' => '/admin/managefindings', 'restricted' => array('admin')],
                'Disorders' => ['uri' => '/Admin/disorder/list', 'restricted' => array('admin')],
            ),
            'Investigation Management' => array(
                'Investigations' => ['uri' => '/oeadmin/investigation/list', 'restricted' => array('admin')]
            ),
        ),
    ),
);
