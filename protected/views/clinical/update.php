<?php

$this->renderPartial('base');

$form = $this->beginWidget('CActiveForm', array(
    'id'=>'clinical-create',
    'enableAjaxValidation'=>false,
));

echo CHtml::hiddenField('action', 'update');
echo CHtml::hiddenField('event_id', $id);

/**
 * Loop through all the possible element types and display
 */
foreach ($elements as $element) {
	echo $this->renderPartial(
		'/elements/' .
			get_class($element['element']).
			'/_form/' .
			$element['siteElementType']->view_number,
		array('model' => $element['element'], 'form' => $form)
	);
}

echo CHtml::submitButton('Update event');

$this->endWidget();