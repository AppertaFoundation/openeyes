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

/** @var $element \OEModule\OphCiExamination\models\CorrectionGiven */
/** @var $this \OEModule\OphCiExamination\widgets\CorrectionGiven */

$model_name = \CHtml::modelName($element);
?>

<script type="text/javascript" src="<?= $this->getJsPublishedPath("CorrectionGiven.js") ?>"></script>
<div class="element-fields element-eyes">
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) { ?>
        <div class="js-element-eye <?= $eye_side ?>-eye js-correction-given-form <?= $page_side ?>" data-side="<?= $eye_side ?>" id="<?= "{$model_name}_{$eye_side}_form" ?>">
        <!-- active-form should not have any other classes -->
            <div class="active-form" style="<?= $element->hasEye($eye_side) ? '' : 'display: none;' ?>">
                <div class="remove-side"><i class="oe-i remove-circle small"></i></div>
                <div class="flex-layout">
                    <table class="cols-10">
                        <colgroup>
                            <col class="cols-6">
                            <col class="cols-5">
                        </colgroup>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="hidden"
                                           name="<?= "{$model_name}[{$eye_side}_as_found]" ?>"
                                           value="<?= CHtml::encode($element->{"{$eye_side}_as_found"}) ?>"
                                           class="js-as-found"
                                    />
                                    <input type="hidden"
                                           name="<?= "{$model_name}[{$eye_side}_as_found_element_type_id]" ?>"
                                           value="<?= CHtml::encode($element->{"{$eye_side}_as_found_element_type_id"}) ?>"
                                           class="js-as-found-element-type-id"
                                    />
                                    <div class="js-label-as-found"
                                         style="<?= $element->{"{$eye_side}_as_found"} ? "" : "display: none;" ?>"><?= $element::ORDER_AS_FOUND_LABEL ?></div>
                                    <div class="js-label-as-adjusted"
                                         style="<?= $element->{"{$eye_side}_as_found"} ? "display: none;" : "" ?>"><?= $element::ORDER_AS_ADJUSTED_LABEL ?></div>
                                </td>
                                <td>
                                    <div class="js-refraction-display"
                                         style="<?= $element->{"{$eye_side}_as_found"} ? "" : "display: none;" ?>"><?= CHtml::encode($element->{"{$eye_side}_refraction"}) ?></div>
                                    <input type="text" class="fixed-width-large js-refraction"
                                           id="<?= "{$model_name}_{$eye_side}_refraction" ?>"
                                           name="<?= "{$model_name}[{$eye_side}_refraction]" ?>"
                                           value="<?= CHtml::encode($element->{"{$eye_side}_refraction"}) ?>"
                                           style="<?= $element->{"{$eye_side}_as_found"} ? "display: none;" : "" ?>"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- use flex to position the 'add-data-actions' -->
                    <div class="add-data-actions flex-item-bottom ">
                        <button class="button hint green  js-add-select-btn" data-adder-trigger="true"><i class="oe-i plus pro-theme"></i></button>
                    </div><!-- add-data-actions -->
                </div>
            </div><!-- end of active-form -->

            <!-- inactive form -->
            <div class="inactive-form" style="<?= $element->hasEye($eye_side) ? 'display: none;' : '' ?>">
                <div class="add-side">
                    <a href="#">Add <?= ucfirst($eye_side) ?> side</a>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            new OpenEyes.OphCiExamination.CorrectionGivenController({
                side: '<?= $eye_side ?>',
                container: document.querySelector('#<?= "{$model_name}_{$eye_side}_form" ?>'),
                asFoundElementTypes: <?= $this->getJsonAsFoundEventTypeOptions() ?>
            });
        </script>
    <?php } ?>

</div>
