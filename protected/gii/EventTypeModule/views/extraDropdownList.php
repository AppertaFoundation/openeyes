<div style="margin-top: 8px;">
	Dropdown method:&nbsp;&nbsp;&nbsp;<input type="radio" class="dropDownMethodSelector" name="dropDownMethod<?php echo $element_num?>Field<?php echo $field_num?>" value="0" <?php if (@$_POST['dropDownMethod'.$element_num.'Field'.$field_num] === '0') {?> checked="checked" <?php }?>/> Enter values
	&nbsp;&nbsp;
	<input type="radio" class="dropDownMethodSelector" name="dropDownMethod<?php echo $element_num?>Field<?php echo $field_num?>" value="1" <?php if (@$_POST['dropDownMethod'.$element_num.'Field'.$field_num] === '1') {?> checked="checked" <?php }?>/> Point at SQL table
	<div id="dropDownMethodFields<?php echo $element_num?>Field<?php echo $field_num?>">
		<?php if (@$_POST['dropDownMethod'.$element_num.'Field'.$field_num] === '0') {
			$this->renderPartial('extraDropdownListEnterValues',array('element_num'=>$element_num,'field_num'=>$field_num));
		} else if (@$_POST['dropDownMethod'.$element_num.'Field'.$field_num] === '1') {
			$this->renderPartial('extraDropdownListPointAtSQLTable',array('element_num'=>$element_num,'field_num'=>$field_num));
		}?>
	</div>
</div>
