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

<div class="element-fields element-eyes row">
	<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
	<div class="element-eye right-eye column left side<?php if (!$element->hasRight()) {
    ?> inactive<?php 
}?>" data-side="right">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<div class="field-row row">
				<div class="large-12 column end">
					<?php echo $form->radioButtons($element, 'right_rapd', array(
                            0 => 'Not Checked',
                            1 => 'Yes',
                            2 => 'No'
                        ),
                        ($element->right_rapd !== null) ? $element->right_rapd : 0,
                        false,
                        false,
                        false,
                        true,
                        array(
                            'text-align'=>'right',
                            'nowrapper'=>false
                        ));
                    ?>
				</div>
			</div>
			<div class="field-row">
				<?php echo $form->textArea($element, 'right_comments', array('rows' => 1, 'nowrapper'=>true)) ?>
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
			<div class="field-row row">
				<div class="large-12 column end">
					<?php echo $form->radioButtons($element, 'left_rapd', array(
                            0 => 'Not Checked',
                            1 => 'Yes',
                            2 => 'No'
                        ),
                        ($element->left_rapd !== null) ? $element->left_rapd : 0,
                        false,
                        false,
                        false,
                        true,
                        array(
                            'text-align'=>'right',
                            'nowrapper'=>false
                        ));
                    ?>
				</div>
			</div>
			<div class="field-row">
				<?php echo $form->textArea($element, 'left_comments', array('rows' => 1, 'nowrapper'=>true)) ?>
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
