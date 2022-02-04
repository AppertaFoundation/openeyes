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
global $reason_id;
global $reason_other_text;
/**
 * @var $errors
 */
?>

<?php
$form_id = 'prescription-update';
$settings = new SettingMetadata();
$form_format = $settings->getSetting('prescription_form_format');
$this->beginContent('//patient/event_container', array('no_face'=>true , 'form_id' => $form_id));

$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => $form_id,
    'enableAjaxValidation' => false,
));

// Event actions
$this->event_actions[] = EventAction::button(
    'Save draft',
    'savedraft',
    array('level' => 'primary'),
    array('id' => 'et_save_draft', 'class' => 'button small', 'form' => $form_id)
);
$this->event_actions[] = EventAction::button(
    'Save',
    'save',
    array('level' => 'secondary'),
    array('id' => 'et_save', 'class' => 'button small', 'form' => $form_id)
);
if ($cocoa_api = \Yii::app()->moduleAPI->get("OphInCocoa")) {
    if ($event_button =  $cocoa_api->prescriptionEventAction($form_id)) {
        $this->event_actions[] = $event_button;
    }
}
if ($this->checkPrintAccess()) {
    $this->event_actions[] = EventAction::button(
        'Save and print',
        'saveprint',
        array('level' => 'secondary'),
        array('id' => 'et_save_print', 'class' => 'button small', 'form' => $form_id)
    );
    $this->event_actions[] = EventAction::button(
        "Save and print $form_format",
        'saveprintform',
        array('level' => 'secondary'),
        array(
            'id' => 'et_save_print_form',
            'class' => 'button small',
            'style' => 'display: none;',
            'form' => $form_id,
            'data-enabled' => $settings->getSetting('enable_prescription_overprint')
        )
    );
}
?>

<input type="hidden" id="Element_OphDrPrescription_Details_edit_reason_id"
       name="Element_OphDrPrescription_Details[edit_reason_id]" value="<?php echo htmlentities($reason_id); ?>" />
<input type="hidden" id="Element_OphDrPrescription_Details_edit_reason_other_text"
       name="Element_OphDrPrescription_Details[edit_reason_other]" value="<?php echo htmlentities($reason_other_text); ?>" />

<?php $this->displayErrors($errors)?>
<?php $this->renderOpenElements($this->action->id, $form); ?>
<?php $this->renderOptionalElements($this->action->id, $form); ?>
<?php $this->displayErrors($errors, true)?>

<?php $this->endWidget(); ?>

<?php $this->endContent();?>
