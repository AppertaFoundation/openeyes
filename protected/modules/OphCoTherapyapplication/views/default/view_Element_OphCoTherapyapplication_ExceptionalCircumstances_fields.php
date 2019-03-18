<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 *  You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<table class="label-value no-left">
    <colgroup>
        <col class="cols-5">
    </colgroup>
    <tbody>
        <tr>
            <td>
                <div class="data-label">
                    <?php echo $element->getAttributeLabel($side . '_standard_intervention_exists') ?>:
                </div>
            </td>
            <td>
                <?php echo $element->{$side . '_standard_intervention_exists'} ? 'Yes' : 'No' ?>
            </td>
        </tr>

        <?php if ($element->{$side . '_standard_intervention_exists'}) { ?>
            <tr>
                <td>
                    <div class="data-label">
                        <?php echo $element->getAttributeLabel($side . '_standard_intervention_id') ?>:
                    </div>
                </td>
                <td>
                    <?php echo $element->{$side . '_standard_intervention'}->name ?>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="data-label">
                        <?php echo $element->getAttributeLabel($side . '_standard_previous') ?>:
                    </div>
                </td>
                <td>
                    <?php echo $element->{$side . '_standard_previous'} ? 'Yes' : 'No' ?>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="data-label">
                        <?php echo $element->getAttributeLabel($side . '_intervention_id') ?>:
                    </div>
                </td>
                <td>
                    <?php echo $element->{$side . '_intervention'}->name ?>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="data-label">
                        <?php echo $element->getAttributeLabel($side . '_description') ?>:
                    </div>
                </td>
                <td>
                    <?php echo Yii::app()->format->Ntext($element->{$side . '_description'}) ?>
                </td>
            </tr>

            <?php if ($element->needDeviationReasonForSide($side)) { ?>
                <tr>
                    <td>
                        <div class="data-label">
                            <?php echo $element->getAttributeLabel($side . '_deviationreasons') ?>:
                        </div>
                    </td>
                    <td>
                        <ul>
                            <?php
                            foreach ($element->{$side . '_deviationreasons'} as $dr) {
                                echo '<li>' . $dr->name . '</li>';
                            }
                            ?>
                        </ul>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td>
                    <div class="data-label">
                         <?php echo $element->getAttributeLabel($side . '_condition_rare') ?>:
                    </div>
                </td>
                <td>
                    <?php echo $element->{$side . '_condition_rare'} ? 'Yes' : 'No' ?>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="data-label">
                        <?php echo $element->getAttributeLabel($side . '_incidence') ?>:
                    </div>
                </td>
                <td>
            <?php echo Yii::app()->format->Ntext($element->{$side . '_incidence'}) ?>
                </td>
            </tr>
        <?php } ?>

        <tr>
            <td>
                <div class="data-label">
                    <?php echo $element->getAttributeLabel($side . '_patient_different') ?>:
                </div>
            </td>
            <td>
                <?php echo Yii::app()->format->Ntext($element->{$side . '_patient_different'}) ?>
            </td>
        </tr>

        <tr>
            <td>
                <div class="data-label">
                    <?php echo $element->getAttributeLabel($side . '_patient_gain') ?>:
                </div>
            </td>
            <td>
                <?php echo Yii::app()->format->Ntext($element->{$side . '_patient_gain'}) ?>
            </td>
        </tr>

        <?php if ($element->{$side . '_previnterventions'}) { ?>
        <tr>
            <td>
                <div class="data-label">
                    <?php echo $element->getAttributeLabel($side . '_previnterventions') ?>:
                </div>
            </td>
            <td>
                <?php
                foreach ($element->{$side . '_previnterventions'} as $previntervention) {
                    $this->renderPartial('view_OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention', array(
                        'pastintervention' => $previntervention,
                    ));
                }
                ?>
            </td>
        </tr>
        <?php } ?>

        <?php if ($element->{$side . '_relevantinterventions'}) { ?>
        <tr>
            <td>
                <div class="data-label">
                <?php echo $element->getAttributeLabel($side . '_relevantinterventions') ?>:
                </div>
            </td>
            <td>
                <?php
                foreach ($element->{$side . '_relevantinterventions'} as $relevantintervention) {
                    $this->renderPartial('view_OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention', array(
                        'pastintervention' => $relevantintervention,
                    ));
                }
                ?>
            </td>
        </tr>
        <?php } ?>

        <tr>
            <td>
                <div class="data-label">
                <?php echo $element->getAttributeLabel($side . '_patient_factors') ?>:
                </div>
            </td>
            <td>
                <?php echo $element->{$side . '_patient_factors'} ? 'Yes' : 'No' ?>
            </td>
        </tr>

        <?php if ($element->{$side . '_patient_factors'}) { ?>
        <tr>
            <td>
                <div class="data-label">
                    <?php echo $element->getAttributeLabel($side . '_patient_factor_details') ?>:
                </div>
            </td>
            <td>
                <?php echo Yii::app()->format->Ntext($element->{$side . '_patient_factor_details'}) ?>
            </td>
        </tr>
        <?php } ?>

        <tr>
            <td>
                <div class="data-label">
                <?php echo $element->getAttributeLabel($side . '_patient_expectations') ?>:
                </div>
            </td>
            <td>
                <?php echo Yii::app()->format->Ntext($element->{$side . '_patient_expectations'}) ?>
            </td>
        </tr>

        <tr>
            <td>
                <div class="data-label">
                <?php echo $element->getAttributeLabel($side . '_start_period_id') ?>:
                </div>
            </td>
            <td>
                <?php echo $element->{$side . '_start_period'}->name ?>
            </td>
        </tr>

        <?php if ($element->{$side . '_start_period'}->urgent) { ?>
            <tr>
                <td>
                    <div class="data-label">
                        <?php echo $element->getAttributeLabel($side . '_urgency_reason') ?>:
                    </div>
                </td>
                <td>
                    <?php echo Yii::app()->format->Ntext($element->{$side . '_urgency_reason'}) ?>
                </td>
            </tr>
        <?php } ?>

        <?php if ($element->{$side . '_filecollections'} && (isset($status) && ($status != OphCoTherapyapplication_Processor::STATUS_SENT))) { ?>
            <tr>
                <td>
                    <div class="data-label">
                        <?php echo $element->getAttributeLabel($side . '_filecollections') ?>:
                    </div>
                </td>
                <td>
                    <ul style="display: inline-block">
                        <?php foreach ($element->{$side . '_filecollections'} as $filecoll) { ?>
                            <li><a href="<?php echo $filecoll->getDownloadURL() ?>"><?php echo $filecoll->name ?></a></li>
                    <?php } ?>
                    </ul>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>