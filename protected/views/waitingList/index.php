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


Yii::app()->clientScript->registerCoreScript('jquery');

?>
<h3 class="title">Waiting list</h3>

<div id="theatreList">
<?php

if (empty($operations)) { ?>
<h2 class="theatre">The waiting list for this service is empty.</h2>
<?php
} else {
?>
    <table>
    <tr>
        <th class="repeat leftAlign">Patient</th>
	<th class="repeat leftAlign">Hosnum</th>
	<th class="repeat leftAlign">Procedures</th>
	<th class="repeat leftAlign">Eye</th>
	<th class="repeat leftAlign">Consultant</th>
	<th class="repeat leftAlign">Decision Date</th>
	<th class="repeat leftAlign">Book Status</th>
    </tr>
<?php
        foreach ($operations as $id => $operation) {
		$eo = ElementOperation::model()->findByPk($operation['eoid']);
		$consultant = $eo->event->episode->firm->getConsultant();
		$user = $consultant->contact->userContactAssignment->user;
?>
    <tr>
        <td class="patient leftAlign">
<?php
        echo CHtml::link(
                $operation['first_name'] . ' ' . $operation['last_name'],
                Yii::app()->createUrl('patient/view', array(
                                        'id' => $operation['pid'],
					'tabId' => 1,
					'eventId' => $operation['evid']
                ))
        );
?>
</td><td>
<?php echo $operation['hos_num'] ?>
</td><td>
<?php echo $operation['List'] ?>
</td><td>
<?php echo $eo->getEyeText() ?>
</td><td>
<?php echo $user->title . ' ' . $user->first_name . ' ' . $user->last_name ?>
</td><td>
<?php echo $eo->convertDate($eo->decision_date) ?>
</td><td>
<?php echo $eo->getStatusText() ?>
</td></tr>
<?php
        }
?>
</table>
<?php
}
?>
</div>
