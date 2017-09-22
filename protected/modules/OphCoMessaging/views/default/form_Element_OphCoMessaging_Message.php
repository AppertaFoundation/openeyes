<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="element-fields row">
	<div class="element-fields">
        <div class="row field-row">
            <div class="large-2 column"><label for="find-user">For the attention of:  <span class="has-tooltip fa fa-info-circle" data-tooltip="Cannot be changed after message creation."></span></label></div>

            <?php if ($element->isNewRecord) { ?>
                <div class="large-4 column autocomplete-row">
                    <span id="fao-field">
                    <span id="fao_user_display"><?php echo $element->for_the_attention_of_user ? $element->for_the_attention_of_user->getFullnameAndTitle() : ''; ?></span>
                    <?php
                    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                        'name' => 'find_user',
                        'id' => 'find-user',
                        'value' => '',
                        'source' => "js:function(request, response) {
                                    $.ajax({
                                        'url': '" . Yii::app()->createUrl('/user/autocomplete') . "',
                                        'type':'GET',
                                        'data':{'term': request.term},
                                        'success':function(data) {
                                            data = $.parseJSON(data);
                                            response(data);
                                        }
                                    });
                                }",
                        'options' => array(
                            'minLength' => '3',
                            'select' => "js:function(event, ui) {
                                        $('#fao_user_display').html(ui.item.label);
                                        $('#OEModule_OphCoMessaging_models_Element_OphCoMessaging_Message_for_the_attention_of_user_id').val(ui.item.id);
                                        $('#find-user').val('');
                                        return false;
                                    }",
                        ),
                        'htmlOptions' => array(
                            'placeholder' => 'search by name or username',
                        ),
                    ));
    ?>
                    </span>
                </div>
                <?php } else { ?>
                <div class="large-4 column"><div class="data-value"><?= $element->for_the_attention_of_user->getFullnameAndTitle(); ?></div></div>
            <?php } ?>
            <?php echo $form->hiddenField($element, 'for_the_attention_of_user_id'); ?>
    </div>
	<?php echo $form->dropDownList($element, 'message_type_id', CHtml::listData(OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType::model()->findAll(array('order' => 'display_order asc')), 'id', 'name'), array('empty' => '- Please select -'), false, array('label' => 2, 'field' => 4))?>
	<?php echo $form->checkbox($element, 'urgent', array(), array('label' => 2, 'field' => 1))?>
    <?php echo $form->textArea($element, 'message_text', array('rows' => 6, 'cols' => 80), false, null, array('label' => 2, 'field' => 6))?>
	</div>
</div>
