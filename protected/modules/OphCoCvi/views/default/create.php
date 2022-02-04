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
$form_id = 'create-form';
$this->beginContent('//patient/event_container', array('no_face'=>false , 'form_id' => $form_id));
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => $form_id,
    'enableAjaxValidation' => false,
    'layoutColumns' => array(
        'label' => 4,
        'field' => 8
    )
));

$this->event_actions[] = EventAction::button('Print empty consent page', null, array('level' => 'secondary'), array('type' => 'button', 'id' => 'et_print_empty_consent', 'class' => 'button small',));

$this->renderPartial('event_actions', array('form_id' => 'create-form'));
?>
<?php
if (!isset($this->patient->practice) || !isset($this->patient->practice->contact->address)) { ?>
    <div id="no-practice-address" class="alert-box alert with-icon">
        Warning: Patient has no GP practice address
    </div>
<?php } ?>
<?php $this->displayErrors($errors) ?>
<?php $this->renderPartial('//patient/event_elements', array('form' => $form)); ?>

<?php $this->displayErrors($errors, true) ?>

<?php $this->endWidget() ?>

<?php $this->endContent();
