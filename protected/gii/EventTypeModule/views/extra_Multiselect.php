<div style="margin-top: 8px;">
	Multi select method:&nbsp;&nbsp;&nbsp;<input type="radio" class="multiSelectMethodSelector" name="multiSelectMethod<?php echo $element_num?>Field<?php echo $field_num?>" value="0" <?php if (@$_POST['multiSelectMethod'.$element_num.'Field'.$field_num] === '0') {?> checked="checked" <?php }?>/> Enter values
	&nbsp;&nbsp;
	<input type="radio" class="multiSelectMethodSelector" name="multiSelectMethod<?php echo $element_num?>Field<?php echo $field_num?>" value="1" <?php if (@$_POST['multiSelectMethod'.$element_num.'Field'.$field_num] === '1') {?> checked="checked" <?php }?>/> Point at SQL table<br/>
	<?php if (isset($this->form_errors['multiSelectMethod'.$element_num.'Field'.$field_num])) {?>
		<span style="color: #f00;"><?php echo $this->form_errors['multiSelectMethod'.$element_num.'Field'.$field_num]?></span><br/>
	<?php }?>
	<div style="height: 0.4em;"></div>
	Use the checkboxes to mark pre-selected items
	<div style="height: 0.4em;"></div>
	<div id="multiSelectMethodFields<?php echo $element_num?>Field<?php echo $field_num?>">
		<?php if (@$_POST['multiSelectMethod'.$element_num.'Field'.$field_num] === '0') {
			$this->renderPartial('extra_Multiselect_entervalues',array('element_num'=>$element_num,'field_num'=>$field_num));
		} else if (@$_POST['multiSelectMethod'.$element_num.'Field'.$field_num] === '1') {
			$this->renderPartial('extra_Multiselect_pointatsqltable',array('element_num'=>$element_num,'field_num'=>$field_num));
		}?>
	</div>
</div>
