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
 * @var \OEModule\OphCiExamination\models\BirthHistory $element
 * @var \OEModule\OphCiExamination\widgets\BirthHistory $this
 */
?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath("BirthHistory.js") ?>"></script>
<?php $model_name = CHtml::modelName($element); ?>
<div class="element-fields flex-layout full-width" id="<?= $model_name ?>_form">
    <div class="cols-11">
        <table id="<?= $model_name ?>_entry_table" class="cols-full last-left">
        <colgroup>
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-2">
            <col class="cols-2">
            <col class="cols-2">
        </colgroup>
        <thead>
            <th><?= $element->getAttributeLabel('weight') ?></th>
            <th><?= $element->getAttributeLabel('birth_history_delivery_type_id') ?></th>
            <th><?= $element->getAttributeLabel('gestation_weeks') ?></th>
            <th><?= $element->getAttributeLabel('had_neonatal_specialist_care') ?></th>
            <th><?= $element->getAttributeLabel('was_multiple_birth') ?></th>
        </thead>
        <tbody>
            <tr>
                <td id="<?= $model_name ?>_weight_wrapper" class="nowrap">
                    <input name="<?= $model_name ?>[input_weight_kgs]"
                           type="text" placeholder="0.000" class="cols-8"
                           data-weight-type="kgs" value="<?= $this->getInputWeightKgs() ?>"
                           id="<?= "{$model_name}_input_weight_kgs" ?>"
                           data-adder-id="<?= "{$model_name}_input_weight_kgs" ?>"
                           data-adder-requires-item-set="<?= "{$model_name}_input_weight_mode" ?>"
                           data-adder-requires-item-set-values="[&quot;kgs&quot;]"
                           data-adder-item-set-type="float"
                           data-adder-item-set-decimal-places="3"
                           data-adder-item-set-max="9.999"
                           data-adder-header="kgs"
                           data-ec-keep-field="true"
                    />
                    <input name="<?= "{$model_name}[{$this::$INPUT_LB_PORTION_FLD}]" ?>"
                           type="text" placeholder="0" class="cols-4"
                           data-weight-type="lbs"
                           value="<?= $this->getInputWeightLbsPortion() ?>"
                           id="<?= "{$model_name}_input_weight_lbs_portion" ?>"
                           data-adder-id="<?= "{$model_name}_input_weight_lbs_portion" ?>"
                           data-adder-requires-item-set="<?= "{$model_name}_input_weight_mode" ?>"
                           data-adder-requires-item-set-values="[&quot;lbs&quot;]"
                           data-adder-item-set-type="float"
                           data-adder-item-set-max="22"
                           data-adder-header="lbs"
                           data-ec-keep-field="true"
                    />
                    <input name="<?= "{$model_name}[{$this::$INPUT_OZ_PORTION_FLD}]" ?>"
                           type="text" placeholder="00" class="cols-4"
                           data-weight-type="lbs"
                           value="<?= $this->getInputWeightOzsPortion() ?>"
                           id="<?= "{$model_name}_input_weight_ozs_portion" ?>"
                           data-adder-id="<?= "{$model_name}_input_weight_ozs_portion" ?>"
                           data-adder-requires-item-set="<?= "{$model_name}_input_weight_mode" ?>"
                           data-adder-requires-item-set-values="[&quot;lbs&quot;]"
                           data-adder-item-set-type="float"
                           data-adder-item-set-max="15"
                           data-adder-header="ozs"
                           data-ec-keep-field="true"
                    />

                    <?= \CHtml::dropDownList(
                            "{$model_name}_input_weight_mode",
                            $this->inputWeightMode(),
                            [
                                'kgs' => 'kgs',
                                'lbs' => 'lb/ozs',
                            ],
                            [
                                'class' => 'cols-4',
                                'data-adder-id' => "{$model_name}_input_weight_mode",
                                'data-adder-header' => $element->getAttributeLabel('weight'),
                                'data-ec-keep-field' => true
                            ]
                        );
                                                            ?>
                </td>
                <td>
                    <?= $form->dropDownList($element, 'birth_history_delivery_type_id',
                        CHtml::listData($element->delivery_type_options, 'id', 'name'), [
                            'empty' => '- Select -',
                            'nowrapper' => true,
                            'data-adder-header' => $element->getAttributeLabel('birth_history_delivery_type_id')
                        ]); ?>
                </td>
                <td>
                    <input name="<?= $model_name ?>[gestation_weeks]"
                           id="<?= $model_name ?>_gestation_weeks"
                           type="text", placeholder="40" class="cols-8"
                           value="<?= CHtml::encode($element->gestation_weeks) ?>"
                           data-adder-header="<?= $element->getAttributeLabel('gestation_weeks') ?>"
                           data-adder-item-set-type="float"
                           data-adder-item-set-max="42"
                           data-ec-keep-field="true"
                    />
                </td>
                <td>
                    <?= $form->dropDownList($element,
                        'had_neonatal_specialist_care',
                        CHtml::listData($element->nr_boolean_options, 'id', 'name'), [
                            'empty' => '- Select -',
                            'nowrapper' => true,
                            'data-adder-header' => $element->getAttributeLabel('had_neonatal_specialist_care')
                        ]); ?>
                </td>
                <td>
                    <?= $form->dropDownList($element,
                        'was_multiple_birth',
                        CHtml::listData($element->nr_boolean_options, 'id', 'name'), [
                            'empty' => '- Select -',
                            'nowrapper' => true,
                            'data-adder-header' => $element->getAttributeLabel('was_multiple_birth')
                        ]); ?>
                </td>
            </tr>
        </tbody>
    </table>
        <div id="birth-history-comments" class="cols-full js-comment-container"
         data-comment-button="#add-birthhistory-popup .js-add-comments"
         style="display: <?= $element->comments ? : "none"; ?>">
        <!-- comment-group, textarea + icon -->
            <div class="comment-group flex-layout flex-left">
                    <textarea id="<?= $model_name ?>_comments"
                              name="<?= $model_name ?>[comments]"
                              class="js-comment-field cols-10"
                              placeholder="Enter comments here"
                              autocomplete="off" rows="1"
                              style="overflow: hidden; word-wrap: break-word; height: 24px;"><?= CHtml::encode($element->comments) ?></textarea>
                <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
            </div>
        </div>
    </div>
    <div class="add-data-actions flex-item-bottom " id="add-birthhistory-popup">
        <button class="button js-add-comments"
                type="button"
                data-comment-container="#<?= $model_name ?>_form .js-comment-container"
                style="<?= $element->comments ? 'display: none;' : '' ?>">
            <i class="oe-i comments small-icon "></i>
        </button>
        <button class="button hint green js-add-select-search" data-adder-trigger="true" id="add-birthhistory-btn" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        const controller = new OpenEyes.OphCiExamination.BirthHistoryController({
            container: document.querySelector('#<?= $model_name ?>_form'),
            'adderHeaders': {
                'weight': '<?= $element->getAttributeLabel('weight') ?>',
                'deliveryType': '<?= $element->getAttributeLabel('birth_history_delivery_type_id') ?>',
                'gestationWeeks': '<?= $element->getAttributeLabel('gestation_weeks') ?>',
                'specialistCare': '<?= $element->getAttributeLabel('had_neonatal_specialist_care') ?>',
                'multipleBirths': '<?= $element->getAttributeLabel('was_multiple_birth') ?>'
            }
        });
    });
</script>