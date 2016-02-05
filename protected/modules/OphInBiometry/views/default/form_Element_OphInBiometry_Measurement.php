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

<?php

	if($element->event != null &&  $element->event->id > 0) {
		$iolRefValues = Element_OphInBiometry_IolRefValues::Model()->findAllByAttributes(
			array(
				'event_id' => $element->event->id
			));
	}
	else
	{
		$iolRefValues = array();
	}
?>

<section class="element <?php echo $element->elementType->class_name?>"
	data-element-type-id="<?php echo $element->elementType->id?>"
	data-element-type-class="<?php echo $element->elementType->class_name?>"
	data-element-type-name="<?php echo $element->elementType->name?>"
	data-element-display-order="<?php echo $element->elementType->display_order?>">
	<div class="element-fields element-eyes row">
		<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
		<div id="right-eye-lens" class="element-eye right-eye top-pad left side column  <?php if (!$element->hasRight()) {
    ?> inactive<?php 
} ?>" onClick="switchSides($(this));" data-side="right">
			<div class="element-header right-side">
				<h4><b>RIGHT</b></h4>
			</div>
			<div class="active-form">
				<a href="#" class="icon-remove-side remove-side">Remove side</a>
				<?php $this->renderPartial('form_Element_OphInBiometry_Measurement_fields', array('side' => 'right', 'element' => $element, 'form' => $form, 'data' => $data, 'measurementInput' => $iolRefValues)); ?>
			</div>
			<div class="inactive-form">
				<div class="add-side">
					<a href="#">
						Add Right side <span class="icon-add-side"></span>
					</a>
				</div>
			</div>
		</div>
		<div id="left-eye-lens" class="element-eye left-eye top-pad right side column <?php if (!$element->hasLeft()) {
    ?> inactive<?php 
} ?>" onClick="switchSides($(this));" data-side="left">
			<div class="element-header left-side">
				<h4><b>LEFT</b></h4>
			</div>
			<div class="active-form">
				<a href="#" class="icon-remove-side remove-side">Remove side</a>
				<?php $this->renderPartial('form_Element_OphInBiometry_Measurement_fields', array('side' => 'left', 'element' => $element, 'form' => $form, 'data' => $data, 'measurementInput' => $iolRefValues)); ?>
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
</section>
<script type="text/javascript">
	function switchSides( element ){
		// swith from right active to left
		if( $(element).hasClass('left-eye') ){
			$('#right-eye-lens').addClass('disabled').removeClass('highlighted-lens');
			$('#right-eye-selection').addClass('disabled').removeClass('highlighted-selection');
			$('#right-eye-calculation').addClass('disabled').removeClass('highlighted-calculation');

			$('#left-eye-lens').removeClass('disabled').addClass('highlighted-lens');
			$('#left-eye-selection').removeClass('disabled').addClass('highlighted-selection');
			$('#left-eye-calculation').removeClass('disabled').addClass('highlighted-calculation');

		}else if( $(element).hasClass('right-eye') ){
			$('#left-eye-lens').addClass('disabled').removeClass('highlighted-lens');
			$('#left-eye-selection').addClass('disabled').removeClass('highlighted-selection');
			$('#left-eye-calculation').addClass('disabled').removeClass('highlighted-calculation');

			$('#right-eye-lens').removeClass('disabled').addClass('highlighted-lens');
			$('#right-eye-selection').removeClass('disabled').addClass('highlighted-selection');
			$('#right-eye-calculation').removeClass('disabled').addClass('highlighted-calculation');
		}
	}
</script>
