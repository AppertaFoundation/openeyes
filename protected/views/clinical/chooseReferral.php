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

?><h1>Choose Referral</h1>

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
