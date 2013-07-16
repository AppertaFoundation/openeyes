<div id="multiSelectFieldValues<?php echo $element_num?>Field<?php echo $field_num?>">
	<?php if (isset($_POST['multiSelectFieldValue'.$element_num.'Field'.$field_num.'_1'])) {?>
		<?php foreach ($_POST as $key => $value) {
			if (preg_match('/^multiSelectFieldValue'.$element_num.'Field'.$field_num.'_([0-9]+)$/',$key,$m)) {?>
				<input type="checkbox" class="multiSelectFieldValueTextInputDefault" name="multiSelectFieldValueTextInputDefault<?php echo $element_num?>Field<?php echo $field_num?>_<?php echo $m[1]?>" value="1"<?php if (@$_POST['multiSelectFieldValueTextInputDefault'.$element_num.'Field'.$field_num.'_'.$m[1]]) {?> checked="checked"<?php }?> />
				<input class="multiSelectFieldValueTextInput" type="text" name="multiSelectFieldValue<?php echo $element_num?>Field<?php echo $field_num?>_<?php echo $m[1]?>" value="<?php echo $value?>" /><?php if ($m[1] != '1') {?><input type="submit" class="multiSelectFieldValuesRemoveValue" value="remove"><?php }?><br/>
			<?php }
		};
	} else {?>
		<input type="checkbox" class="multiSelectFieldValueTextInputDefault" name="multiSelectFieldValueTextInputDefault<?php echo $element_num?>Field<?php echo $field_num?>_1" value="1" />
		<input type="text" class="multiSelectFieldValueTextInput" name="multiSelectFieldValue<?php echo $element_num?>Field<?php echo $field_num?>_1" value="Enter value" /><br/>
	<?php }?>
</div>
<input type="submit" class="multiSelectFieldValuesAddValue" name="multiSelectFieldValuesAddValue<?php echo $element_num?>Field<?php echo $field_num?>" value="add value" />
