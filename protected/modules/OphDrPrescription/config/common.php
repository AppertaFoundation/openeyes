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
                // The 'All sets' screen is useful for debugging and may be required again in a later phase. So just commenting out for now
                // 'All Sets' => '/OphDrPrescription/admin/DrugSet/index',
                'Drug Sets' => '/OphDrPrescription/admin/AutoSetRule/index',
                'Local Drugs' => '/OphDrPrescription/OphDrPrescriptionAdmin/localDrugsAdmin/list',
                'Local Drug Mappings' => '/OphDrPrescription/OphDrPrescriptionAdmin/localDrugsAdmin/ListLocalDrugInstitutionMappings',
                'DM+D Drugs' => array('module' => 'OphDrPrescription', 'uri' => '/OphDrPrescription/OphDrPrescriptionAdmin/dmdDrugsAdmin/list', 'restricted' => array('admin')),
                'Per Op Drugs' => array('module' => 'OphTrOperationnote', 'uri' => '/OphTrOperationnote/admin/viewPostOpDrugs', 'restricted' => array('admin')),
                'Per Op Drug Mappings' => array('module' => 'OphTrOperationnote', 'uri' => '/oeadmin/PostOpDrugMappings/list'),
                'Prescription Edit Options' => array('module' => 'OphDrPrescription', 'uri' => '/OphDrPrescription/admin/default/PrescriptionEditOptions'),
                'Routes' => array('module' => 'OphDrPrescription', 'uri' => '/OphDrPrescription/routesAdmin/list', 'restricted' => array('admin')),
                'Dispense conditions' => array('module' => 'OphDrPrescription', 'uri' => '/OphDrPrescription/admin/DispenseCondition/index'),
                'Dispense locations' => array('module' => 'OphDrPrescription', 'uri' => '/OphDrPrescription/admin/DispenseLocation/index'),
            ],
        ]

    ],
    'aliases' => [
        'OphDrPrescriptionAdmin' => 'OEModule.OphDrPrescription.modules.OphDrPrescriptionAdmin',
    ],
    'modules' => ['OphDrPrescriptionAdmin'],

    'import' => ['application.modules.OphDrPrescription.components.*'],
];
