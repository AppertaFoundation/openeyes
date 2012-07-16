<?php
$eyedraw_classes = array();

$dh = opendir(Yii::app()->basePath.'/modules/eyedraw');

while ($file = readdir($dh)) {
	if (preg_match('/^OEEyeDrawWidget([a-zA-Z]+)\.php$/',$file,$m)) {
		$eyedraw_classes[] = $m[1];
	}
}

closedir($dh);
?>
<div style="margin-top: 8px;">
	Eyedraw type:&nbsp;&nbsp;&nbsp;
	<select class="eyeDrawClassSelect" name="eyedrawClass<?php echo $element_num?>Field<?php echo $field_num?>">
		<option value="">- Please select -</option>
		<?php foreach ($eyedraw_classes as $class) {?>
			<option value="<?php echo $class?>"<?php if (@$_POST['eyedrawClass'.$element_num.'Field'.$field_num] == $class) {?> selected="selected"<?php }?>><?php echo $class?></option>
		<?php }?>
	</select><br/>
	<?php if (isset($this->form_errors['eyedrawClass'.$element_num.'Field'.$field_num])) {?>
		<span style="color: #f00;"><?php echo $this->form_errors['eyedrawClass'.$element_num.'Field'.$field_num]?></span><br/>
	<?php }?>
	<div style="height: 0.4em;"></div>
	<div id="eyeDrawExtraReportFieldDiv<?php echo $element_num?>Field<?php echo $field_num?>">
		<?php if (in_array(@$_POST['eyedrawClass'.$element_num.'Field'.$field_num],array('Cataract','Buckle','Vitrectomy'))) {?>
			<input type="checkbox" name="eyedrawExtraReport<?php echo $element_num?>Field<?php echo $field_num?>" value="1"<?php if (@$_POST['eyedrawExtraReport'.$element_num.'Field'.$field_num]){?> checked="checked"<?php }?> /> Store eyedraw report data in hidden input<br/>
		<?php }?>
	</div>
	<div style="height: 0.4em;"></div>
	Size: <input type="text" class="noreturn" name="eyedrawSize<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['eyedrawSize'.$element_num.'Field'.$field_num]?>" /><br/>
	<?php if (isset($this->form_errors['eyedrawSize'.$element_num.'Field'.$field_num])) {?>
		<span style="color: #f00;"><?php echo $this->form_errors['eyedrawSize'.$element_num.'Field'.$field_num]?></span><br/>
	<?php }?>
</div>
