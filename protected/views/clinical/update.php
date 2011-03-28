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
	$className = get_class($element['element']);
	$partialParams = array('model' => $element['element'], 'form' => $form);
	if ('ElementIntraocularPressure' == $className) {
		$partialParams['values'] = $iopValues;
		$partialParams['right_iop'] = $element['element']->right_iop;
		$partialParams['left_iop'] = $element['element']->left_iop;
	}

	echo $this->renderPartial(
		'/elements/' . $className . '/_form/' .
			$element['siteElementType']->view_number,
		$partialParams
	);
}

echo CHtml::submitButton('Update event');

$this->endWidget();