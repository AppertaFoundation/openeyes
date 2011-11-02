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

if (!empty($operation->booking)) {
	$session = $operation->booking->session;

	if (isset($session->firm)) {
		$firmName = $session->firm->name . ' (' . $session->firm->serviceSpecialtyAssignment->service->name . ')';
	} else {
		$firmName = 'Emergency List';
	}

	$theatre = $session->sequence->theatre;
?>
                <div class="data">

                        <span style="display:inline-block; width:160px;">Firm:</span><strong><?php echo CHtml::encode($firmName); ?></strong><br>
                        <span style="display:inline-block; width:160px;">Location:</span><strong><?php echo CHtml::encode($theatre->site->name) . ' - ' . CHtml::encode($theatre->name); ?></strong><br>
                        <span style="display:inline-block; width:160px;">Date of operation:</span><strong><?php echo date('F j, Y', strtotime($session->date)); ?></strong><br>
                        <span style="display:inline-block; width:160px;">Session time:</span><strong><?php echo substr($session->start_time, 0, 5) . ' - ' . substr($session->end_time, 0, 5); ?></strong><br>
                        <span style="display:inline-block; width:160px;">Admission time:</span><strong><?php echo substr($operation->booking->admission_time, 0, 5); ?></strong> <br>

                        <span style="display:inline-block; width:160px;">Duration of operation:</span><strong><?php echo $operation->total_duration . ' minutes'; ?></strong>
                </div>
<?php
}
?>
