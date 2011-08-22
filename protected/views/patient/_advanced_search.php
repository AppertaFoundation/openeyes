<?php
echo '<div style="float: left;">';
echo CHtml::label('First name:', 'first_name');
echo CHtml::textField('Patient[first_name]', '', array('style'=>'width: 150px;', 'class' => 'topPaddingSmall'));

//echo '<p/>';
echo '<br />';

echo CHtml::label('Last name:', 'last_name');
echo CHtml::textField('Patient[last_name]', '', array('style'=>'width: 150px;', 'class' => 'topPadding'));

//echo '<p/>';
echo '<br />';

echo CHtml::label('Date of birth:', 'dob');
echo CHtml::textField('dob_day', '', array('size'=>2, 'maxlength'=>2, 'style'=>'width: 37px;', 'class' => 'topPadding'));
echo '/';
echo CHtml::textField('dob_month', '', array('size'=>2, 'maxlength'=>2, 'style'=>'width: 37px;', 'class' => 'topPadding'));
echo '/';
echo CHtml::textField('dob_year', '', array('size'=>4, 'maxlength'=>4, 'style'=>'width: 64px;', 'class' => 'topPadding'));

echo '</div>';

echo '<div style="padding-left: 300px;">';
echo CHtml::label('NHS #:', 'nhs_number');
echo CHtml::textField('Patient[nhs_num]', '', array('style' => 'width: 150px;', 'class' => 'topPaddingSmall'));

//echo '<div class="cleartall"></div>';

echo '<br />';

echo CHtml::label('Gender:', 'gender');
echo CHtml::radioButtonList('Patient[gender]', '', array('M'=>'Male','F'=>'Female'),
	array('separator'=>' &nbsp; ', 'class' => 'topPadding'));
echo '</div>';