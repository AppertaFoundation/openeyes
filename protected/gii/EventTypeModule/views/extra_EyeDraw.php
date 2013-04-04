<?php
require(Yii::app()->basePath.'/modules/eyedraw/OEEyeDrawWidget.php');
$eyedraw_doodles = array();
foreach (DoodleInfo::$titles as $key => $value) {
	if ($key != 'NONE') {
		$eyedraw_doodles[] = $key;
	}
}
sort($eyedraw_doodles);

if (!isset($_POST['eyedrawSize'.$element_num.'Field'.$field_num])) {
	$_POST['eyedrawSize'.$element_num.'Field'.$field_num] = 300;
}
?>
<div style="margin-top: 8px;">
	Toolbar doodles:&nbsp;&nbsp;&nbsp;
	<select class="eyeDrawDoodleSelect" data-attr-element="<?php echo $element_num?>" data-attr-field="<?php echo $field_num?>">
		<option value="">- Please select -</option>
		<?php foreach ($eyedraw_doodles as $class) {?>
			<option value="<?php echo $class?>"><?php echo $class?></option>
		<?php }?>
	</select><br/>
	<?php if (isset($this->form_errors['eyedrawDoodleSelect'.$element_num.'Field'.$field_num])) {?>
		<span style="color: #f00;"><?php echo $this->form_errors['eyedrawDefaultDoodleSelect'.$element_num.'Field'.$field_num]?></span><br/>
	<?php }?>
	<div style="height: 0.4em;"></div>
	<div class="selectedToolbarDoodles">
		<?php if (!empty($_POST['eyedrawToolbarDoodle'.$element_num.'Field'.$field_num])) {
			foreach ($_POST['eyedrawToolbarDoodle'.$element_num.'Field'.$field_num] as $doodle) {?>
				<div>
					<input type="hidden" name="eyedrawToolbarDoodle<?php echo $element_num?>Field<?php echo $field_num?>[]" value="<?php echo $doodle?>" /><?php echo $doodle?>
					<a href="#" class="removeToolbarDoodle">(remove)</a>
				</div>
			<?php }
		}?>
	</div>
	<div style="height: 0.6em;"></div>
	Default doodles:&nbsp;&nbsp;&nbsp;
	<select class="eyeDrawDefaultDoodleSelect" data-attr-element="<?php echo $element_num?>" data-attr-field="<?php echo $field_num?>">
		<option value="">- Please select -</option>
		<?php foreach ($eyedraw_doodles as $class) {?>
			<option value="<?php echo $class?>"><?php echo $class?></option>
		<?php }?>
	</select><br/>
	<?php if (isset($this->form_errors['eyedrawDefaultDoodleSelect'.$element_num.'Field'.$field_num])) {?>
		<span style="color: #f00;"><?php echo $this->form_errors['eyedrawDefaultDoodleSelect'.$element_num.'Field'.$field_num]?></span><br/>
	<?php }?>
	<div style="height: 0.4em;"></div>
	<div class="selectedDefaultDoodles">
		<?php if (!empty($_POST['eyedrawDefaultDoodle'.$element_num.'Field'.$field_num])) {
			foreach ($_POST['eyedrawDefaultDoodle'.$element_num.'Field'.$field_num] as $doodle) {?>
				<div>
					<input type="hidden" name="eyedrawDefaultDoodle<?php echo $element_num?>Field<?php echo $field_num?>[]" value="<?php echo $doodle?>" /><?php echo $doodle?>
					<a href="#" class="removeDefaultDoodle">(remove)</a>
				</div>
			<?php }
		}?>
	</div>
	<div style="height: 0.6em;"></div>
	Size: <input type="text" class="noreturn" name="eyedrawSize<?php echo $element_num?>Field<?php echo $field_num?>" value="<?php echo @$_POST['eyedrawSize'.$element_num.'Field'.$field_num]?>" /><br/>
	<?php if (isset($this->form_errors['eyedrawSize'.$element_num.'Field'.$field_num])) {?>
		<span style="color: #f00;"><?php echo $this->form_errors['eyedrawSize'.$element_num.'Field'.$field_num]?></span><br/>
	<?php }?>
</div>
