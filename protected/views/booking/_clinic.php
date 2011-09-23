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


Yii::app()->clientScript->scriptMap['jquery.js'] = false;
$firm = $operation->event->episode->firm; ?>
<div class="view">
	<strong>Service:</strong>
	<?php echo CHtml::encode($firm->serviceSpecialtyAssignment->service->name); ?>
</div>
<div class="view">
	<strong>Firm:</strong>
	<?php echo CHtml::encode($firm->name); ?>
</div>
<?php
if (!empty($operation->booking)) {
	$theatre = $operation->booking->session->sequence->theatre; ?>
<div class="view">
	<strong>Location:</strong>
	<?php echo CHtml::encode($theatre->site->name) . ' - ' . 
		CHtml::encode($theatre->name); ?>
</div>
<?php	
} ?>
<!--div class="view">
	<strong>Referral date:</strong>
</div-->
<!--div class="view">
	<strong>Clinic date:</strong>
</div>
<div class="view">
	<strong>PCT Clinical pathway:</strong>
</div>
<div class="view">
	<strong>Diagnosis:</strong>
</div-->
