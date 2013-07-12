<script type="text/javascript" src="<?php echo Yii::app()->createUrl('js/gii.js')?>"></script>
<?php
$dh = opendir("js");
while ($file = readdir($dh)) {
	if (preg_match('/^gii-.*\.js$/',$file)) {?>
<script type="text/javascript" src="<?php echo Yii::app()->createUrl('js/'.$file)?>"></script>
<?php } }
closedir($dh);
?>

<h1>Event type module Generator</h1>

<p>This generator helps you to generate the skeleton code needed by an OpenEyes event type module.</p>

<?php $form=$this->beginWidget('BaseGiiEventTypeCActiveForm', array('model'=>$model)); ?>
	<input type="radio" id="EventTypeModuleModeRadioGenerateNew" class="EventTypeModuleMode" name="EventTypeModuleMode" value="0"<?php if (empty($_POST) || @$_POST['EventTypeModuleMode'] == 0) {?> checked="checked"<?php }?> /> Generate new&nbsp;&nbsp;
	<input type="radio" id="EventTypeModuleModeRadioModifyExisting" class="EventTypeModuleMode" name="EventTypeModuleMode" value="1"<?php if (@$_POST['EventTypeModuleMode'] == 1) {?> checked="checked"<?php }?> /> Modify existing
	<input type="hidden" id="has_errors" value="<?php echo empty($this->form_errors) ? '0' : '1'?>" />

	<div class="row" id="EventTypeModuleGenerateDiv">
		<?php if (@$_POST['EventTypeModuleMode'] == 1) {?>
			<?php echo $this->renderPartial('EventTypeModuleGenerate_ModifyExisting')?>
		<?php } else {?>
			<?php echo $this->renderPartial('EventTypeModuleGenerate_GenerateNew')?>
		<?php }?>
	</div>
<?php $this->endWidget(); ?>
<script type="text/javascript">
	var baseUrl = '<?php echo Yii::app()->baseUrl?>';
</script>
