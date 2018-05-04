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
    <?php foreach(['left' => 'right', 'right' => 'left'] as $page_side => $eye_side):?>
        <div class="element-eye <?=$eye_side?>-eye column <?=$page_side?> side"
             data-side="<?=$eye_side?>"
        >
            <div class="active-form field-row flex-layout"
                 style="<?=!$element->hasEye($eye_side)?"display: none;":""?>">
              <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
              <div class="cols-11 flex-layout">
                  <?php echo $form->textArea($element, $eye_side.'_description', array('nowrapper' => true, 'class' => 'cols-6', 'rows' => 1));?>
              </div>
              <div class="flex-item-bottom">
                <button class="button hint green js-add-select-search" type="button">
                  <i class="oe-i plus pro-theme"></i>
                </button>
                <div id="add-to-adnexal" class="oe-add-select-search" style="display: none;">
                    <?php $this->renderPartial('_attributes', array('element' => $element, 'field' => $eye_side.'_description', 'form' => $form))?>
                </div>
              </div>
            </div>
            <div class="inactive-form" style="<?=$element->hasEye($eye_side)?"display: none;":""?>">
                <div class="add-side">
                    <a href="#">
                        Add <?=ucfirst($eye_side)?> side <span class="icon-add-side"></span>
                    </a>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(function () {
               var side = $('.edit-<?=CHtml::modelName($element)?> .<?=$eye_side?>-eye');
               var popup = side.find('#add-to-adnexal');
               var adnexText = side.find('#<?=CHtml::modelName($element)?>_<?=$eye_side?>_description');

               function setAdnexText() {
                   popup.find('.selected').each(function () {
                     adnexText.val(adnexText.val() ?
                       adnexText.val()+$(this).attr('data-str') : $(this).attr('data-str')
                     );
                   });

                   adnexText.trigger('oninput');
                   popup.find('.selected').removeClass('selected');
               }

               setUpAdder(
                   popup,
                   'multi',
                   setAdnexText,
                   side.find('.js-add-select-search'),
                   popup.find('.add-icon-btn'),
                   popup.find('.close-icon-btn')
               );
            });
        </script>
    <?php endforeach;?>
</div>
