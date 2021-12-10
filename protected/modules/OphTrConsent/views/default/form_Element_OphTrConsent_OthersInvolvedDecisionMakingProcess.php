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
    $model_name = CHtml::modelName($element);

    $url = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.assets.js'), true);
    Yii::app()->clientScript->registerScriptFile($url . '/OpenEyes.UI.AdderDialog.Contact.js', CClientScript::POS_END);
?>

<div class="element-actions"><!-- note: order is important because of Flex, trash must be last element -->
    <!-- No icons --></div><!-- *** Element DATA in EDIT mode *** -->
<div class="element-fields full-width">
    <div class="flex">
        <div class="cols-11">
            <table class="cols-full last-left" id="js-patient_contacts">
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-3">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-1">
                </colgroup>
                <thead>
                    <th>Relationship</th>
                    <th>Contact Person</th>
                    <th>Contact Method</th>
                    <th>Signature required</th>
                    <th>Comment</th>
                </thead>
                <tbody>
                <?php foreach ($element->consentContact as $row_id => $contact) { ?>
                    <tr>
                        <th><?= $contact->getRelationshipName() ?></th>
                        <td><?= $contact->getFullName() ?></td>
                        <td><?= $contact->getContactMethodName() ?></td>
                        <td>
                            <?php if ($contact->getSignatureRequiredFromContactMethodType()===2) {
                                echo \CHtml::radioButtonList(
                                    $model_name . "[signature_required][]_" . $row_id,
                                    $contact->getSignatureRequired(),
                                    [
                                        1 => $contact->consentPatientContactMethod::getTypeLabel('SIGNATURE_REQUIRED'),
                                        0 => $contact->consentPatientContactMethod::getTypeLabel('SIGNATURE_NOT_REQUIRED'),
                                    ],
                                    array_merge(
                                        [],
                                        $contact->consentPatientContactMethod->need_signature === '2' ? [] : ['disabled' => 'disabled']
                                    )
                                );
                            } else {
                                echo $contact->consentPatientContactMethod->getDefaultSignatureRequiredString();
                                echo \CHtml::hiddenField(
                                    $model_name . "[signature_required][]_" . $row_id,
                                    $contact->getSignatureRequired(),
                                    ['class' => 'signature_required', 'id' => $model_name . "_signature_required_" . $row_id]
                                );
                            }
                            ?>
                        </td>
                        <td>
                            <?= \CHtml::hiddenField(
                                $model_name . "[jsonData][]",
                                $contact->getJsonData(),
                                ['class' => 'contact_json_data', 'id'=> $model_name.'_'.$row_id.'_jsonData']
                            ) ?>
                            <div class="cols-full">
                                <div class="js-comment-container flex-layout flex-left"
                                    id="<?= $model_name ?>_<?= $row_id ?>_comment_container"
                                    style="<?= (strlen($contact->comment) === 0) ? 'display: none;' : '' ?>"
                                    data-comment-button="#<?= $model_name ?>_<?= $row_id ?>_comments_button"

                                >
                                        <textarea
                                                class="js-comment-field autosize cols-full"
                                                rows="1"
                                                placeholder="Comment"
                                                autocomplete="off"
                                                name="<?= $model_name ?>[comment][]"
                                                id="<?= $model_name ?>_<?= $row_id ?>_comment"
                                        ><?= \CHtml::encode($contact->comment) ?></textarea>
                                    <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                                </div>
                                <button id="<?= $model_name ?>_<?= $row_id ?>_comments_button"
                                        class="button js-add-comments"
                                        data-comment-container="#<?= $model_name ?>_<?= $row_id ?>_comment_container"
                                        type="button"
                                        data-hide-method="display"
                                        style="<?= (strlen($contact->comment) > 0) ? 'display: none;' : '' ?>"
                                ><i class="oe-i comments small-icon"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            <i class="oe-i trash remove-patient-contact"></i>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="add-data-actions flex-item-bottom">
            <button id="add_patient_contact_button" type="button" class="green hint js-add-select-btn"
                    data-popup="add-patient-contact">Add contact
            </button>
        </div>
    </div>
</div>

<!-- MOUSTACHE TEMPLATE !-->
<table class="hidden" id="js-mustache_template">
    <tr>
        <td>{{relationship}}</td>
        <td>{{name}}</td>
        <td>{{consent_patient_contact_method}}</td>
        <td>
            <div class='hidden js-signature-needed'>
                {{signature_require_string}}
                <?= \CHtml::hiddenField(
                    $model_name . "[signature_required][]_{{uniqueId}}",
                        '{{signature_require}}',
                        ['class' => 'signature_required', 'id' => $model_name . "_signature_required_{{uniqueId}}"]
                    );
?>
            </div>
            <div class='hidden js-signature-needed-radio'>
                <?php
                    echo \CHtml::radioButtonList(
                        $model_name . "[signature_required][]_{{uniqueId}}",
                        1,
                        [1 => 'Signature required', 0 => 'No signature'],
                        ['class'=>'js-signature-require'],
                    );
                    ?>
            </div>
        </td>
        <td>
            <?= \CHtml::hiddenField(
                $model_name . "[jsonData][]",
                '',
                ['class' => 'contact_json_data', 'id'=> $model_name.'_{{uniqueId}}_jsonData']
            ) ?>
            <div class="cols-full">
                <div class="js-comment-container flex-layout flex-left"
                     id="<?= $model_name ?>_{{uniqueId}}_comment_container"
                     style="display: none;"
                     data-comment-button="#<?= $model_name ?>_{{uniqueId}}_comments_button">
                        <textarea
                                class="js-comment-field autosize cols-full"
                                rows="1"
                                placeholder="Comment"
                                autocomplete="off"
                                name="<?= $model_name ?>[comment][]"
                                id="<?= $model_name ?>_{{uniqueId}}_comment"
                        ></textarea>
                    <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                </div>
                <button id="<?= $model_name ?>_{{uniqueId}}_comments_button"
                        class="button js-add-comments"
                        data-comment-container="#<?= $model_name ?>_{{uniqueId}}_comment_container"
                        type="button"
                        data-hide-method="display"
                        style=""
                ><i class="oe-i comments small-icon"></i>
                </button>
            </div>
        </td>
        <td>
            <i class="oe-i trash remove-patient-contact"></i>
        </td>
    </tr>
</table>

<script>
    $(document).ready(function () {
        new OpenEyes.UI.AdderDialog.Contact({
            id: 'patient_contact_adder',
            patientId: window.OE_patient_id || null,
            openButton: $('#add_patient_contact_button'),
            width: "600px",
            deselectOnReturn: true,
            deselectOnClose: true,
            newContactDialogURL: "<?= Yii::app()->createUrl('/OphTrConsent/default'); ?>/ContactPage",
            ulClass: "category-filter",
            listFilter: true,
            itemSets:
                $.map(<?= CJSON::encode($element->getContactTypeItemSet()) ?>, function ($itemSet) {
                    return new OpenEyes.UI.AdderDialog.ItemSet($itemSet.items, {
                        'header': $itemSet.header,
                        'multiSelect': $itemSet.multiSelect,
                        'id' : $itemSet.id
                    });
                }),
            searchOptions: {
                searchSource: "",
                code: window.OE_patient_id || null,
            },
            //enableCustomSearchEntries: true,
            //searchAsTypedPrefix: 'Add a new contact:',
        });


        // Change columns 2-3
        $("#patient_contact_adder tr").each(function() {
            $(this).children(":eq(3)").after($(this).children(":eq(2)"));
        })

        $("#patient_contact_adder thead").each(function() {
            $(this).children(":eq(3)").after($(this).children(":eq(2)"));
        });

        $('body').on('click', '.remove-patient-contact', function() {
            $(this).closest('tr').remove();
        });
    });
</script>