<script type="text/javascript" src="/js/gii.js"></script>

<h1>Event type module Generator</h1>

<p>This generator helps you to generate the skeleton code needed by an OpenEyes event type module.</p>

<?php $form=$this->beginWidget('BaseGiiEventTypeCActiveForm', array('model'=>$model)); ?>
	<input type="radio" id="EventTypeModuleModeRadioGenerateNew" class="EventTypeModuleMode" name="EventTypeModuleMode" value="0"<?php if (empty($_POST) || @$_POST['EventTypeModuleMode'] == 0) {?> checked="checked"<?php }?> /> Generate new&nbsp;&nbsp;
	<input type="radio" id="EventTypeModuleModeRadioModifyExisting" class="EventTypeModuleMode" name="EventTypeModuleMode" value="1"<?php if (@$_POST['EventTypeModuleMode'] == 1) {?> checked="checked"<?php }?> /> Modify existing

	<div class="row" id="EventTypeModuleGenerateDiv">
		<?php if (@$_POST['EventTypeModuleMode'] == 1) {?>
			<?php echo $this->renderPartial('EventTypeModuleGenerate_ModifyExisting')?>
		<?php }else{?>
			<?php echo $this->renderPartial('EventTypeModuleGenerate_GenerateNew')?>
		<?php }?>
	</div>
<?php $this->endWidget(); ?>
