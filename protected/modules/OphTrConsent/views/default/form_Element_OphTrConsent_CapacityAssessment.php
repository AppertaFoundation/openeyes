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
?>
<?php
    $model_name = CHtml::modelName($element);
?>
<div class="element-fields full-width">
    <div class="flex-t">
        <div class="cols-6">

            <div class="row large-text">
                The patient lacks capacity to give or withhold consent to this procedure or course of treatment because of:
            </div>

            <div class="row" id="div_OEModule_OphTrConsent_models_Element_OphTrConsent_CapacityAssessment_lackOfCapacityReasons">
                <ul class="row-list">
                    <li>
                        <label>An impairment of, or disturbance in the functioning of, the mind or brain; and/or</label>
                    </li>
                    <li>
                        <?php
                        echo $form->checkBoxList(
                            $element,
                            "lackOfCapacityReasonIds",
                            CHtml::listData(\OEModule\OphTrConsent\models\OphTrConsent_LackOfCapacityReason::model()->findAll(), "id", "label"),
                            array(
                                'labelOptions' => array("style" => "display: inline-block", "class" => "js-lcr-id highlight", 'data-test' => 'consent-lack-of-capacity-reasons'),
                            )
                        ); ?>
                    </li>
                </ul>
            </div>

        </div>

        <div class="cols-5">
            <div class="small-row">
                Further details
            </div>
            <div class="small-row">
                <div class="small-row">
                    <?= CHtml::encode($element->getAttributeLabel("how_judgement_was_made")).':'?>
                </div>
                <?php echo $form->textArea(
                    $element,
                    "how_judgement_was_made",
                    array('nowrapper' => true),
                    false,
                    array('class' => 'cols-full', 'rows' => '1', 'placeholder' => "Details of judgements"),
                );?>
            </div>
            <div class="small-row">
                <div class="small-row">
                    <?= CHtml::encode($element->getAttributeLabel("evidence")).':'?>
                </div>
                <?php echo $form->textArea(
                    $element,
                    "evidence",
                    array('nowrapper' => true),
                    false,
                    array(
                        'class' => 'cols-full',
                        'rows' => '1',
                        'placeholder' => "Details of evidence"
                    )
                ); ?>
            </div>
            <div class="small-row">
                <div class="small-row">
                    <?= CHtml::encode($element->getAttributeLabel("attempts_to_assist")).':'?>
                </div>
                <?php echo $form->textArea(
                    $element,
                    "attempts_to_assist",
                    array('nowrapper' => true),
                    false,
                    array(
                        'class' => 'cols-full',
                        'rows' => '1',
                        'placeholder' => "Details of asssistance"
                    )
                ); ?>
            </div>
            <div class="small-row">
                <div class="small-row">
                    <?= CHtml::encode($element->getAttributeLabel("basis_of_decision")).':'?>
                </div>
                <?php echo $form->textArea(
                    $element,
                    "basis_of_decision",
                    array('nowrapper' => true),
                    false,
                    array(
                        'class' => 'cols-full',
                        'rows' => '1',
                        'placeholder' => "Details of lack of capacity"
                    )
                ); ?>
            </div>
        </div>
    </div>
</div>