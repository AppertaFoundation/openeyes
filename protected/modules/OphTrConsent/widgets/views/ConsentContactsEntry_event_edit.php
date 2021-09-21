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


if (!isset($values)) {
    if (isset($entry) && isset($entry->contact)) {
        $contact = $entry->contact;
    }
    $values = array(
        'id' => $contact->id,
        'label' => $contact->label ? $contact->label->name : "",
        'full_name' => $contact->getFullName(),
        'authorised_decision_id' => $entry->authorised_decision_id,
        'considered_decision_id' => $entry->considered_decision_id
    );
}

?>
<tr data-key="<?= $row_count; ?>">
    <td><?= $values['full_name'] ?></td>
    <td>
        <div class="row flex">
            <div class="cols-5">I have been authorised to make decisions about the procedure in question </div>
            <div>
                <fieldset>
                    <?php if (isset($values['authorised_decision_id']) && $values['authorised_decision_id'] === (string)PatientAttorneyDeputyContact::$NOT_PRESENT) { ?>
                        <label class="inline highlight">
                            <?= \CHtml::radioButton($field_prefix . '[authorised_decision_id]',
                                $values['authorised_decision_id'] === (string)PatientAttorneyDeputyContact::$PRESENT,
                                array('value' => PatientAttorneyDeputyContact::$PRESENT)); ?>
                             under a Lasting Power of Attorney.
                        </label>
                        <label class="inline highlight">
                            <?= \CHtml::radioButton($field_prefix . '[authorised_decision_id]',
                                $values['authorised_decision_id'] === (string)PatientAttorneyDeputyContact::$NOT_PRESENT,
                                array('value' => PatientAttorneyDeputyContact::$NOT_PRESENT)); ?>
                             as a Court Appointed Deputy.
                        </label>
                    <?php } elseif (isset($values['authorised_decision_id'])) { ?>
                        <label class="inline highlight">
                            <?= \CHtml::radioButton(
                                $field_prefix . '[authorised_decision_id]',
                                $values['authorised_decision_id'] === (string)PatientAttorneyDeputyContact::$PRESENT,
                                [
                                    'value' => PatientAttorneyDeputyContact::$PRESENT,
                                    'id' => "{$field_prefix}_authorised_decision_id_{$entry::$PRESENT}"]
                            ); ?>
                             under a Lasting Power of Attorney.
                        </label>
                        <label class="inline highlight">
                            <?= \CHtml::radioButton(
                                $field_prefix . '[authorised_decision_id]',
                                $values['authorised_decision_id'] === (string)$entry::$NOT_PRESENT,
                                [
                                    'value' => $entry::$NOT_PRESENT,
                                    'id' => "{$field_prefix}_authorised_decision_id_{$entry::$NOT_PRESENT}"
                                ]
                            ); ?>
                             as a Court Appointed Deputy.
                        </label>
                    <?php } else { ?>
                        <label class="inline highlight">
                            <?= \CHtml::radioButton(
                                $field_prefix . '[authorised_decision_id]',
                                1,
                                [
                                    'value' => PatientAttorneyDeputyContact::$PRESENT,
                                    'id' => "{$field_prefix}_authorised_decision_id_{$entry::$PRESENT}"]
                            ); ?>
                             under a Lasting Power of Attorney.
                        </label>
                        <label class="inline highlight">
                            <?= \CHtml::radioButton(
                                $field_prefix . '[authorised_decision_id]',
                                0,
                                [
                                    'value' => $entry::$NOT_PRESENT,
                                    'id' => "{$field_prefix}_authorised_decision_id_{$entry::$NOT_PRESENT}"
                                ]
                            ); ?>
                             as a Court Appointed Deputy.
                        </label>
                    <?php } ?>
                </fieldset>
            </div>
        </div>
        <div class="row flex">
            <div class="cols-5">I have considered the relevant circumstances relating to the decision in question and believe the procedure to be in the patient's best interests </div>
            <div>
                <fieldset>
                <?php if (isset($values['considered_decision_id']) && $values['considered_decision_id'] === (string)PatientAttorneyDeputyContact::$NOT_PRESENT) { ?>
                        <label class="inline highlight">
                            <?= \CHtml::radioButton($field_prefix . '[considered_decision_id]',
                                $values['considered_decision_id'] === (string)PatientAttorneyDeputyContact::$YES,
                                array('value' => PatientAttorneyDeputyContact::$YES)); ?>
                             Yes
                        </label>
                        <label class="inline highlight">
                            <?= \CHtml::radioButton($field_prefix . '[considered_decision_id]',
                                $values['considered_decision_id'] === (string)PatientAttorneyDeputyContact::$NO,
                                array('value' => PatientAttorneyDeputyContact::$NO)); ?>
                             No
                        </label>
                <?php } elseif (isset($values['considered_decision_id'])) { ?>
                        <label class="inline highlight">
                            <?= \CHtml::radioButton(
                                $field_prefix . '[considered_decision_id]',
                                $values['considered_decision_id'] === (string)PatientAttorneyDeputyContact::$YES,
                                [
                                    'value' => PatientAttorneyDeputyContact::$YES,
                                    'id' => "{$field_prefix}_considered_decision_id_{$entry::$YES}"]
                            ); ?>
                             Yes
                        </label>
                        <label class="inline highlight">
                            <?= \CHtml::radioButton(
                                $field_prefix . '[considered_decision_id]',
                                $values['considered_decision_id'] === (string)$entry::$NO,
                                [
                                    'value' => $entry::$NO,
                                    'id' => "{$field_prefix}_considered_decision_id_{$entry::$NO}"
                                ]
                            ); ?>
                             No
                        </label>
                <?php } else { ?>
                        <label class="inline highlight">
                            <?= \CHtml::radioButton(
                                $field_prefix . '[considered_decision_id]',
                                1,
                                [
                                    'value' => PatientAttorneyDeputyContact::$YES,
                                    'id' => "{$field_prefix}_considered_decision_id_{$entry::$YES}"]
                            ); ?>
                             Yes
                        </label>
                        <label class="inline highlight">
                            <?= \CHtml::radioButton(
                                $field_prefix . '[considered_decision_id]',
                                0,
                                [
                                    'value' => $entry::$NO,
                                    'id' => "{$field_prefix}_considered_decision_id_{$entry::$NO}"
                                ]
                            ); ?>
                             No
                        </label>
                <?php } ?>
                </fieldset>
            </div>
        </div>
    </td>
    <?php if (isset($removable) && $removable) { ?>
        <input type="hidden" name="<?= $model_name ?>[contact_id][]" value="<?= $values['id'] ?>"/>
    <td>
        <i class="oe-i trash"></i>
    </td>
    <?php } ?>
</tr>

