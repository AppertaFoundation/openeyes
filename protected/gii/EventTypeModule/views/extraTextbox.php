<div style="margin-top: 8px;">
	<table>
		<tr>
			<td>Size:</td>
			<td><input type="text" id="textBoxSize<?php echo $element_num?>Field<?php echo $field_num?>" name="textBoxSize<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo empty($_POST) ? '10' : @$_POST['textBoxSize'.$element_num.'Field'.$field_num]?>" /></td>
		</tr>
		<?php if (isset($this->form_errors['textBoxSize'.$element_num.'Field'.$field_num])) {?>
			<tr>
				<td></td>
				<td>
					<span style="color: #f00;"><?php echo $this->form_errors['textBoxSize'.$element_num.'Field'.$field_num]?></span>
				</td>
			</tr>
		<?php }?>
		<tr>
			<td>Max length:</td>
			<td><input type="text" id="textBoxMaxLength<?php echo $element_num?>Field<?php echo $field_num?>" name="textBoxMaxLength<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['textBoxMaxLength'.$element_num.'Field'.$field_num]?>" /></td>
		</tr>
		<?php if (isset($this->form_errors['textBoxMaxLength'.$element_num.'Field'.$field_num])) {?>
			<tr>
				<td></td>
				<td>
					<span style="color: #f00;"><?php echo $this->form_errors['textBoxMaxLength'.$element_num.'Field'.$field_num]?></span>
				</td>
			</tr>
		<?php }?>
	</table>
</div>
