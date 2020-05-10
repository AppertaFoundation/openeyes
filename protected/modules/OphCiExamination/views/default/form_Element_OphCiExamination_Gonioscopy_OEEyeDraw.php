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
$mode = 'Basic';
$settings = new SettingMetadata();
$expert = $settings->getSetting('expert', $element->elementType);
if ($expert) {
    $mode = 'Expert';
}

$doodleToolBarArray = array('AngleNV', 'AntSynech', 'AngleRecession', 'Freehand');
$bindingArray = array(
    'Gonioscopy' => array(
        'mode' => array('id' => $side.'_gonioscopy_mode', 'attrivate' => 'data-value'),
    ),
);
$onReadyCommandArray = array(
    array('addDoodle', array('Gonioscopy', array('mode' => $mode))),
);
foreach (array('AngleGradeNorth' => 'sup', 'AngleGradeEast' => 'nas', 'AngleGradeSouth' => 'inf', 'AngleGradeWest' => 'tem') as $doodleClass => $position) {
    $bindingArray[$doodleClass]['grade'] = array('id' => 'OEModule_OphCiExamination_models_Element_OphCiExamination_Gonioscopy_'.$side.'_gonio_'.$position.'_id', 'attribute' => 'data-value');
    $bindingArray[$doodleClass]['seen'] = array('id' => $side.'_gonio_'.$position.'_basic', 'attribute' => 'data-value');
    $onReadyCommandArray[] = array('addDoodle', array($doodleClass));
}
$onReadyCommandArray[] = array('deselectDoodles', array());
$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
    'doodleToolBarArray' => $doodleToolBarArray,
    'onReadyCommandArray' => $onReadyCommandArray,
    'bindingArray' => $bindingArray,
    'listenerArray' => array('OphCiExamination_Gonioscopy_Eyedraw_Controller', 'autoReportListener'),
    'idSuffix' => $side.'_'.$element->elementType->id,
    'side' => ($side == 'right') ? 'R' : 'L',
    'mode' => 'edit',
    'model' => $element,
    'attribute' => $side.'_eyedraw',
    'template' => 'OEEyeDrawWidget_InlineToolbar',
    'maxToolbarButtons' => 12,
    'autoReport' => CHtml::modelName($element) . '_'.$side.'_ed_report',
    'autoReportEditable' => false,
    'fields' => $this->renderPartial($element->form_view.'_OEEyeDraw_fields', array(
        'form' => $form,
        'side' => $side,
        'element' => $element,
    ), true),
))?>
<script>
    $(document).ready(function(){
        setTimeout(function(){
            $('#<?= $side ?>_gonioscopy_mode option').filter('[data-value="<?= $mode ?>"]').attr('selected','selected');
            $('#<?= $side ?>_gonioscopy_mode').trigger('change');
        },500);
    });
</script>