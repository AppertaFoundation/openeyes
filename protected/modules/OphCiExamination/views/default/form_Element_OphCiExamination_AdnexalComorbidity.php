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
<div class="element-fields element-eyes">
	<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField'))?>
	<div class="element-eye right-eye column left side" <?php if (!$element->hasRight()) {?>style="display: none;"<?php }?> data-side="right">
		<div class="active-form field-row flex-layout">
      <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
      <div class="cols-11 flex-layout">
          <?php echo $form->textArea($element, 'right_description', array('nowrapper' => true, 'class' => 'cols-6', 'rows' => 1));?>
      </div>
      <div class="flex-item-bottom">
        <button class="button hint green js-add-select-search" type="button">
          <i class="oe-i plus pro-theme"></i>
        </button>
        <div id="add-to-adnexal" class="oe-add-select-search" style="display: none;">
            <?php $this->renderPartial('_attributes', array('element' => $element, 'field' => 'right_description', 'form' => $form))?>
        </div>
      </div>
		</div>
		<div class="inactive-form" <?php if ($element->hasRight()) {?>style="display: none;"<?php }?>>
			<div class="add-side">
				<a href="#">
					Add right side <span class="icon-add-side"></span>
				</a>
			</div>
		</div>
	</div>
	<div class="element-eye left-eye column right side" <?php if (!$element->hasLeft()) {?>style="display: none;"<?php }?> data-side="left">
		<div class="active-form field-row flex-layout">
      <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
      <div class="cols-11 flex-layout">
          <?php echo $form->textArea($element, 'left_description', array('nowrapper' => true, 'class' => 'cols-6', 'rows' => 1))?>
      </div>
			<div class="flex-item-bottom">
        <button class="button hint green js-add-select-search" type="button">
          <i class="oe-i plus pro-theme"></i>
        </button>
        <div id="add-to-adnexal" class="oe-add-select-search" style="display: none;">
            <?php $this->renderPartial('_attributes', array('element' => $element, 'field' => 'left_description', 'form' => $form))?>
        </div>
			</div>
		</div>
		<div class="inactive-form" <?php if ($element->hasLeft()) {?>style="display: none;"<?php }?>>
			<div class="add-side">
				<a href="#">
					Add left side <span class="icon-add-side"></span>
				</a>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
    // Hide the adding dialog, print text to textArea
    $('.oe-add-select-search .add-icon-btn').on('click', function () {
        var eye = $(this).closest('.side').attr('data-side');
        var inputText = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_AdnexalComorbidity_' + eye + '_description');
        var popup = $('.' + eye + '-eye #add-to-adnexal');

        inputText.val(inputText.val() ? inputText.val() + popup.find('li.selected').attr('data-str') : popup.find('li.selected').attr('data-str'));
    });
</script>