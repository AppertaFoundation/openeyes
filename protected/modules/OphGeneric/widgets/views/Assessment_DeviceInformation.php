<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
$medical_retina_open = true;
$medical_retina_entry_values = $this->entry->getAssessmentEntryRadioButtonValues();
?>
<?= \CHtml::hiddenField(
    "OEModule_OphGeneric_models_Assessment" . ($this->key !== null ? "[entries][$this->key][$this->side][row]" : '[entries][row]'),
    $this->row !== null ? $this->row : "",
    ["class" => 'js-hidden-oct-row']
) ?>
<?= \CHtml::hiddenField(
    "OEModule_OphGeneric_models_Assessment" . ($this->key !== null ? "[entries][$this->key][$this->side][side]" : '[entries][side]'),
    $this->side !== null ? $this->side : "",
    ["class" => 'js-hidden-oct-side']
) ?>

<?= \CHtml::hiddenField(
    "OEModule_OphGeneric_models_Assessment" . ($this->key !== null ? "[entries][$this->key][$this->side][entry_id]" : '[entries][entry_id]'),
    $this->entry !== null ? $this->entry->id : "",
    ["class" => 'js-hidden-oct-entry-id']
) ?>

<div class="js-assessment-medical-retina data-group OEModule_OphGeneric_models_Assessment">
    <div class="data-value listview-expand-collapse">
        <div class="cols-11">
            <h4>
                <?php $this->widget('EyeLateralityWidget', array('eye' => $this->entry->eye)); ?>
                Medical Retina
            </h4>
            <div id="js-listview-assessment-medical-retina-pro" <?= $medical_retina_open ? 'style="display:none"' : ''; ?>></div>
            <div id="js-listview-assessment-medical-retina-full" <?= !$medical_retina_open ? 'style="display:none"' : ''; ?>>
                <table class="standard">
                    <tbody>
                        <?php foreach (['crt', 'avg_thickness', 'cst', 'total_vol'] as $item) { ?>
                            <tr>
                                <td><?= $this->entry->getAttributeLabel($item) ?></td>
                                <td>
                                    <?= \AbacChtml::activeTextField(
                                        $this->entry,
                                        ($this->key !== null ? "[$this->key][$this->side]$item" : $item),
                                        ['class' => 'fixed-width-small', 'disabled' => null,
                                            'name' => "OEModule_OphGeneric_models_Assessment" . ($this->key !== null ? "[entries][$this->key][$this->side][$item]" : "[{$item}}]"),
                                        'id' => "OEModule_OphGeneric_models_Assessment_entries_" . ($this->key !== null ? "{$this->key}_{$this->side}_{$item}" : "[{$item}}]")]
                                    ) ?>
                                    <?php echo $this->entry->getMeasurementUnit($item); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <br>

                <table class="standard">
                    <colgroup>
                        <col class="cols-3">
                    </colgroup>
                    <tbody>
                        <?php  foreach (['irf', 'srf', 'cysts', 'retinal_thickening', 'ped', 'cmo', 'dmo', 'heamorrhage', 'exudates'] as $item) {?>
                            <tr>
                                <td><?= $this->entry->getAttributeLabel($item); ?></td>
                                <td>
                                    <fieldset>
                                        <?php foreach (['remove', 'red', 'orange', 'green'] as $index => $icon) {?>
                                            <?php
                                            $entry_value = $medical_retina_entry_values[$icon];
                                            $name = 'OEModule_OphGeneric_models_Assessment' . ($this->key !== null ? "[entries][$this->key][$this->side][$item]" : "[$item]");
                                            $tooltip_text = $this->entry->getTooltipText();
                                            ?>
                                            <label class="inline highlight">
                                                <?=\CHtml::radioButton(
                                                    $name,
                                                    ( (string)  $this->entry->$item === (string)$entry_value),
                                                    ['disabled' => null,
                                                        'id' => "OEModule_OphGeneric_models_Assessment_entries_" . ($this->key !== null ? "{$this->key}_{$this->side}_{$item}_{$icon}" : "{$item}_{$id}"),
                                                        'class' => "js-assessment-" . $item,
                                                        'value' => $entry_value
                                                    ]
                                                )  ?>
                                                <?php if (strpos($icon, 'remove') !== false) { ?>
                                                    <i class="oe-i medium remove js-has-tooltip" data-tooltip-content="<?= $tooltip_text[$entry_value] ?>"></i>
                                                <?php } else { ?>
                                                    <span class="highlighter <?= $icon ?> large-text js-has-tooltip" data-tooltip-content="<?= $tooltip_text[$entry_value] ?>"><?= $this->entry->getEntryIcon($entry_value)?></span>
                                                <?php } ?>
                                            </label>
                                        <?php } ?>
                                    </fieldset>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <hr class="divider">
            </div>
        </div>
        <div class="expand-collapse-icon-btn">
            <i class="oe-i small js-listview-expand-btn-assessment <?= $medical_retina_open ? 'collapse' : 'expand'; ?>"
               data-list="assessment-medical-retina"></i>
        </div>
    </div>
</div>

<div class="js-assessment-glaucoma data-group">
    <div class="data-value listview-expand-collapse expand">
        <div class="cols-11">
            <h4>
                <?php $this->widget('EyeLateralityWidget', array('eye' => $this->entry->eye)); ?>
                Glaucoma
            </h4>
            <div id="js-listview-assessment-glaucoma-full" style="display: none">
                <table class="standard">
                    <tbody>
                    <?php foreach (['avg_rnfl', 'cct', 'cd_ratio'] as $item) { ?>
                        <tr>
                            <td><?= $this->entry->getAttributeLabel($item) ?></td>
                            <td><?= CHtml::activeTextField(
                                $this->entry,
                                ($this->key !== null ? "[$this->key][$this->side]$item" : $item),
                                ['class' => 'fixed-width-small', 'disabled' => null,
                                'name' => "OEModule_OphGeneric_models_Assessment" . ($this->key !== null ? "[entries][$this->key][$this->side][$item]" : "[$item]"),
                                'id' => "OEModule_OphGeneric_models_Assessment_entries_" . ($this->key !== null ? "{$this->key}_{$this->side}_$item" : "$item")]
                                ); ?>
                                <?php echo strpos($item, 'cd_ratio') === false ? 'μm' : '&nbsp; &nbsp; &nbsp;&nbsp;' ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div id="js-listview-assessment-glaucoma-pro"></div>
        </div>
        <div class="expand-collapse-icon-btn">
            <i class="oe-i small js-listview-expand-btn-assessment expand" data-list="assessment-glaucoma"></i>
        </div>
    </div>
    <hr class="divider cols-11">
</div>
<div class="flex-layout">
    <div class="cols-full">
        <div id="assessment-<?= $this->side ?>-comments-<?=$this->key?>" class="flex-layout flex-left comment-group js-comment-container"
             style="<?= !$this->entry->comments ? 'display: none;' : '' ?>" data-comment-button="#assessment-<?= $this->side ?>-comment-button-<?=$this->key?>">
            <?=\CHtml::activeTextArea(
                $this->entry,
                'comments',
                array(
                    'rows' => 1,
                    'placeholder' => $this->entry->getAttributeLabel('comments'),
                    'class' => 'autosize cols-full js-comment-field',
                    'style' => 'overflow: hidden; overflow-wrap: break-word; height: 24px;',
                    'name' => 'OEModule_OphGeneric_models_Assessment' . ($this->key !== null ? "[entries][$this->key][$this->side][comments]" : '[comments]')
                )
            ) ?>
            <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
        </div>
    </div>
    <button id="assessment-<?= $this->side ?>-comment-button-<?=$this->key?>"
            class="button js-add-comments" data-comment-container="#assessment-<?= $this->side ?>-comments-<?=$this->key?>"
            type="button" style="<?= $this->entry->comments ? 'visibility: hidden;' : '' ?>">
        <i class="oe-i comments small-icon"></i>
    </button>
</div>
<hr style="visibility: hidden;">
<script type="text/javascript">
    $(document).ready(function () {
        autosize($('.js-comment-field'));
    })
</script>
