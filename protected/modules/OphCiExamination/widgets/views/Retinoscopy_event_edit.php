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

/** @var $element \OEModule\OphCiExamination\models\Retinoscopy */
/** @var $this \OEModule\OphCiExamination\widgets\Retinoscopy */

$model_name = \CHtml::modelName($element);
?>

<script type="text/javascript" src="<?= $this->getJsPublishedPath("Retinoscopy.js") ?>"></script>
<?php echo $form->hiddenInput($element, 'eye_id', false, ['class' => 'sideField']); ?>
<div class="element-fields element-eyes">
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) { ?>
        <div class="js-element-eye <?= $eye_side ?>-eye js-retinoscopy-form <?= $page_side ?>"
             id="<?= $model_name ?>_<?= $eye_side ?>_form"
             data-side="<?= $eye_side ?>"
        >
            <!-- active-form should not have any other classes -->
            <div class="active-form" style="<?= $element->hasEye($eye_side) ? '' : 'display: none;' ?>">
                <div class="remove-side"><i class="oe-i remove-circle small"></i></div>
                <div class="flex row">
                    <div class="cols-5">
                        <?php
                        $this->widget('application.modules.eyedraw.OEEyeDrawWidget', [
                            'listenerArray' => ['OpenEyes.OphCiExamination.retinoscopyEyedrawListener'],
                            'onReadyCommandArray' => [
                                ['addDoodle', ['RetinoscopyPowerCross']],
                                ['deselectDoodles', []],
                            ],
                            'bindingArray' => [
                                'RetinoscopyPowerCross' => [
                                    'workingDistance' => ['id' => "{$model_name}_{$eye_side}_working_distance_value"],
                                    'power1' => ['id' => "{$model_name}_{$eye_side}_power1"],
                                    'power2' => ['id' => "{$model_name}_{$eye_side}_power2"],
                                    'angle1' => ['id' => "{$model_name}_{$eye_side}_angle"]
                                ]
                            ],
                            'idSuffix' => "{$eye_side}_" . $element->elementType->id,
                            'side' => ($eye_side == 'right') ? 'R' : 'L',
                            'mode' => 'edit',
                            'model' => $element,
                            'attribute' => $eye_side . '_eyedraw',
                            'width' => 150,
                            'height' => 150,
                            'toolbar' => false,
                            'showDoodlePopup' => false
                        ]);
                        ?>
                    </div>

                    <!-- using tables in here for better layout control -->
                    <table class="cols-7">
                        <tbody>
                        <tr>
                            <td><?= $element->getAttributeLabel("{$eye_side}_dilated") ?></td>
                            <td><input id="<?= "{$model_name}_{$eye_side}_hidden" ?>" type="hidden" value="0" name="<?= "{$model_name}[{$eye_side}_dilated]" ?>" />
                                <?= \CHtml::checkBox(
                                    "{$model_name}[{$eye_side}_dilated]",
                                    (string)$element->{"{$eye_side}_dilated"} === '1',
                                    ['value' => '1', 'data-adder-ignore' => 'true']
                                ); ?></td>
                        </tr>
                        <tr>
                            <td><?= $element->getAttributeLabel("{$eye_side}_working_distance") ?></td>
                            <td><input type="hidden" id="<?= "{$model_name}_{$eye_side}_working_distance_value" ?>"
                                       value="<?= $element->{"{$eye_side}_working_distance"}
                                           ? $element->{"{$eye_side}_working_distance"}->value
                                           : $element->{"{$eye_side}_working_distance_options"}[0]->value ?>"
                                       data-adder-wd-value-field="true"
                                />
                                <?= \CHtml::dropDownList(
                                    "{$model_name}[{$eye_side}_working_distance_id]",
                                    $element->{"{$eye_side}_working_distance_id"},
                                    CHtml::listData($element->{"{$eye_side}_working_distance_options"}, 'id', 'name'),
                                    [
                                        'class' => 'cols-4',
                                        'data-adder-input-id' => "working_distance",
                                        'data-adder-header' => $element->getAttributeLabel("{$eye_side}_working_distance"),
                                        'data-adder-wd-select-field' => "true",
                                        'options' => array_combine(
                                            array_map(
                                                function ($distance) { return $distance->id; },
                                                $element->{"{$eye_side}_working_distance_options"}
                                            ),
                                            array_map(
                                                function ($distance) { return ['data-value' => $distance->value]; },
                                                $element->{"{$eye_side}_working_distance_options"}
                                            )
                                        )
                                    ]
                                );
                                ?></td>
                        </tr>
                        <tr>
                            <td><?= $element->getAttributeLabel("{$eye_side}_angle") ?></td>
                            <td><input type="text"
                                       name="<?= "{$model_name}[{$eye_side}_angle]" ?>"
                                       id="<?= "{$model_name}_{$eye_side}_angle" ?>"
                                       data-adder-input-id="angle"
                                       data-adder-header="<?= $element->getAttributeLabel("{$eye_side}_angle") ?>"
                                       value="<?= CHtml::encode($element->{"{$eye_side}_angle"}) ?>"
                                       data-adder-item-set-type="float"
                                       data-adder-item-set-max="180"
                                       required="required"
                                /></td>
                        </tr>
                        <tr>
                            <td><?= $element->getAttributeLabel("{$eye_side}_power1") ?></td>
                            <td><input type="text"
                                       id="<?= "{$model_name}_{$eye_side}_power1" ?>"
                                       name="<?= "{$model_name}[{$eye_side}_power1]" ?>"
                                       data-adder-input-id="power1"
                                       data-adder-item-set-type="float"
                                       data-adder-item-set-max="30"
                                       data-adder-item-set-support-sign="true"
                                       data-adder-item-set-support-decimal-values="true"
                                       data-ec-keep-field="true"
                                       data-ec-format-fixed="2"
                                       data-ec-format-force-sign="true"
                                       data-adder-header="<?= $element->getAttributeLabel("{$eye_side}_power1") ?>"
                                       value="<?= CHtml::encode($element->{"{$eye_side}_power1"}) ?>"
                                       required="required"
                                       class="fixed-width-medium"></td>
                        </tr>
                        <tr>
                            <td><?= $element->getAttributeLabel("{$eye_side}_power2") ?></td>
                            <td><input type="text"
                                       id="<?= "{$model_name}_{$eye_side}_power2" ?>"
                                       name="<?= "{$model_name}[{$eye_side}_power2]" ?>"
                                       data-adder-input-id="power2"
                                       data-adder-item-set-type="float"
                                       data-adder-item-set-max="30"
                                       data-adder-item-set-support-sign="true"
                                       data-adder-item-set-support-decimal-values="true"
                                       data-ec-keep-field="true"
                                       data-ec-format-fixed="2"
                                       data-ec-format-force-sign="true"
                                       data-adder-header="<?= $element->getAttributeLabel("{$eye_side}_power2") ?>"
                                       value="<?= CHtml::encode($element->{"{$eye_side}_power2"}) ?>"
                                       required="required"
                                       class="fixed-width-medium"></td>
                        </tr>
                        <tr>
                            <td>Refraction</td>
                            <td><span class="js-refraction-display"><?= $element->{"{$eye_side}_refraction"} ?></span>
                                <input type="hidden"
                                       id="<?= "{$model_name}_{$eye_side}_refraction" ?>"
                                       name="<?= "{$model_name}[{$eye_side}_refraction]" ?>"
                                       class="js-refraction-field"
                                /></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex">
                    <div class="cols-10">
                        <!-- comments wrapper -->
                        <div id="retinoscopy-<?= $eye_side ?>-comments"
                             class="cols-full js-comment-container"
                             data-comment-button="#retinoscopy-<?= $eye_side ?>-comment-button"
                             style="<?= $element->{"{$eye_side}_comments"} ? "" : "display: none;" ?>">
                            <!-- comment-group, textarea + icon -->
                            <div class="comment-group flex-layout flex-left">
                                    <textarea placeholder="Comments"
                                              rows="1"
                                              class="js-comment-field cols-full"><?= CHtml::encode($element->{"{$eye_side}_comments"}) ?></textarea>
                                <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"
                                   data-hide-method="display"></i>
                            </div>
                        </div>
                    </div>
                    <!-- use flex to position the 'add-data-actions' -->
                    <div class="add-data-actions flex-item-bottom ">

                        <button
                            class="button js-add-comments"
                            id="retinoscopy-<?= $eye_side ?>-comment-button"
                            data-comment-container="#retinoscopy-<?= $eye_side ?>-comments"
                            type="button"
                            data-hide-method="display"
                            style="<?= $element->{"{$eye_side}_comments"} ? "display: none;" : "" ?>"
                        >
                            <i class="oe-i comments small-icon "></i>
                        </button>
                        <button class="button hint green js-add-select-btn"
                                data-adder-trigger="true"><i class="oe-i plus pro-theme"></i></button>
                    </div>
                </div><!-- flex -->

            </div><!-- end of active-form -->
            <!-- inactive form -->
            <div class="inactive-form" style="display:none">
                <div class="add-side">
                    <a href="#">Add <?= $eye_side ?> side</a>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
