<?php
echo '<div style="float: left;">';
echo CHtml::label('First name:', 'first_name');
echo CHtml::textField('first_name');

echo '<p/>';

echo CHtml::label('Last name:', 'last_name');
echo CHtml::textField('last_name');

echo '<p/>';

echo CHtml::label('Date of birth:', 'dob');
echo CHtml::textField('dob_day', '', array('size'=>2, 'maxlength'=>2));
echo '/';
echo CHtml::textField('dob_month', '', array('size'=>2, 'maxlength'=>2));
echo '/';
echo CHtml::textField('dob_year', '', array('size'=>4, 'maxlength'=>4));

echo '</div>';

echo '<div style="padding-left: 300px;">';
echo CHtml::label('NHS #:', 'nhs_number');
echo CHtml::textField('nhs_number', '', array('style' => 'width: 150px;'));

echo '<p/>';

echo CHtml::label('Gender:', 'gender');
echo CHtml::radioButtonList('gender', '', array(0=>'Male',1=>'Female'), 
	array('separator'=>' &nbsp; '));
echo '</div>';
?>