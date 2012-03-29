<div class="element_field">
	<label style="display: inline;">Field name: </label>
	<label style="margin-left: 9.3em; display: inline;">Field label: </label>
	<br/>
	<?php echo CHtml::textField('elementName'.$element_num.'FieldName'.$field_num,@$_POST['elementName'.$element_num.'FieldName'.$field_num],array('size'=>30)); ?> 
	<?php echo CHtml::textField('elementName'.$element_num.'FieldLabel'.$field_num,@$_POST['elementName'.$element_num.'FieldLabel'.$field_num],array('size'=>35)); ?>

	<select name="elementType<?php echo $element_num?>FieldType<?php echo $field_num?>" >
		<option value="Textbox"<?php if (@$_POST['elementType'.$element_num.'FieldType'.$field_num] == 1) {?> selected="selected"<?php }?>>Textbox</option>
		<option value="Textarea"<?php if (@$_POST['elementType'.$element_num.'FieldType'.$field_num] == 2) {?> selected="selected"<?php }?>>Textarea</option>
		<option value="Date picker"<?php if (@$_POST['elementType'.$element_num.'FieldType'.$field_num] == 3) {?> selected="selected"<?php }?>>Date picker</option>
		<option value="Dropdown list"<?php if (@$_POST['elementType'.$element_num.'FieldType'.$field_num] == 4) {?> selected="selected"<?php }?>>Dropdown list</option>
		<option value="Checkboxes"<?php if (@$_POST['elementType'.$element_num.'FieldType'.$field_num] == 5) {?> selected="selected"<?php }?>>Checkboxes</option>
		<option value="Radio buttons"<?php if (@$_POST['elementType'.$element_num.'FieldType'.$field_num] == 6) {?> selected="selected"<?php }?>>Radio buttons</option>
		<option value="Boolean"<?php if (@$_POST['elementType'.$element_num.'FieldType'.$field_num] == 7) {?> selected="selected"<?php }?>>Boolean</option>
		<option value="EyeDraw"<?php if (@$_POST['elementType'.$element_num.'FieldType'.$field_num] == 8) {?> selected="selected"<?php }?>>EyeDraw</option>
	</select>

	<input type="submit" class="remove_element_field" name="removeElementField<?php echo $element_num?>_<?php echo $field_num?>" value="remove" /><br />
	<?php if (isset($this->form_errors['elementName'.$element_num.'FieldName'.$field_num])) {?>
		<span style="color: #f00;"><?php echo $this->form_errors['elementName'.$element_num.'FieldName'.$field_num]?></span><br/>
	<?php }?>
	<?php if (isset($this->form_errors['elementName'.$element_num.'FieldLabel'.$field_num])) {?>
		<span style="color: #f00;"><?php echo $this->form_errors['elementName'.$element_num.'FieldLabel'.$field_num]?></span><br/>
	<?php }?>
	<div style="height: 1em;"></div>
</div>
