<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

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