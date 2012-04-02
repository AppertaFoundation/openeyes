<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<h4 class="elementTypeName"><?php echo $element->elementType->name ?></h4>

<?php echo $form->radioButtons($element, 'eye_id', 'eye', $this->selected_eye);?>
<?php
$form->widget('application.widgets.ProcedureSelection',array(
	'element' => $element,
	'selected_procedures' => $this->selected_procedures,
	'newRecord' => true,
	'last' => true
));
?>
<?php echo $form->radioButtons($element, 'anaesthetic_type_id', 'anaesthetic_type');?>
<?php echo $form->radioButtons($element, 'anaesthetist_id', 'anaesthetist')?>
<?php echo $form->radioButtons($element, 'anaesthetic_delivery_id', 'anaesthetic_delivery')?>
<?php echo $form->textArea($element, 'anaesthetic_comment', array('rows' => 6, 'cols' => 80))?>
<?php echo $form->dropDownList($element, 'surgeon_id', CHtml::listData($this->surgeons, 'id', 'FullName'),array('empty'=>'- Please select -')); ?>
<?php echo $form->dropDownList($element, 'assistant_id', CHtml::listData($this->surgeons, 'id', 'FullName'),array('empty'=>'- None -')); ?>
<?php echo $form->dropDownList($element, 'supervising_surgeon_id', CHtml::listData($this->surgeons, 'id', 'FullName'),array('empty'=>'- None -')); ?>
