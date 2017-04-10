<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div class="eyedraw-fields">
    <div class="field-row row">
        <div class="large-3 column">
            <label for="<?php echo CHtml::modelName($element).'_' . $side .'_stfb';?>"><?= $element->getAttributeLabel($side . '_stfb') ?></label>
        </div>
        <div class="large-9 column end">
            <?= CHtml::activeCheckBox($element, $side . '_stfb') ?>
        </div>
    </div>
    <?php echo CHtml::activeHiddenField($element, $side . '_ed_report'); ?>
    <div class="field-row">
            <label>
                <?php echo $element->getAttributeLabel($side . '_ed_report') ?>:
            </label>
        <div class="row collapse">
            <div class="large-11 column">
                <span id="<?= CHtml::modelName($element) . '_' . $side . '_ed_report_display'?>"> </span>
            </div>
        </div>

    </div>

    <div class="field-row">
        <label for="<?= get_class($element).'_'.$side.'_comments';?>">
            <?= $element->getAttributeLabel($side.'_comments'); ?>:
        </label>
        <?= CHtml::activeTextArea($element, $side.'_comments', array('rows' => '2', 'cols' => '20', 'class' => 'autosize clearWithEyedraw')) ?>
    </div>
</div>
