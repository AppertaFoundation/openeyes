<div id="dropDownFieldValues<?php echo $element_num?>Field<?php echo $field_num?>">
	<?php if (isset($_POST['dropDownFieldValue1Field1_1'])) {
		foreach ($_POST as $key => $value) {
			if (preg_match('/^dropDownFieldValue'.$element_num.'Field'.$field_num.'_([0-9]+)$/',$key,$m)) {?>
				<input class="dropDownFieldValueTextInput" type="text" name="dropDownFieldValue<?php echo $element_num?>Field<?php echo $field_num?>_<?php echo $m[1]?>" value="<?php echo $value?>" /><?php if ($m[1] != '1') {?><input type="submit" class="dropDownFieldValuesRemoveValue" value="remove"><?php }?><br/>
			<?php }
		};
	}else{?>
		<input type="text" class="dropDownFieldValueTextInput" name="dropDownFieldValue<?php echo $element_num?>Field<?php echo $field_num?>_1" value="Enter value" /><br/>
	<?php }?>
</div>
<input type="submit" class="dropDownFieldValuesAddValue" name="dropDownFieldValuesAddValue<?php echo $element_num?>Field<?php echo $field_num?>" value="add value" />
