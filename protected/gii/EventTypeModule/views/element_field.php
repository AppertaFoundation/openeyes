<div class="element_field">
	<label style="display: inline;">Field label: </label>
	<label style="margin-left: 9.3em; display: inline;">Field name: </label>
	<br/>
	<?php echo CHtml::textField('elementName'.$element_num.'FieldLabel'.$field_num,@$_POST['elementName'.$element_num.'FieldLabel'.$field_num],array('size'=>30,'class'=>'fieldLabel')); ?>
	<?php echo CHtml::textField('elementName'.$element_num.'FieldName'.$field_num,@$_POST['elementName'.$element_num.'FieldName'.$field_num],array('size'=>30, 'class'=>'fieldName')); ?>
	<?php
	$field_types = array('Textbox','Decimal','Integer','Textarea','Date picker','Dropdown list','Textarea with dropdown','Checkbox','Radio buttons','Boolean','Multi select','Slider');

	if (file_exists(Yii::app()->basePath.'/modules/eyedraw')) {
		$field_types[] = 'EyeDraw';
	}

	sort($field_types);
	?>

	<select name="elementType<?php echo $element_num?>FieldType<?php echo $field_num?>" class="selectFieldType">
		<?php foreach ($field_types as $field) {?>
			<option value="<?php echo $field?>"<?php if (@$_POST['elementType'.$element_num.'FieldType'.$field_num] == $field) {?> selected="selected"<?php }?>><?php echo $field?></option>
		<?php }?>
	</select>

	<input type="submit" class="remove_element_field" name="removeElementField<?php echo $element_num?>_<?php echo $field_num?>" value="remove" /><br />
	<input type="checkbox" name="isRequiredField<?php echo $element_num?>_<?php echo $field_num?>" value="1" <?php if (empty($_POST) || @$_POST['isRequiredField'.$element_num.'_'.$field_num]) {?> checked="checked" <?php }?>/> Required<br/>

	<div id="extraDataElement<?php echo $element_num?>Field<?php echo $field_num?>">
		<?php if (file_exists("protected/gii/EventTypeModule/views/extra_".str_replace(" ","",@$_POST['elementType'.$element_num.'FieldType'.$field_num]).".php")) {
			$this->renderPartial('extra_'.str_replace(' ','',@$_POST['elementType'.$element_num.'FieldType'.$field_num]),array('element_num'=>$element_num,'field_num'=>$field_num));
		}?>
	</div>

	<?php if (isset($this->form_errors['elementName'.$element_num.'FieldName'.$field_num])) {?>
		<span style="color: #f00;"><?php echo $this->form_errors['elementName'.$element_num.'FieldName'.$field_num]?></span><br/>
	<?php }?>
	<?php if (isset($this->form_errors['elementName'.$element_num.'FieldLabel'.$field_num])) {?>
		<span style="color: #f00;"><?php echo $this->form_errors['elementName'.$element_num.'FieldLabel'.$field_num]?></span><br/>
	<?php }?>
	<div style="height: 1em;"></div>
</div>
