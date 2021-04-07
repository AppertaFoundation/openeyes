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
$model_name = CHtml::modelName($model);
?>
<section class="<?php echo $type; ?> box patient-info js-toggle-container element">
    <h3 class="box-title">Personal Details:</h3>
    <?= \CHtml::activeHiddenField($model, "{$type}_id", array('class' => 'id-input')); ?>
    <div class="js-toggle-body">
        <table class="standard">
            <colgroup>
                <col class="cols-5">
                <col class="cols-6">
                <col class="cols-1">
            </colgroup>
            <tbody>
            <?php $patient_identifier_types_used = []; ?>
            <?php foreach ($patient_identifier_types as $patient_identifier_type) {
                $patient_identifier_title = $patient_identifier_type->long_title ?? $patient_identifier_type->short_title;
                ?>
                <tr>
                    <!--                Parameterized for CERA-519-->
                    <td> <?= $patient_identifier_title; ?>:
                    </td>
                    <td>
                        <span class="data-value patient_identifiers_<?= str_replace(" ", "_", strtolower($patient_identifier_title)); ?>"></span>
                        <?php if (!array_key_exists($patient_identifier_type->usage_type, $patient_identifier_types_used)) {
                            $patient_identifier_types_used[$patient_identifier_type->usage_type] = true; ?>
                            <input class="patient_identifiers_<?= str_replace(" ", "_", strtolower($patient_identifier_title)); ?>-input"
                                   name="<?= $model_name; ?>[<?= $type . '_' . strtolower($patient_identifier_type->usage_type) . '_identifier_value'; ?>]"
                                   id="<?= $model_name . '_' . $type . '_' . strtolower($patient_identifier_type->usage_type) . '_identifier_value'; ?>"
                                   type="hidden" value="">
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td>
                    First name(s):
                </td>
                <td>
                    <div class="data-value first_name"></div>
                </td>
            </tr>
            <tr>
                <td>
                    Last name:
                </td>
                <td>
                    <div class="data-value last_name"></div>
                </td>
            </tr>
            <tr>
                <td> Date of Birth:</td>
                <td>
                    <div class="data-value dob"></div>
                    <?= \CHtml::activeHiddenField($model, "{$type}_dob", array('class' => 'dob-input')); ?>
                </td>
            </tr>
            <tr>
                <td> Gender:</td>
                <td>
                    <div class="data-value gender"></div>
                    <?= \CHtml::activeHiddenField($model, "{$type}_gender", array('class' => 'genderletter-input')); ?>
                </td>
            </tr>
            </tbody>
        </table>

    </div>
</section>

<?php if (!$model->isNewRecord) : ?>
    <?php echo $this->getEpisodesHTML($model->{"{$type}Patient"}); ?>
    <?php echo $this->getGeneticsHTML($model->{"{$type}Patient"}); ?>
<?php endif; ?>
