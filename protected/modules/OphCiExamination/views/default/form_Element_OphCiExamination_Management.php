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
<div class="element-fields flex-layout full-width ">
  <div class="cols-7">
      <?php echo $form->textArea($element, 'comments', array('class' => 'cols-full', 'nowrapper' => true), false, array('rows' => 1, 'placeholder' => 'Management', 'style' => 'overflow: hidden; overflow-wrap: break-word; height: 24px;'))?>
  </div>
	<div class="flex-item-bottom">
    <button class="button hint green js-add-select-search" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button>
    <div id="add-to-management" class="oe-add-select-search auto-width" style="display: none;">
        <?php $this->renderPartial('_attributes', array('element' => $element, 'field' => 'comments', 'form' => $form))?>
    </div>
	</div>
</div>

<script type="text/javascript">
    // Hide the adding dialog, print text to textArea
    $('.oe-add-select-search .add-icon-btn').on('click', function () {
        var inputText = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_Management_comments');
        var popup = $('#add-to-management');

        inputText.val(inputText.val() ? inputText.val() + popup.find('li.selected').attr('data-str') : popup.find('li.selected').attr('data-str'));
    });
</script>
