<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/** @var BaseEventTypeController $this */
/** @var \OEModule\OphTrConsent\models\Element_OphTrConsent_BestInterestDecision $element */

$model_name = CHtml::modelName($element);
?>
<div class="element-fields full-width">
	<label class="highlight inline">
        <?php echo $form->checkBox($element, "patient_has_not_refused", array('nowrapper' => true)) ?>
    </label>
	<div id="if_patient_has_not_refused" class="<?= $element->patient_has_not_refused ? 'show' : 'hide'?>">
        <hr class="divider" />
        <div class="flex-t">
            <div class="cols-6">
                <?= str_replace('\n', '<br>', CHtml::encode($element->getAttributeLabel("reason_for_procedure")))?>
                <div class="row">
                    <?php echo $form->textArea(
                        $element,
                        "reason_for_procedure",
                        array('nowrapper' => true),
                        false,
                        array(
                            'class' => 'cols-full',
                            'rows' => '1',
                            'placeholder' => "Reason for procedure (mandatory)"
                        )
                    ); ?>
                </div>
            </div>
            <div class="cols-5">
                <div class="row">
                    <?= CHtml::encode($element->getAttributeLabel("treatment_cannot_wait_reason"))?> <br><span class="fade">For example: if patient unconscious, or where patient has fluctuating capacity.</span>
                </div>
                <div class="small-row">
                    <label class="highlight inline">
                        <?php echo $form->checkBox($element, "treatment_cannot_wait", array('nowrapper' => true)) ?>
                    </label>
                    <?php echo $form->textArea(
                        $element,
                        "treatment_cannot_wait_reason",
                        array('nowrapper' => true),
                        false,
                        array(
                            'class' => 'cols-full',
                            'rows' => '1',
                            'placeholder' => "Reason why treatment can not wait (optional)"
                        )
                    ); ?>
                </div>
                <div class="small-row">
                    <div class="small-row"><?= CHtml::encode($element->getAttributeLabel("wishes")).':'?></div>
                    <?php echo $form->textArea(
                        $element,
                        "wishes",
                        array('nowrapper' => true),
                        false,
                        array(
                            'class' => 'cols-full',
                            'rows' => '1',
                            'placeholder' => "Details of wishes, feelings values and beliefs relating to the decision"
                        )
                    ); ?>
                </div>
            </div>
        </div>
        <hr class="divider" />
        <div class="flex-t">
            <div class="upload-box cols-2">
                <label for="Document_right_document_id" id="upload_box" class="upload-label">
                    <i class="oe-i download no-click large"></i>
                    <br> Select or drop files here
                </label>
                <input class="js-file-input" multiple="multiple" autocomplete="off" type="file" name="Document[right_document_id]" style="display:none">
            </div>
            <div class="cols-9">
                <div class="small-row">
                    <i
                        class="oe-i info small pad-right js-has-tooltip"
                        data-tooltip-content='The following file types are accepted: <?= implode(", ", array_keys(static::$accepted_file_types))?> (Maximum size: <?= Helper::fileSize(static::$max_filesize) ?>)'>
                    </i>
                    Upload support documentation (optional)
                </div>
                <table class="cols-full">
                    <tbody class="js-list">
                        <?php $i = 0; ?>
                        <?php foreach ($element->attachments as $attachment): ?>
                            <tr data-id="<?= $attachment->id ?? "new" ?>">
                                <td>
                                    <?= $attachment->protected_file_id > 0 ? \CHtml::encode($attachment->protectedFile->name) : \CHtml::encode($attachment->tmp_name) ?>
                                </td>
                                <td>
                                    <?= $attachment->protected_file_id > 0 ? Helper::fileSize($attachment->protectedFile->getSize()) : "N/A" ?>
                                </td>
                                <td>
                                    <?php if($attachment->protected_file_id > 0): ?>
                                        <i class="oe-i tick-green small pad-right"></i>Attached
                                    <?php else: ?>
                                        <i class="oe-i cross-red small pad-right"></i>Error: unable to attach
                                    <?php endif; ?>
                                </td>
                                <td><i class="oe-i trash js-remove"></i></td>
                                <?=\CHtml::hiddenField("{$model_name}[attachments][{$i}][id]", $attachment->id ?? "new"); ?>
                                <?=\CHtml::hiddenField("{$model_name}[attachments][{$i}][protected_file_id]", $attachment->protected_file_id); ?>
                                <?php if(strlen($attachment->tmp_name) > 0) {
                                    echo \CHtml::hiddenField("{$model_name}[attachments][{$i}][tmp_name]", $attachment->tmp_name);
                                }?>
                            </tr>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $pt_has_capacity = $("#OEModule_OphTrConsent_models_Element_OphTrConsent_BestInterestDecision_patient_has_not_refused");
        var $if_patient_has_not_refused = $("#if_patient_has_not_refused");

        if($pt_has_capacity.prop("checked")) {
            $if_patient_has_not_refused.show();
        }
        else {
            $if_patient_has_not_refused.hide();
        }

        $pt_has_capacity.on("change", function () {
            if($(this).prop("checked")) {
                $if_patient_has_not_refused.show();
            }
            else {
                $if_patient_has_not_refused.hide();
            }
        });
    });
</script>