<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$base_name = CHtml::modelName($element) . "[{$side}_values][{$index}]";

?>

<tr data-index="<?= $index ?>" data-side="<?= $side ?>" data-index="<?= $index ?>">
    <td>
        <input type="hidden" name="<?= $base_name ?>[instrument_id]"
               id="<?= $base_name ?>[instrument_id]" value="<?= $instrumentId ?>"/>
        <div><?= $instrumentName ?></div>
    </td>
    <td style="<?=($value->instrument && $value->instrument->scale) ? "display: none":"" ?>">
        <?= $form->dropDownList(
            $value,
            'reading_id',
            'OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Reading',
            array(
                'nowrapper' => true,
                'data-base-name' => $base_name,
                'name' => "{$base_name}[reading_id]",
                'prompt' => '--'
            )
        ) ?>
    </td>
    <td class="scale_values" style="<?= (!$value->instrument || !$value->instrument->scale) ? "display: none":""?>">
        <?php if ($value->instrument && $value->instrument->scale) {
            echo $this->render(
                'application.modules.OphCiExamination.views.default._qualitative_scale',
                array(
                    'name' => CHtml::modelName($element),
                    'value' => $value,
                    'side' => $side,
                    'index' => $index,
                    'scale' => $value->instrument->scale
                )
            );
        } ?>
    </td>
    <td>
        <?= CHtml::textField(
            "{$base_name}[reading_time]",
            $time,
            array('autocomplete' => Yii::app()->params['html_autocomplete'], 'class' => 'fixed-width-small')
        ) ?>
    </td>
    <td>
        <input class="iop-date" autocomplete="off" type="text" value="<?=$examinationDate?>"
           id="<?= CHtml::modelName($element) . '_' . $side . '_values_' . $index . '_examination_date'?>"
           name="<?=$base_name?>[examination_date]">
    </td>

    <td>
        <div class="flex-layout flex-right">
            <span class="js-comment-container cols-full flex-layout"
                id="<?= CHtml::modelName($element) . '_' . $side . '_values_' . $index . '_comment_container' ?>"
                style="display: none;>"
                data-comment-button="#<?= CHtml::modelName($element) . '_' . $side . '_values_' . $index . '_comment_button' ?>">
                <?= CHtml::textArea($base_name . '['.$side.'_comments]', "", [
                    'rows' => 1,
                    'class' => 'js-comment-field',
                    'id' => CHtml::modelName($element) . '_' . $side . '_values_' . $index . '_'.$side.'_comments',
                ]) ?>

              <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
            </span>

            <button
                    id="<?= CHtml::modelName($element) . '_' . $side . '_values_' . $index . '_comment_button' ?>"
                    type="button"
                    class="button js-add-comments"
                    data-comment-container="#<?= CHtml::modelName($element) . '_' . $side . '_values_' . $index . '_comment_container' ?>">
                <i class="oe-i comments small-icon"></i>
            </button>
        </div>
    </td>

    <td>
        <?= CHtml::hiddenField("{$base_name}[eye_id]", ($side == 'left') ? Eye::LEFT : Eye::RIGHT) ?>
        <i class="oe-i trash"></i>
    </td>
</tr>
