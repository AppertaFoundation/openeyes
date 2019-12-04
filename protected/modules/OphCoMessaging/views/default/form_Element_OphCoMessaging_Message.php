<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div>
  <div class="element-fields full-width flex-layout flex-top col-gap">
    <div class="cols-5">
      <table class="cols-full">
        <colgroup>
          <col class="cols-6">
          <col class="cols-6">
        </colgroup>
        <tbody>
        <tr>
          <td>
            <label for="find-user">For the attention of: <span class="js-has-tooltip fa oe-i info small"
                                                               data-tooltip-content="Cannot be changed after message creation."></span></label>
          </td>
          <td style="text-align: right;">
                <?php if ($element->isNewRecord) { ?>
                <div class="autocomplete-row">
                  <span id="fao-field">
                    <span
                        id="fao_user_display"><?php echo $element->for_the_attention_of_user ? $element->for_the_attention_of_user->getFullnameAndTitle() : ''; ?></span>
                      <?php $this->widget('application.widgets.AutoCompleteSearch'); ?>
                    </span>
                </div>
                <?php } else { ?>
                <div class="cols-4">
                  <div class="data-value"><?= $element->for_the_attention_of_user->getFullnameAndTitle(); ?></div>
                </div>
                <?php } ?>
                <?php echo $form->hiddenField($element, 'for_the_attention_of_user_id'); ?>
          </td>
        </tr>
        <tr>
          <td>
            Type:
          </td>
          <td>
                <?php echo $form->dropDownList($element, 'message_type_id',
                  CHtml::listData(OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType::model()->findAll(array('order' => 'display_order asc')),
                      'id', 'name'), array('empty' => 'Select', 'nolabel' => true), false,
                  array('label' => 0, 'field' => 12)) ?>

          </td>
        </tr>
        <tr>
          <td>
            Urgent:
          </td>
          <td style="text-align: right;">
                <?php echo $form->checkbox($element, 'urgent', array('nowrapper' => true, 'no-label' => true),
                  array('field' => 11)) ?>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <div class="cols-7">
      <table class="cols-full">
        <colgroup>
          <col>
          <col class="cols-9">
        </colgroup>
        <tbody>
        <tr>
          <td>
            Message
          </td>
          <td>
                <?php echo $form->textArea($element, 'message_text',
                  array('rows' => 4, 'cols' => 500, 'no_label' => true), false,
                  array('placeholder' => 'Your Message...', 'class' => 'autosize'), array('label' => 2, 'field' => 12)) ?>
          </td>
        </tr>
        </tbody>
      </table>

    </div>
  </div>
</div>
<script>
    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#oe-autocompletesearch'),
        url: '/user/autocomplete',
        onSelect: function () {
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            $('#fao_user_display').html(AutoCompleteResponse.label);
            $('#OEModule_OphCoMessaging_models_Element_OphCoMessaging_Message_for_the_attention_of_user_id').val(AutoCompleteResponse.id);
            return false;
        }
    });
</script>
