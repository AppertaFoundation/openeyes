<div style="margin-top: 8px;">
	<table>
		<tr>
			<td>Min value:</td>
			<td><input type="text" id="integerMinValue<?php echo $element_num?>Field<?php echo $field_num?>" name="integerMinValue<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['integerMinValue'.$element_num.'Field'.$field_num]?>" /> (eg 1)</td>
		</tr>
		<?php if (isset($this->form_errors['integerMinValue'.$element_num.'Field'.$field_num])) {?>
			<tr>
				<td></td>
				<td>
					<span style="color: #f00;"><?php echo $this->form_errors['integerMinValue'.$element_num.'Field'.$field_num]?></span>
				</td>
			</tr>
		<?php }?>
		<tr>
			<td>Max value:</td>
			<td><input type="text" id="integerMaxValue<?php echo $element_num?>Field<?php echo $field_num?>" name="integerMaxValue<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['integerMaxValue'.$element_num.'Field'.$field_num]?>" /> (eg 100 or 133.7)</td>
		</tr>
		<?php if (isset($this->form_errors['integerMaxValue'.$element_num.'Field'.$field_num])) {?>
			<tr>
				<td></td>
				<td>
					<span style="color: #f00;"><?php echo $this->form_errors['integerMaxValue'.$element_num.'Field'.$field_num]?></span>
				</td>
			</tr>
		<?php }?>
		<tr>
			<td>Default value:</td>
			<td><input type="text" id="integerDefaultValue<?php echo $element_num?>Field<?php echo $field_num?>" name="integerDefaultValue<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['integerDefaultValue'.$element_num.'Field'.$field_num]?>" /> (eg 10.7, blank for none)</td>
		</tr>
		<?php if (isset($this->form_errors['integerDefaultValue'.$element_num.'Field'.$field_num])) {?>
			<tr>
				<td></td>
				<td>
					<span style="color: #f00;"><?php echo $this->form_errors['integerDefaultValue'.$element_num.'Field'.$field_num]?></span>
				</td>
			</tr>
		<?php }?>
	</table>
</div>
