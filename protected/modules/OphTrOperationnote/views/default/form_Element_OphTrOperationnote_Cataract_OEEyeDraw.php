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
?>
<?php
    $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
        'doodleToolBarArray' => array(
            array('PhakoIncision', 'SidePort', 'IrisHook', 'Malyugin', 'PCIOL', 'ACIOL', 'PI', 'MattressSuture',
                'CapsularTensionRing', 'CornealSuture', 'ToricPCIOL', 'LimbalRelaxingIncision', ),
        ),
        'bindingArray' => array(
            'PhakoIncision' => array(
                'incisionSite' => array('id' => 'Element_OphTrOperationnote_Cataract_incision_site_id', 'attribute' => 'data-value'),
                'incisionType' => array('id' => 'Element_OphTrOperationnote_Cataract_incision_type_id', 'attribute' => 'data-value'),
                'incisionLength' => array('id' => 'Element_OphTrOperationnote_Cataract_length'),
                'incisionMeridian' => array('id' => 'Element_OphTrOperationnote_Cataract_meridian'),
            ),
        ),
        'listenerArray' => array(
            'sidePortController',
        ),
        'idSuffix' => 'Cataract',
        'side' => $this->selectedEyeForEyedraw->shortName,
        'mode' => 'edit',
        'width' => 300,
        'height' => 300,
        'model' => $element,
        'attribute' => 'eyedraw',
        'offsetX' => 10,
        'offsetY' => 10,
        'template' => 'OEEyeDrawWidget_InlineToolbar',
        'autoReport' => 'Element_OphTrOperationnote_Cataract_report',
    ));
?>

<?php echo $form->hiddenInput($element, 'report2', $element->report2)?>
<?php
    $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
        'onReadyCommandArray' => array(
            array('addDoodle', array('OperatingTable')),
            array('addDoodle', array('Surgeon')),
            array('deselectDoodles', array()),
        ),
        'syncArray' => array(
            'Cataract' => array('Surgeon' => array('PhakoIncision' => array('parameters' => array('rotation')))),
        ),
        'idSuffix' => 'Position',
        'side' => $this->selectedEyeForEyedraw->shortName,
        'mode' => 'edit',
        'width' => 140,
        'height' => 140,
        'model' => $element,
        'attribute' => 'eyedraw2',
        'offsetX' => 10,
        'offsetY' => 10,
        'toolbar' => false,
        'showDrawingControls' => false,
        'showDoodlePopup' => false,
        'template' => 'OEEyeDrawWidget_InlineToolbar',
    ));
?>