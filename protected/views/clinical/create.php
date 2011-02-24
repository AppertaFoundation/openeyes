<?php

$this->renderPartial('base');

$form = $this->beginWidget('CActiveForm', array(
    'id'=>'clinical-create',
    'enableAjaxValidation'=>false,
));

echo CHtml::hiddenField('action', 'create');
echo CHtml::hiddenField('event_type_id', $eventTypeId);

/**
 * Loop through all the possible element types and display
 */
foreach ($siteElementTypeObjects as $siteElementType) {
	$className = $siteElementType->possibleElementType->elementType->class_name;
	// @todo - this shouldn't be here
	$element = new $className;

	echo $this->renderPartial(
		'/elements/' .
			$siteElementType->possibleElementType->elementType->class_name .
			'/_form/' .
			$siteElementType->view_number,
		array('model' => $element, 'form' => $form)
	);
}

echo CHtml::submitButton('Create event');

$this->endWidget();