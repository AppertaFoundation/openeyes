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

	echo $this->renderPartial(
		'/elements/' . $className . '/_form/' .
			$element['siteElementType']->view_number,
		array('model' => $element['element'], 'form' => $form)
	);
}

if (EyeDrawService::getActive()) {
        echo CHtml::submitButton('Update event', array('onClick' => 'eyedraw_submit();'));
} else {
        echo CHtml::submitButton('Update event');
}

$this->endWidget();
