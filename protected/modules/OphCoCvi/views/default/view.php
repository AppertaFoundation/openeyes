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
$event_info = $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_EventInfo');
$patient_sign = $event_info->getSignatureByType(\BaseSignature::TYPE_PATIENT);

$selectOptions = [];
if ($this->checkPrintAccess()) {
    $this->event_actions[] = EventAction::printButton();
    $this->event_actions[] = EventAction::button('Print Visually Impaired', 'visually_impaired', null, array('class' => 'small button'));
    $selectOptions['print_visually_impaired'] = 'Print Visually Impaired';
}

if ($this->checkLabelPrintAccess()) {
    // OE-11960 for missing buttons on 'Print Labels' popup
    $this->event_actions[] = EventAction::button('Print Labels', 'print_labels', null, array('class' => 'small button'));
    $selectOptions['print_labels'] = 'Print Labels';
}

if ($this->getManager()->isIssued($this->event)) {
    $this->event_actions[] = EventAction::button('Print consent page', 'print_consent', array('level' => 'secondary'), array('type' => 'button', 'id' => 'et_print_consent', 'class' => 'button small',));
}

// To be fixed in: OE-11959 - CVI - "Print Consent Page" feature is missing
$this->event_actions[] = EventAction::button('Print Information Sheet', null, array('level' => 'secondary'), array('type' => 'button', 'id' => 'et_print_info_sheet', 'class' => 'button small'));
$selectOptions['et_print_info_sheet'] = 'Print Information Sheet';

$this->event_actions[] = EventAction::dropdownToButton('', 'select_action', $selectOptions, null);

if ($this->canIssue()) {
    $this->event_actions[] = EventAction::link('Issue', '/OphCoCvi/default/issue/' . $this->event->id, null, array('class' => 'small button secondary'));
}

$this->beginContent('//patient/event_container');
?>

<?php if ($this->event->delete_pending) { ?>
    <div class="alert-box alert with-icon">
        This event is pending deletion and has been locked.
    </div>
<?php } ?>

<?php if ($this->getManager()->isIssued($this->event)) : ?>
    <div class="alert-box success">
        <u>Delivery status:</u><br/>
        Delivery to GP: <i><?=CHtml::encode($this->getManager()->getGPDeliveryStatus($this->event))?></i><br/>
        Delivery to Local Authority: <i><?=CHtml::encode($this->getManager()->getLADeliveryStatus($this->event))?></i><br/>
        Delivery to RCOP: <i><?=CHtml::encode($this->getManager()->getRCOPDeliveryStatus($this->event))?></i>
    </div>
<?php endif; ?>

<?php $this->renderOpenElements($this->action->id) ?>

<?php $this->endContent() ?>
<script>
$(document).ready(function(){
    $('#et_select_action').on('change', function(){
        $('.event-header').find('button').hide();
        var option = $(this).val();
        $('#'+option).show();

        $(this).val("");
    });
});
</script>
