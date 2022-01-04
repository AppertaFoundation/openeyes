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
        'eyedraw_iol_classes' => array(
            'PCIOL',
            'ACIOL',
            'ToricPCIOL',
        ),
        'admin_menu' => array(
            'OphTrOperationnote' => [
                'Per Op Instructions' => '/OphTrOperationnote/admin/postOpInstructions',
                'Default Incision Length' => '/OphTrOperationnote/admin/viewIncisionLengthDefaults',
                'Operative Devices' => ['uri' => '/OphTrOperationnote/OperativeDevice/list', 'restricted' => array('admin')],
                'Operative Devices Mapping' => '/OphTrOperationnote/OperativeDeviceMapping/list',
                'Generic Operation Default Comments' => ['uri' => '/OphTrOperationnote/GenericProcedureData/list', 'restricted' => array('admin')],
                'Generic Operation Quick Text' => ['uri' => '/OphTrOperationnote/AttributesAdmin/list', 'restricted' => array('admin')],
            ]
        ),
        'reports' => array(
            'Operations' => '/OphTrOperationnote/report/operation',
        ),

        // Default anaesthetic settings
                //'ophtroperationnote_default_anaesthetic_child' => 'GA',
                //'ophtroperationnote_default_anaesthetic' => 'GA',
    ),
);
