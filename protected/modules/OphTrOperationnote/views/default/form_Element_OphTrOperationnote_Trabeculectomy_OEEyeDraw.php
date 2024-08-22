<?php

/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<?php
$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
    'doodleToolBarArray' => array(
        array('TrabySuture', 'PI', 'SidePort', 'Patch', 'ConjunctivalSuture', 'ACMaintainer', 'CornealSuture'),
    ),
    'onReadyCommandArray' => array(
        array('addDoodle', array('AntSeg')),
        array('addDoodle', array('ConjunctivalFlap')),
        array('addDoodle', array('PI', array('rotation' => 0))),
        array(
            'addDoodle',
            array('SidePort', array('rotation' => ($this->selectedEyeForEyedraw->name == 'Right' ? 5 : 3) * pi() / 4)),
        ),
        array('addDoodle', array('TrabyFlap')),
        array('addDoodle', array('TrabySuture')),
        array('addDoodle', array('TrabySuture')),
        array('addDoodle', array('TrabySuture')),
    ),
    'model' => $element,
    'attribute' => 'eyedraw',
    'side' => $this->selectedEyeForEyedraw->shortName,
    'idSuffix' => 'Trabeculectomy',
    'mode' => 'edit',
    'width' => 300,
    'height' => 300,
    'template' => 'OEEyeDrawWidget_InlineToolbar',
    'scale' => 0.72,
    'autoReport' => 'Element_OphTrOperationnote_Trabeculectomy_report',
    'listenerArray' => array(
        'trabeculectomyController',
    ),
    'bindingArray' => array(
        'ConjunctivalFlap' => array(
            'method' => array(
                'id' => 'Element_OphTrOperationnote_Trabeculectomy_conjunctival_flap_type_id',
                'attribute' => 'data-value',
            ),
        ),
        'TrabyFlap' => array(
            'site' => array(
                'id' => 'Element_OphTrOperationnote_Trabeculectomy_site_id',
                'attribute' => 'data-value',
            ),
            'size' => array(
                'id' => 'Element_OphTrOperationnote_Trabeculectomy_size_id',
                'attribute' => 'data-value',
            ),
            'sclerostomy' => array(
                'id' => 'Element_OphTrOperationnote_Trabeculectomy_sclerostomy_type_id',
                'attribute' => 'data-value',
            ),
        ),
    ),
));
