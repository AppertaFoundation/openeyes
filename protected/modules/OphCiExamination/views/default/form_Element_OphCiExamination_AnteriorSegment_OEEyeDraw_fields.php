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
<div class="field-row">
    <?php echo CHtml::activeHiddenField($element, $side . '_ed_report'); ?>
    <div class="large-12 column autoreport-display">
        <span id="<?= CHtml::modelName($element) . '_' . $side . '_ed_report_display'?>" class="data-value"></span>
    </div>
</div>
<div class="field-row">
    <?php
        $default = OEModule\OphCiExamination\models\OphCiExamination_AnteriorSegment_Nuclear::getEyedrawDefault();
        echo $form->hiddenField($element, $side.'_nuclear_id', array(
            'data-eyedraw-map' => CJSON::encode(OEModule\OphCiExamination\models\OphCiExamination_AnteriorSegment_Nuclear::getEyedrawMapping()),
            'data-eyedraw-default' => $default ? $default->id : ''));

    $default = OEModule\OphCiExamination\models\OphCiExamination_AnteriorSegment_Cortical::getEyedrawDefault();
    echo $form->hiddenField($element, $side.'_cortical_id', array(
        'data-eyedraw-map' => CJSON::encode(OEModule\OphCiExamination\models\OphCiExamination_AnteriorSegment_Cortical::getEyedrawMapping()),
        'data-eyedraw-default' => $default ? $default->id : ''));
    ?>
    <div class="large-12 column end">
        <?php echo CHtml::activeTextArea($element, $side . '_description',
            array('rows' => '1', 'placeholder' => $element->getAttributeLabel($side . '_description'), 'nowrapper' => true)) ?>
    </div>
</div>

