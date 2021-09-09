<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<?php
$this->beginContent('//patient/event_container', array()); ?>

<?php
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => 'update-form',
    'enableAjaxValidation' => false,
    'layoutColumns' => array(
        'label' => 2,
        'field' => 10
    )
));
$elementy_type_id = null;

$this->event_actions[] = EventAction::button('Print Consent Page', null, array('level' => 'secondary'), array('type' => 'button', 'id' => 'et_print_consent', 'class' => 'button small',));

// Event actions
$this->renderPartial('event_actions', array('form_id' => 'update-form'));

?>

<?php $this->displayErrors($errors) ?>
<?php $this->renderPartial('//patient/event_elements', array('form' => $form)); ?>
<?php $this->displayErrors($errors, true) ?>

<?php $this->endWidget() ?>

<?php $this->endContent();
