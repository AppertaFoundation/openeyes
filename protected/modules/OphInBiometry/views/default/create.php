<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2013
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
$assetManager = Yii::app()->getAssetManager();
$assetManager->registerScriptFile('js/libs/uri-1.10.2.js');
$form_id = 'create-form';
$this->beginContent('//patient/event_container', array('no_face'=>false , 'form_id' => $form_id));
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => $form_id,
    'enableAjaxValidation' => false,
    'layoutColumns' => array(
        'label' => 2,
        'field' => 10,
    ),
));
$isManualEntryDisabled = 0;
if ($this->isManualEntryDisabled()) {
    $isManualEntryDisabled = 1;
}
echo '<input type="hidden" id="show_disable_manual_warning" value='.$isManualEntryDisabled.'>';
$this->event_actions[] = EventAction::button('Save', 'save', array('level' => 'save'), array('form' => $form_id));
$this->displayErrors($errors);
$this->renderPartial('//patient/event_elements', array(
    'form' => $form,
    'disableOptionalElementActions' => true,
));

$this->renderPartial('_va_view', ['action' => 'view']);
$this->displayErrors($errors, true);
$this->endWidget();
$this->endContent();
