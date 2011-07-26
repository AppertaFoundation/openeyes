<?php
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl.'/js/phrase.js');
Yii::app()->clientScript->registerCoreScript('jquery');

$form = $this->beginWidget('CActiveForm', array(
    'id'=>'clinical-update',
    'enableAjaxValidation'=>false,
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
		array('model' => $element, 'form' => $form, 'specialties' => $specialties)
	);
}

if (EyeDrawService::getActive()) {
	echo CHtml::submitButton('Update event', array('onClick' => 'eyedraw_submit();'));
} else {
	echo CHtml::submitButton('Update event');
}

$this->endWidget();
