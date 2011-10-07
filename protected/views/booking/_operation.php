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

Yii::app()->clientScript->scriptMap['jquery.js'] = false; ?>
<div class="view">
	<strong>Eye(s) to be operated on:</strong>
	<?php echo CHtml::encode($operation->getEyeText()); ?>
</div>
<div class="view">
	<strong>Procedure(s) entered:</strong>
<?php
	$procedures = '';
	if (!empty($operation->procedures)) {
		foreach ($operation->procedures as $procedure) {
			$procedures .= $procedure->term;
			$procedures .= ', ';
		}
		$procedures = substr($procedures, 0, strlen($procedures) - 2);
	}
	echo $procedures; ?>
</div>
<div class="view">
	<strong>Consultant required?</strong>
	<?php echo CHtml::encode($operation->getBooleanText('consultant_required')); ?>
</div>
<div class="view">
	<strong>Anaesthetic required:</strong>
	<?php echo CHtml::encode($operation->getAnaestheticText()); ?>
</div>
<div class="view">
	<strong>Overnight stay required?</strong>
	<?php echo CHtml::encode($operation->getBooleanText('overnight_stay')); ?>
</div>
<div class="view">
	<strong>Comments:</strong>
	<?php echo CHtml::encode($operation->comments); ?>
</div>