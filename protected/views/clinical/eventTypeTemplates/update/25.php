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
}

// Display referral select box if required
if (isset($referrals) && is_array($referrals)) {
        // There is at least on referral, so include it/them
        if (count($referrals) > 1) {
                // Display a list of referrals for the user to choose from
?>
<div class="box_grey_big_gradient_top"></div>
<div class="box_grey_big_gradient_bottom">
                <span class="referral_red">There is more than one open referral that could apply to this event.</span><p />
                <label for="referral_id">Select the referral that applies to this event:</label>
<?php
                echo CHtml::dropDownList('referral_id', '', CHtml::listData($referrals, 'id', 'id'));
?>
</div>
<?php
        }
} ?>
<div class="cleartall"></div>
<button type="submit" value="submit" class="shinybutton highlighted"><span>Edit operation</span></button>
<?php

$this->endWidget();
