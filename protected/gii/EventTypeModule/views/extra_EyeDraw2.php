<?php
$eyedraw_classes = array(
	'AnteriorSegment',
	'Buckle',
	'Cataract',
	'Gonioscopy',
	'OpticDisc',
	'PosteriorSegment',
	'Refraction',
	'SurgeonPosition',
	'Vitrectomy',
);
?>
<div style="margin-top: 8px;">
	Template:&nbsp;&nbsp;&nbsp;
	<select class="eyeDraw2ClassSelect" name="eyedraw2Class<?php echo $element_num?>Field<?php echo $field_num?>">
		<?php foreach ($eyedraw_classes as $class) {?>
			<option value="<?php echo $class?>"<?php if (@$_POST['eyedraw2Class'.$element_num.'Field'.$field_num] == $class) {?> selected="selected"<?php }?>><?php echo $class?></option>
		<?php }?>
	</select><br/>
	<?php if (isset($this->form_errors['eyedraw2Class'.$element_num.'Field'.$field_num])) {?>
		<span style="color: #f00;"><?php echo $this->form_errors['eyedraw2Class'.$element_num.'Field'.$field_num]?></span><br/>
	<?php }?>
	<div style="height: 0.4em;"></div>
	<div id="eyeDraw2ExtraReportFieldDiv<?php echo $element_num?>Field<?php echo $field_num?>">
		<?php if (in_array(@$_POST['eyedraw2Class'.$element_num.'Field'.$field_num],array('Cataract','Buckle','Vitrectomy'))) {?>
			<input type="checkbox" name="eyedraw2ExtraReport<?php echo $element_num?>Field<?php echo $field_num?>" value="1"<?php if (@$_POST['eyedraw2ExtraReport'.$element_num.'Field'.$field_num]){?> checked="checked"<?php }?> /> Store eyedraw report data in hidden input<br/>
		<?php }?>
	</div>
	<div style="height: 0.4em;"></div>
	Size: <input type="text" class="noreturn" name="eyedraw2Size<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo (isset($_POST['eyedraw2Size'.$element_num.'Field'.$field_num]) ? @$_POST['eyedraw2Size'.$element_num.'Field'.$field_num] : '300')?>" /><br/>
	<?php if (isset($this->form_errors['eyedraw2Size'.$element_num.'Field'.$field_num])) {?>
		<span style="color: #f00;"><?php echo $this->form_errors['eyedraw2Size'.$element_num.'Field'.$field_num]?></span><br/>
	<?php }?>
</div>
