<div id="radioButtonFieldValues<?php echo $element_num?>Field<?php echo $field_num?>">
	<?php if (isset($_POST['radioButtonFieldValue'.$element_num.'Field'.$field_num.'_1'])) {?>
		<input type="radio" class="radioButtonFieldValueTextInputDefault" name="radioButtonFieldValueTextInputDefault<?php echo $element_num?>Field<?php echo $field_num?>" value="0"<?php if (@$_POST['radioButtonFieldValueTextInputDefault'.$element_num.'Field'.$field_num] == 0) {?> checked="checked"<?php }?> /> No default value<br/>
		<?php foreach ($_POST as $key => $value) {
			if (preg_match('/^radioButtonFieldValue'.$element_num.'Field'.$field_num.'_([0-9]+)$/',$key,$m)) {?>
				<input type="radio" class="radioButtonFieldValueTextInputDefault" name="radioButtonFieldValueTextInputDefault<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo $m[1]?>" <?php if (@$_POST['radioButtonFieldValueTextInputDefault'.$element_num.'Field'.$field_num] == $m[1]) {?> checked="checked" <?php }?> />
				<input class="radioButtonFieldValueTextInput" type="text" name="radioButtonFieldValue<?php echo $element_num?>Field<?php echo $field_num?>_<?php echo $m[1]?>" value="<?php echo $value?>" /><?php if ($m[1] != '1') {?><input type="submit" class="radioButtonFieldValuesRemoveValue" value="remove"><?php }?><br/>
			<?php }
		};
	} else {?>
		<input type="radio" class="radioButtonFieldValueTextInputDefault" name="radioButtonFieldValueTextInputDefault<?php echo $element_num?>Field<?php echo $field_num?>" value="0" checked="checked"> No default value<br/>
		<input type="radio" class="radioButtonFieldValueTextInputDefault" name="radioButtonFieldValueTextInputDefault<?php echo $element_num?>Field<?php echo $field_num?>" value="1" />
		<input type="text" class="radioButtonFieldValueTextInput" name="radioButtonFieldValue<?php echo $element_num?>Field<?php echo $field_num?>_1" value="Enter value" /><br/>
	<?php }?>
</div>
<input type="submit" class="radioButtonFieldValuesAddValue" name="radioButtonFieldValuesAddValue<?php echo $element_num?>Field<?php echo $field_num?>" value="add value" />
