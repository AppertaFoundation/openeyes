<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
/** @var \OEModule\OphTrConsent\models\Element_OphTrConsent_AdditionalSignatures $element */
$el_class = CHtml::modelName($element);
$consent_type_id = (int)$element->cf_type_id;
?>
<div class="element-fields full-width">
    <div class="cols-10">
        <table class="cols-full last-left">
            <colgroup><col class="cols-6"></colgroup>
            <tbody>
            <?= \CHtml::hiddenField(
                $el_class . "[cf_type_id]",
                $consent_type_id
            ); ?>

            <?php if ($consent_type_id === Element_OphTrConsent_Type::TYPE_PARENTAL_AGREEMENT_ID) { ?>
                <tr>
                    <td>Child's agreement to treatment</td>
                    <td>
                        <?= CHtml::radioButtonList(
                            $el_class . '[child_agreement]',
                            $element->child_agreement,
                            [1 => 'Yes', 0 => 'N/A'],
                            ['separator' => ' ']
                        ); ?>
                    </td>
                </tr>
            <?php } else { ?>
                <tr>
                    <td>
                        <label class="highlight inline">
                            <?= \CHtml::hiddenField(
                                $el_class . "[witness_required]",
                                $element->witness_required
                            ); ?>

                            <?= \CHtml::checkBox(
                                "",
                                $element->witness_required === "1",
                                [
                                    "id" => "additional_signatures_witness_required"
                                ]
                            )?>
                            Witness required
                        </label>
                    </td>
                    <td>
                        <?= \CHtml::textField(
                            $el_class . "[witness_name]",
                            $element->witness_name,
                            [
                                'id' =>  $el_class . "_witness_name",
                                'class' => 'cols-full',
                                'placeholder' => 'Witness name',
                                'disabled' => true
                            ]
                        ) ?>
                    </td>
                </tr>
            <?php }  ?>
            <tr>
                <td>
                    <label class="highlight inline">
                        <?= \CHtml::hiddenField(
                            $el_class . "[interpreter_required]",
                            $element->interpreter_required
                        ); ?>
                        <?= \CHtml::checkBox(
                            "",
                            $element->interpreter_required === "1",
                            [
                                "id" => "additional_signatures_interpreter_required"
                            ]
                        )?>
                        Interpreter required
                    </label>
                </td>
                <td>
                    <?= \CHtml::textField(
                        $el_class . "[interpreter_name]",
                        $element->interpreter_name,
                        [
                            'id' =>  $el_class . "_interpreter_name",
                            'class' => 'cols-full',
                            'placeholder' => 'Interpreter name',
                            'disabled' => true
                        ]
                    ) ?>
                </td>
            </tr>

            <?php if ($consent_type_id === Element_OphTrConsent_Type::TYPE_PARENTAL_AGREEMENT_ID) { ?>
                <tr>
                    <td>
                        <label class="highlight inline">
                            <?= \CHtml::hiddenField(
                                $el_class . "[guardian_required]",
                                $element->guardian_required
                            ); ?>

                            <?= \CHtml::checkBox(
                                "",
                                $element->guardian_required === "1",
                                [
                                    "id" => "additional_signatures_guardian_required"
                                ]
                            )?>
                            Parent / Guardian signature
                        </label>
                    </td>
                    <td>
                        <div class="flex">
                            <?= \CHtml::textField(
                                $el_class . "[guardian_name]",
                                $element->guardian_name,
                                [
                                    'id' =>  $el_class . "_guardian_name",
                                    'class' => 'cols-6',
                                    'placeholder' => 'Name of Parnet / Guardian',
                                    'disabled' => true
                                ]
                            ) ?>
                            <label class="fade">Relationship</label>
                            <?= \CHtml::textField(
                                $el_class . "[guardian_relationship]",
                                $element->guardian_relationship,
                                [
                                    'id' =>  $el_class . "_guardian_relationship",
                                    'class' => 'cols-3',
                                    'placeholder' => 'Relationship',
                                    'disabled' => true
                                ]
                            ) ?>
                        </div>

                    </td>
                </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>
</div>
