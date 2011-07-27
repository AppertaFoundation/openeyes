<?php
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl.'/js/phrase.js');
Yii::app()->clientScript->registerCoreScript('jquery');

$form = $this->beginWidget('CActiveForm', array(
    'id'=>'clinical-update',
    'enableAjaxValidation'=>false,
	'htmlOptions'=> array('class'=>'sliding')
));

echo CHtml::hiddenField('action', 'update');
echo CHtml::hiddenField('event_id', $id);

/**
 * Loop through all the possible element types and display
 */
foreach ($elements as $element) {
	$elementClassName = get_class($element);

	echo $this->renderPartial(
		'/elements/' . $elementClassName . '/_form/' .
			$element->viewNumber,
		array('model' => $element, 'form' => $form, 'specialties' => $specialties, 
			'patient' => $patient)
	);
} ?>
<div class="cleartall"></div>
<?php
if (EyeDrawService::getActive()) { ?>
<button type="submit" value="submit" class="shinybutton highlighted" style="float: right; margin-right: 70px;" onClick="javascript: eyedraw_submit();"><span>Update</span></button>
<?php
} else { ?>
<button type="submit" value="submit" class="shinybutton highlighted" style="float: right; margin-right: 70px;"><span>Update</span></button>
<?php
}

$this->endWidget();
