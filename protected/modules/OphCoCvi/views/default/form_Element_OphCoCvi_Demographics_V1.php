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
if ($element->isNewRecord) {
    $element->nhs_number = \PatientIdentifierHelper::getIdentifierValue($this->patient->globalIdentifier);
}
?>

<div class="element-fields full-width">
    <div class="flex-layout flex-top col-gap">

        <div class="cols-6">

            <table class="cols-full last-left">
                <colgroup>
                    <col class="cols-5">
                    <col class="cols-7">
                </colgroup>
                <tbody>
                <tr>
                    <td>Title and Surname</td>
                    <td>
                        <?= CHtml::activeTextField($element, 'title_surname', ['class' => 'cols-full']); ?>
                    </td>
                </tr>
                <tr>
                    <td>Other names</td>
                    <td><?= CHtml::activeTextField($element, 'other_names', ['class' => 'cols-full']); ?></td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td><?= CHtml::activeTextArea($element, 'address', ["class" => "cols-full"]); ?></td>
                </tr>
                <tr>
                    <td>Post Code</td>
                    <td>
                        <?= CHtml::activeTextField($element, 'postcode', ['class' => 'cols-5', "maxlength" => 4]); ?>
                        &nbsp;
                        <?= CHtml::activeTextField($element, 'postcode_2nd', ['class' => 'cols-5', "maxlength" => 3]); ?>
                    </td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><?= CHtml::activeEmailField($element, 'email', ['class' => 'cols-full']); ?></td>
                </tr>
                <tr>
                    <td>Telephone</td>
                    <td><?= CHtml::activeTelField($element, 'telephone', ['class' => 'cols-full']); ?></td>
                </tr>
                <tr>
                    <td>Date of Birth</td>
                    <td><?= CHtml::activeTelField($element, 'date_of_birth', ['class' => 'cols-full']); ?></td>
                </tr>
                <tr>
                    <td>Gender</td>
                    <td>
                        <?= CHtml::activeDropDownList($element, 'gender_id', CHtml::listData(Gender::model()->findAll(), 'id', 'name'), [
                            'class' => 'cols-full'
                        ]); ?>
                    </td>
                </tr>
                <tr>
                    <td>Ethnic Group</td>
                    <td>
                        <?php
                            $options = EthnicGroup::model()->findAll();
                            echo \CHtml::activeDropDownList($element, 'ethnic_group_id', \CHtml::listData(EthnicGroup::model()->findAll(), 'id', 'name'), [
                            'class' => 'cols-full',
                            'options' => (function (array $options) {
                                $result = [];
                                foreach ($options as $model) {
                                    $result[$model->id] = ['data-describe' => $model->describe_needs];
                                }
                                return $result;
                            })($options)
                            ]); ?>
                    </td>
                </tr>
                </tr>
                <tr style="display:none" id="div_OEModule_OphCoCvi_models_Element_OphCoCvi_Demographics_V1_describe_ethnics">
                    <td><?=$element->getAttributeLabel('describe_ethnics');?></td>
                    <td>
                        <?= CHtml::activeTextArea($element, 'describe_ethnics', ['class' => 'cols-full']); ?>
                    </td>
                </tr>
                </tbody>
            </table>

        </div><!-- left -->

        <div class="cols-6">

            <table class="cols-full last-left">
                <colgroup>
                    <col class="cols-5">
                    <col class="cols-7">
                </colgroup>
                <tbody>
                <tr>
                    <td><?= PatientIdentifierHelper::getIdentifierPrompt($this->patient->globalIdentifier); ?></td>
                    <td><?= CHtml::activeTextField($element, 'nhs_number', ['class' => 'cols-full']); ?></td>
                </tr>
                <tr>
                    <td>GP's Name</td>
                    <td><?= CHtml::activeTextArea($element, 'gp_name', ["class" => "cols-full"]); ?></td>
                </tr>
                <tr>
                    <td>GP's Address</td>
                    <td><?= CHtml::activeTextArea($element, 'gp_address', ["class" => "cols-full", "rows" => 5]); ?></td>
                </tr>
                <tr>
                    <td>GP's Post Code</td>
                    <td>
                        <?= CHtml::activeTextField($element, 'gp_postcode', ['class' => 'cols-5', "maxlength" => 4]); ?>
                        &nbsp;
                        <?= CHtml::activeTextField($element, 'gp_postcode_2nd', ['class' => 'cols-5', "maxlength" => 3]); ?>
                    </td>
                </tr>
                <tr>
                    <td>GP's Telephone</td>
                    <td>
                        <?= CHtml::activeTextField($element, 'gp_telephone', ['class' => 'cols-full']); ?>
                    </td>
                </tr>
                </tbody>
            </table>

            <hr class="divider">
            <div class="row field-row">
                <div class="small-push-6 column-5">
                    <a href="#" id="la-search-toggle" class="button secondary small">
                        Find Local Authority Details
                    </a>
                </div>
            </div>
            <?php $this->renderPartial('localauthority_search', array('hidden' => true)); ?>

            <table class="cols-full last-left">
                <colgroup>
                    <col class="cols-5">
                    <col class="cols-7">
                </colgroup>
                <tbody>
                <tr>
                    <td><?=$element->getAttributeLabel('la_name');?></td>
                    <td><?=\CHtml::activeTextField($element, 'la_name', ['class' => 'cols-full']);?></td>
                </tr>
                <tr>
                    <td><?=$element->getAttributeLabel('la_address');?></td>
                    <td><?=\CHtml::activeTextArea($element, 'la_address', ['class' => 'cols-full', 'rows' => 5]);?></td>
                </tr>
                <tr>
                    <td><?=$element->getAttributeLabel('la_postcode');?></td>
                    <td>
                        <?= CHtml::activeTextField($element, 'la_postcode', ['class' => 'cols-5', "maxlength" => 4]); ?>
                        &nbsp;
                        <?= CHtml::activeTextField($element, 'la_postcode_2nd', ['class' => 'cols-5', "maxlength" => 3]); ?>
                    </td>
                </tr>
                <tr>
                    <td><?=$element->getAttributeLabel('la_telephone');?></td>
                    <td><?=\CHtml::activeTelField($element, 'la_telephone', ['class' => 'cols-full']);?></td>
                </tr>
                <tr>
                    <td><?=$element->getAttributeLabel('la_email');?></td>
                    <td><?=\CHtml::activeEmailField($element, 'la_email', ['class' => 'cols-full']);?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
