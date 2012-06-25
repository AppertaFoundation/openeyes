<div style="margin-top: 8px;">
	<div id="textAreaDropDownMethodFields<?php echo $element_num?>Field<?php echo $field_num?>">
		<div id="textAreaDropDownFieldValues<?php echo $element_num?>Field<?php echo $field_num?>">
			<?php if (isset($_POST['textAreaDropDownFieldValue'.$element_num.'Field'.$field_num.'_1'])) {
				foreach ($_POST as $key => $value) {
					if (preg_match('/^textAreaDropDownFieldValue'.$element_num.'Field'.$field_num.'_([0-9]+)$/',$key,$m)) {?>
						<input class="textAreaDropDownFieldValueTextInput" type="text" name="textAreaDropDownFieldValue<?php echo $element_num?>Field<?php echo $field_num?>_<?php echo $m[1]?>" value="<?php echo $value?>" /><?php if ($m[1] != '1') {?><input type="submit" class="textAreaDropDownFieldValuesRemoveValue" value="remove"><?php }?><br/>
					<?php }
				};
			}else{?>
				<input type="text" class="textAreaDropDownFieldValueTextInput" name="textAreaDropDownFieldValue<?php echo $element_num?>Field<?php echo $field_num?>_1" value="Enter value" /><br/>
			<?php }?>
		</div>
		<input type="submit" class="textAreaDropDownFieldValuesAddValue" name="textAreaDropDownFieldValuesAddValue<?php echo $element_num?>Field<?php echo $field_num?>" value="add value" />
	</div>
</div>
