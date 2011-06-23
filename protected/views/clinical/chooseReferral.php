<h1>Choose Referral</h1>

<?php

$this->renderPartial('base');

$form = $this->beginWidget('CActiveForm', array(
    'id'=>'clinical-chooseReferral',
    'enableAjaxValidation'=>false,
));

echo CHtml::hiddenField('action', 'chooseReferral');
echo CHtml::hiddenField('event_id', $id);
?>
Referral: <br />
<?php

echo CHtml::dropDownList('referral_id', null, $referrals);

?>
<br />
<?php

echo CHtml::submitButton('Choose Referral');

$this->endWidget();
