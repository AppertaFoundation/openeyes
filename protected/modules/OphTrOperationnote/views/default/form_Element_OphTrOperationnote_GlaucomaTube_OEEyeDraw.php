<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<?php
$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
    'doodleToolBarArray' => array(
        0 => array('TubeExtender', 'Patch', 'PI', 'IntraluminalStent', 'TubeLigation', 'ACMaintainer'),
    ),
    'onReadyCommandArray' => array(
        array('addDoodle', array('AntSeg')),
        array('addDoodle', array('Tube')),
        array('addDoodle', array('Patch')),
        array('addDoodle', array('IntraluminalStent')),
        array('deselectDoodles', array()),
    ),
    'bindingArray' => array(
        'Tube' => array(
            'platePosition' => array(
                'id' => 'Element_OphTrOperationnote_GlaucomaTube_plate_position_id',
                'attribute' => 'data-value',
            ),
        ),
    ),
    'listenerArray' => array(
        'glaucomaController',
    ),
    'side' => $this->selectedEyeForEyedraw->shortName,
    'idSuffix' => $element->elementType->id,
    'mode' => 'edit',
    'width' => 300,
    'height' => 300,
    'model' => $element,
    'attribute' => 'eyedraw',
    'offsetX' => 10,
    'offsetY' => 10,
    'scale' => 0.72,
    'template' => 'OEEyeDrawWidget_InlineToolbar',
));
