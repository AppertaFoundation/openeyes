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
?>
<style>
	.readonly-div{
		height: auto;
		border: 1px solid #6b8aaa;
		background-color: #dddddd;
	}
</style>

	<div class="element-fields element-eyes row">
		<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
		<div id="right-eye-calculation" class="element-eye right-eye left side column <?php if (!$element->hasRight()) { ?> inactive<?php } ?>"
				 data-side="right" >
			<div class="active-form">

				<?php $this->renderPartial('form_Element_OphInBiometry_Calculation_fields',
                        array('side' => 'right', 'element' => $element, 'form' => $form, 'data' => $data)); ?>
			</div>
			<div class="inactive-form">
				<div class="add-side">
					Set right side lens type
				</div>
			</div>
		</div>

		<div id="left-eye-calculation" class="element-eye left-eye right side column  <?php if (!$element->hasLeft()) { ?> inactive<?php } ?>"
				 data-side="left">
			<div class="active-form">

				<?php $this->renderPartial('form_Element_OphInBiometry_Calculation_fields',
                        array('side' => 'left', 'element' => $element, 'form' => $form, 'data' => $data)); ?>
			</div>
			<div class="inactive-form">
				<div class="add-side">
					Set left side lens type
				</div>
			</div>
		</div>

	</div>
<div class="element-fields element-eyes row edit element">
	<div id="right-eye-comments" class="element-eye right-eye left side disabled" data-side="right">
		<div class="active-form">
			<div class="element-fields">
				<span class="field-info">General Comments:</span>
			</div>
		</div>
		<div class="active-form">
			<div class="element-fields">
				<?php echo $form->textArea($element, 'comments_right',
					array('rows' => 3, 'label' => false, 'nowrapper' => true), false,
					array('class' => 'comments_right')) ?>
			</div>
		</div>
	</div>
	<div id="left-eye-comments" class="element-eye left-eye right side column disabled" data-side="left">
		<div class="active-form">
			<div class="element-fields">
				<span class="field-info">General Comments:</span>
			</div>
		</div>
		<div class="active-form">
			<div class="element-fields">
			<?php echo $form->textArea($element, 'comments_left',
				array('rows' => 3, 'label' => false, 'nowrapper' => true), false,
				array('class' => 'comments_left')) ?>
		</div>
		</div>
	</div>
</div>

<div id="comments">
	<span class="field-info large-12">
	<?php
	if ($this->is_auto) {
		if (!$this->getAutoBiometryEventData($this->event->id)[0]->is700() || $element->{'comments'}) {
			echo 'Device Comments:';
			echo '<div class="readonly-box">' . $element->{'comments'} . '<br></div>';
		}
	} else {
		echo $form->textField($element, 'comments', array('style' => 'width:1027px;'), null,
			array('label' => 4, 'field' => 200));
	}
	?>
	</span>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		if ($('section.Element_OphInBiometry_Measurement').find('.element-eye.right-eye').hasClass('inactive')) {
			$('section.Element_OphInBiometry_Calculation').find('.element-eye.right-eye').find('.active-form').hide();
			$('section.Element_OphInBiometry_Calculation').find('.element-eye.right-eye').find('.inactive-form').show();
		}
		if ($('section.Element_OphInBiometry_Measurement').find('.element-eye.left-eye').hasClass('inactive')) {
			$('section.Element_OphInBiometry_Calculation').find('.element-eye.left-eye').find('.active-form').hide();
			$('section.Element_OphInBiometry_Calculation').find('.element-eye.left-eye').find('.inactive-form').show();
		}
	});
</script>
