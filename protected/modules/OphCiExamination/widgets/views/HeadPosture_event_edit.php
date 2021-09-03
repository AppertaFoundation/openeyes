<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var \OEModule\OphCiExamination\models\HeadPosture $element
 * @var \OEModule\OphCiExamination\widgets\HeadPosture $this
 */
?>
<?php $model_name = CHtml::modelName($element); ?>
<div class="element-fields flex-layout full-width" id="<?= $model_name ?>_form">
    <table class="cols-10 last-left">
        <colgroup>
            <col class="cols-2">
            <col class="cols-2">
            <col class="cols-2">
            <col class="cols-6">
        </colgroup>
        <thead>
        <th><?= $element->getAttributeLabel('tilt') ?></th>
        <th><?= $element->getAttributeLabel('turn') ?></th>
        <th><?= $element->getAttributeLabel('chin') ?></th>
        <th></th>
        </thead>
        <tbody>
        <tr>
            <td>
                <?= $form->dropDownList($element, 'tilt',
                    CHtml::listData($element->tilt_options, 'id', 'name'), [
                        'empty' => '- Select -',
                        'nowrapper' => true,
                        'data-adder-header' => $element->getAttributeLabel('tilt')
                    ]); ?>
            </td>
            <td>
                <?= $form->dropDownList($element, 'turn',
                    CHtml::listData($element->turn_options, 'id', 'name'), [
                        'empty' => '- Select -',
                        'nowrapper' => true,
                        'data-adder-header' => $element->getAttributeLabel('turn')
                    ]); ?>
            </td>
            <td>
                <?= $form->dropDownList($element, 'chin',
                    CHtml::listData($element->chin_options, 'id', 'name'), [
                        'empty' => '- Select -',
                        'nowrapper' => true,
                        'data-adder-header' => $element->getAttributeLabel('chin')
                    ]); ?>
            </td>
            <td>
                <button id="<?=$model_name ?>-comment-button"
                        class="button js-add-comments" data-comment-container="#<?=$model_name?>-comments"
                        type="button" style="<?= $element->comments ? 'display: none;' : '' ?>" data-hide-method="display">
                    <i class="oe-i comments small-icon"></i>
                </button>
                <div id="<?=$model_name?>-comments" class="flex-layout js-comment-container"
                     style="<?= !$element->comments ? 'display: none;' : '' ?>" data-comment-button="#<?=$model_name ?>-comment-button">
                    <?=\CHtml::activeTextArea($element, 'comments',
                        array(
                            'rows' => 1,
                            'placeholder' => $element->getAttributeLabel('comments'),
                            'class' => 'cols-full js-comment-field',
                            'style' => 'overflow-wrap: break-word; height: 24px;',
                        )) ?>
                    <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="add-data-actions flex-item-bottom " id="add-headposture-popup">
            <button class="button hint green js-add-select-search" data-adder-trigger="true" type="button">
                <i class="oe-i plus pro-theme"></i>
            </button>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        new OpenEyes.UI.ElementController({
            container: document.querySelector('#<?= $model_name ?>_form')
        });
    });
</script>