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
			<option value="<?php echo $class?>"><?php echo $class?></option>
		<?php }?>
	</select><br/>
	Size: <input type="text" name="eyedrawSize<?php echo $element_num?>Field<?php echo $field_num?>" value="" /><br/><br/>
</div>
