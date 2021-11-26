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
if ($this->checkPrintAccess()) {
    $this->event_actions[] = EventAction::printButton();
    $this->event_actions[] = EventAction::button('Print for visually impaired', 'print_va', array(), array('class' => 'button small'));
}

$event = $this->event;
$withdrawn = false;
$signature = null;
$has_opnote = null;
$has_withdrawal = null;

if ($withdrawal = $event->getElementByClass(Element_OphTrConsent_Withdrawal::class)) {
    $withdrawn = $withdrawal->withdrawn;
    $signature = $withdrawal->signature_id;
}

$existing_withdrawal_criteria = new CDbCriteria();
$existing_withdrawal_criteria->with = ['event'];
$existing_withdrawal_criteria->compare('event.deleted', 0);
$existing_withdrawal_criteria->compare('t.event_id', $this->event->id);
$has_withdrawal = Element_OphTrConsent_Withdrawal::model()->find($existing_withdrawal_criteria);

$existing_consent_criteria = new CDbCriteria();
$existing_consent_criteria->with = ['event'];
$existing_consent_criteria->compare('event.deleted', 0);
$existing_consent_criteria->compare('t.event_id', $this->event->id);
$has_consent = Element_OphTrConsent_Procedure::model()->find($existing_consent_criteria);

if ($has_consent && $has_consent->booking_event_id !== null) {
    $existing_opnote_criteria = new CDbCriteria();
    $existing_opnote_criteria->with = ['event'];
    $existing_opnote_criteria->compare('event.deleted', 0);
    $existing_opnote_criteria->compare('t.booking_event_id', $has_consent->booking_event_id);
    $has_opnote = Element_OphTrOperationnote_ProcedureList::model()->find($existing_opnote_criteria);
}

$confirmed = false;
$confirmSignature = null;

if ($confirm = $event->getElementByClass(Element_OphTrConsent_Confirm::class)) {
    $confirmed = $confirm->confirmed;
    $confirmSignature = $confirm->signature_id;
}

$existing_confirm_criteria = new CDbCriteria();
$existing_confirm_criteria->with = ['event'];
$existing_confirm_criteria->compare('event.deleted', 0);
$existing_confirm_criteria->compare('t.event_id', $this->event->id);
$has_confirm = Element_OphTrConsent_Confirm::model()->find($existing_confirm_criteria);

$consent_type_criteria = new CDbCriteria();
$consent_type_criteria->with = ['type'];
$consent_type_criteria->compare('t.event_id', $this->event->id);
$consent_type = Element_OphTrConsent_Type::model()->find($consent_type_criteria);

if ($signature === null && $has_withdrawal === null && $has_confirm === null &&
        (
            strcmp($consent_type->type->name, "1. Patient agreement to investigation or treatment for adults with mental capacity to give valid consent") == 0 ||
            strcmp($consent_type->type->name, "1. Patient agreement to investigation or treatment") == 0 ||
            strcmp($consent_type->type->name, "2. Parental agreement to investigation or treatment for a child or young person") == 0 ||
            strcmp($consent_type->type->name, "3. Patient/parental agreement to investigation or treatment (procedures where consciousness not impaired)") == 0
        )
    ) {
    $this->event_actions[] = EventAction::button(
        'Confirm consent',
        'confirm',
        array(),
        array('type' => 'button', 'class' => 'button blue warning small js-add-confirm', 'data-type' => 'confirm')
    );
}

$et_confirm = ElementType::model()->find("class_name = :class_name", array(":class_name" => Element_OphTrConsent_Confirm::class));
if ($et_confirm) {
    $confirm_et_id = $et_confirm->id;
    echo "<input type='hidden' id='confirm_et_id' value='" . CHtml::encode($confirm_et_id) . "' />";
}

if ($signature === null && $has_opnote === null && $has_withdrawal === null) {
    $this->event_actions[] = EventAction::button(
        'Patient withdraws consent',
        'withdraw',
        array(),
        array('type' => 'button', 'class' => 'button red warning small js-add-withdrawal', 'data-type' => 'withdraw')
    );
}

$et = ElementType::model()->find("class_name = :class_name", array(":class_name" => Element_OphTrConsent_Withdrawal::class));
if ($et) {
    $withdrawal_et_id = $et->id;
    echo "<input type='hidden' id='withdraw_et_id' value='" . CHtml::encode($withdrawal_et_id) . "' />";
}

?>
<?php $this->beginContent('//patient/event_container', array('no_face'=>true));?>

    <?php if ($this->event->delete_pending) {?>
        <div class="alert-box alert with-icon">
            This event is pending deletion and has been locked.
        </div>
    <?php } elseif (Element_OphTrConsent_Type::model()->find('event_id=?', array($this->event->id))->draft) {?>
        <div class="alert-box alert with-icon">
            This consent form is a draft and can still be edited
        </div>
    <?php }?>

    <?php if ($withdrawn) : ?>
        <div class="alert-box alert with-icon">
            This consent form has been withdrawn by the patient.
        </div>
    <?php endif; ?>

    <?php $this->renderOpenElements($this->action->id); ?>

    <?php // The "print" value is set by the controller and comes from the user session ?>
    <input type="hidden" name="OphTrConsent_print" id="OphTrConsent_print" value="<?php echo $print;?>" />
    <iframe id="print_iframe" name="print_iframe" style="display: none;"></iframe>
<?php $this->renderPartial('//default/delete');?>
<?php $this->endContent(); ?>