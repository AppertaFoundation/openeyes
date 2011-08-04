<?php
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl.'/js/phrase.js');
Yii::app()->clientScript->registerCoreScript('jquery');

$form = $this->beginWidget('CActiveForm', array(
    'id'=>'clinical-create',
    'enableAjaxValidation'=>false,
	'htmlOptions' => array('class'=>'sliding')
));

echo CHtml::hiddenField('action', 'create');
echo CHtml::hiddenField('event_type_id', $eventTypeId);

/**
 * Loop through all the possible element types and display
 */

foreach ($elements as $element) {
	$elementClassName = get_class($element);

	echo $this->renderPartial(
		'/elements/' .
			$elementClassName .
			'/_form/' .
			$element->viewNumber,
		array('model' => $element, 'form' => $form, 'specialties' => $specialties, 
			'patient' => $patient)
	);

} ?>
<div class="cleartall"></div>
<?php
if (EyeDrawService::getActive()) { ?>
<button type="submit" value="submit" class="shinybutton highlighted" onClick="javascript: eyedraw_submit();"><span>Create</span></button>
<?php
} else { ?>
<button type="submit" value="submit" class="shinybutton highlighted"><span>Create</span></button>
<?php
}

$this->endWidget();
