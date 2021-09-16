<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
foreach ($disorder_section->disorders as $disorder) {
    $right_field_base_name = CHtml::modelName($element) . "[right_disorders][{$disorder->id}]";
    $field_base_name = CHtml::modelName($element) . "[disorders][{$disorder->id}]";
    if (!empty($disorder->disorder)) {
        $disorder_name = $disorder->disorder->term;
    } else {
        $disorder_name = $disorder->term_to_display;
    }
    ?>
        <tr>
            <td><?= \CHtml::encode($disorder->name); ?></td>
            <td>
                <label class="inline highlight ">
                <?= \CHtml::checkBox($right_field_base_name . "[main_cause]", $element->isCviDisorderMainCauseForSide($disorder, 'right'), array(
                        'class' => 'disorder-main-cause',
                        'disabled' => !$element->isCviDisorderMainCauseForAny($disorder, 'right'),
                        'data-active' => $element->hasCviDisorderForAny($disorder),
                    ),
                );
                ?>Main cause
                </label>
            </td>
            <td data-group_id="<?=$disorder->group_id;?>" data-disorder_id="<?=$disorder->id;?>">
                <label class="inline highlight icd10code">
                    <?=CHtml::encode($disorder->code)?>
                </label>

                <?php $this->widget('application.widgets.EyeSelector', [
                'inputNamePrefix' => $field_base_name,
                'selectedEyeId' => $element->getCviDisorderSide($disorder),
                'template' => "{Right}{Left}"
            ]);?>
            </td>
<!--            <td>-->
<!--                <button class="button button-icon small js-unchecked-diagnosis-element disabled" data-id=" php CHtml::encode($disorder->id) " title="Delete Diagnosis">-->
<!--                    <span class="icon-button-small-mini-cross"></span>-->
<!--                    <span class="hide-offscreen">Remove element</span>-->
<!--                </button>-->
<!--            </td>-->
        </tr>
<?php } ?>