<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="element-fields row">
	<div class="row field-row">
		<div class="large-2 column"><label for="find-user"><?= $element->getAttributeLabel('referrer_id') ?></label></div>

		<div class="large-4 column autocomplete-row">
			<span id="referrer-field">
                    <span id="referrer-user-display"><?php echo $element->referrer ? $element->referrer->getFullnameAndTitle() : ''; ?></span>
	<?php
	$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
		'name' => 'find_user',
		'id' => 'find-user',
		'value'=>'',
		'source'=>"js:function(request, response) {
                                    $.ajax({
                                        'url': '" . Yii::app()->createUrl('/user/autocomplete') . "',
                                        'type':'GET',
                                        'data':{'term': request.term, 'consultant_only': 1},
                                        'success':function(data) {
                                            data = $.parseJSON(data);
                                            response(data);
                                        }
                                    });
                                }",
		'options' => array(
			'minLength'=>'3',
			'select' => "js:function(event, ui) {
                                        $('#referrer-user-display').html(ui.item.label);
                                        $('#OEModule_Internalreferral_models_Element_Internalreferral_ReferralDetails_referrer_id').val(ui.item.id);
                                        $('#find-user').val('');
                                        return false;
                                    }",
		),
		'htmlOptions' => array(
			'placeholder' => 'search by name or username'
		),
	));
	?>
				</span>
		</div>
		<?php echo $form->hiddenField($element, 'referrer_id'); ?>
	</div>
	<?php echo $form->dropDownList($element, 'from_subspecialty_id', CHtml::listData(Subspecialty::model()->findAll(array('order'=> 'name asc')),'id','name'),array('empty'=>'- Please select -'))?>
	<?php echo $form->dropDownList($element, 'to_subspecialty_id', CHtml::listData(Subspecialty::model()->findAll(array('order'=> 'name asc')),'id','name'),array('empty'=>'- Please select -'))?>
</div>

