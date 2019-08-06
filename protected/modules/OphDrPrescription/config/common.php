<?php
/**
 * OpenEyes
 *
 * (C] OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option] any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c] 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

return [
    'params' => [
        'reports' => [
            'Prescribed drugs' => '/OphDrPrescription/report/prescribedDrugs',
        ],

        'admin_structure' => [
            'Drugs' => [
                'All Sets' => '/OphDrPrescription/admin/DrugSet/index',
                'All Medications' => '/OphDrPrescription/refMedicationAdmin/list',
                //'Old Auto set rules' => array('module' => 'OphDrPrescription', 'uri' => '/OphDrPrescription/medicationSetAutoRulesAdmin/list'),
                'Auto set rules' => '/OphDrPrescription/admin/AutoSetRule/index',
                'Local Drugs' => '/OphDrPrescription/localDrugsAdmin/list',
                'DM+D Drugs' => '/OphDrPrescription/dmdDrugsAdmin/list',
                'Export' => array('module' => 'OphDrPrescription', 'uri' => '/OphDrPrescription/RefMedicationAdmin/exportForm'),


                'Per Op Drugs' => array('module' => 'OphTrOperationnote', 'uri' => '/OphTrOperationnote/admin/viewPostOpDrugs'),
                'Per Op Drug Mappings' => array('module' => 'OphTrOperationnote', 'uri' => '/oeadmin/PostOpDrugMappings/list'),
                'Prescription Edit Options' => array('module' => 'OphDrPrescription', 'uri' => '/OphDrPrescription/admin/default/PrescriptionEditOptions'),
                /*
                'Tags' => '/TagsAdmin/list',
                'Drug types' => array('module' => 'OphDrPrescription', 'uri' => '/OphDrPrescription/admin/default/drugType'),
                */
                'Routes' => array('module' => 'OphDrPrescription', 'uri' => '/OphDrPrescription/routesAdmin/list'),
            ],
        ]

    ],
    'aliases' => [
        'OphDrPrescriptionAdmin' => 'OEModule.OphDrPrescription.modules.OphDrPrescriptionAdmin',
    ],
    'modules' => ['OphDrPrescriptionAdmin'],

    'import' => ['application.modules.OphDrPrescription.components.*'],
];
