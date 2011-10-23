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
$session = $operation->booking->session;

if (isset($session->firm)) {
	$firmName = $session->firm->name . ' (' . $session->firm->serviceSpecialtyAssignment->service->name . ')';
} else {
	$firmName = 'Emergency List';
}

$theatre = $session->sequence->theatre; ?>
<strong>Firm:</strong> <?php echo CHtml::encode($firmName); ?><br />
<strong>Location:</strong> <?php echo CHtml::encode($theatre->site->name) . ' - ' . CHtml::encode($theatre->name); ?><br />
<strong>Date of operation:</strong> <?php echo date('F j, Y', strtotime($session->date)); ?><br />
<strong>Session time:</strong> <?php echo substr($session->start_time, 0, 5) . ' - ' . substr($session->end_time, 0, 5); ?><br />
<strong>Admission time:</strong> <?php echo substr($operation->booking->admission_time, 0, 5); ?><br />
<strong>Duration of operation:</strong> <?php echo $operation->total_duration . ' minutes'; ?>