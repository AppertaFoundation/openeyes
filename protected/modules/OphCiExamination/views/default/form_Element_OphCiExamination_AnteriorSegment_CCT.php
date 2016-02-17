<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php $methods = CHtml::listData(\OEModule\OphCiExamination\models\OphCiExamination_AnteriorSegment_CCT_Method::model()->activeOrPk(array($element->right_method_id, $element->left_method_id))->findAll(array('order' => 'display_order')), 'id', 'name') ?>
<div class="element-eyes sub-element-fields">
	<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField'))?>
	<div class="element-eye right-eye column left side<?php if (!$element->hasRight()) {
    ?> inactive<?php 
}?>" data-side="right">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<div class="row collapse field-row">
				<div class="large-2 column">
					<?php echo $form->textField($element, 'right_value', array('autocomplete'=>Yii::app()->params['html_autocomplete'], 'nowrapper' => true, 'class' => 'cct_value')) ?>
				</div>
				<div class="large-10 column">
					<div class="postfix align field-info">
						&micro;m, using <?php echo $form->dropDownList($element, 'right_method_id', $methods, array('nowrapper' => true, 'class'=>'inline')) ?>
					</div>
				</div>
			</div>
		</div>
		<div class="inactive-form">
			<div class="add-side">
				<a href="#">
					Add right side <span class="icon-add-side"></span>
				</a>
			</div>
		</div>
	</div>
	<div class="element-eye left-eye column right side<?php if (!$element->hasLeft()) {
    ?> inactive<?php 
}?>" data-side="left">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<div class="row collapse field-row">
				<div class="large-2 column">
					<?php echo $form->textField($element, 'left_value', array('autocomplete'=>Yii::app()->params['html_autocomplete'], 'nowrapper' => true, 'class' => 'cct_value')) ?>
				</div>
				<div class="large-10 column">
					<div class="postfix align field-info">
						&micro;m, using <?php echo $form->dropDownList($element, 'left_method_id', $methods, array('nowrapper' => true, 'class'=>'inline')) ?>
					</div>
				</div>
			</div>
		</div>
		<div class="inactive-form">
			<div class="add-side">
				<a href="#">
					Add left side <span class="icon-add-side"></span>
				</a>
			</div>
		</div>
	</div>
</div>
