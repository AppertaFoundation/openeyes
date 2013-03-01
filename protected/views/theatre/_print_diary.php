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
http://www.openeyes.org.uk	 info@openeyes.org.uk
--
*/
?>
<!-- ================================================ -->
<!-- * * * * * * * * * *   DIARY  * * * * * * * * * * -->
<!-- ================================================ -->

<div id="diaryTemplate">
<div id="d_title">OPERATION LIST FORM</div>

<?php
// TODO: References to sequences need to be removed when possible
$previousSequenceId = '';

$firstTheatreShown = false;
foreach ($theatres as $name => $dates) {
	$firstTheatreShown = true;
	foreach ($dates as $date => $sessions) {
		$timestamp = strtotime($date);
		foreach ($sessions as $session) {
			if ($previousSequenceId != $session['sequenceId'] || $previousDate != $date) {
				if ($previousSequenceId != '') {
?>
</table>
<div style="page-break-after:always"></div>
<?php
}
?>
<table class="d_overview">
<tbody><tr><td>
THEATRE NO:
</td><td colspan="2">
<?php echo(htmlspecialchars($name, ENT_QUOTES)) ?>
</td></tr>
<tr><td>
SESSION:
</td><td>
<?php echo substr($session['startTime'], 0, 5)?> - <?php echo substr($session['endTime'], 0, 5) ?>
</td><td>
NHS
</td></tr>
</tbody></table>

<table class="d_overview">
<tbody><tr><td>
SURGICAL FIRM: <?php echo empty($session['firm_name']) ? 'Emergency list' : htmlspecialchars($session['firm_name'], ENT_QUOTES) ?>
</td><td>
ANAESTHETIST:
</td><td>
&nbsp;
</td><td>
DATE:
</td><td>
<?php echo Helper::convertDate2NHS($date); ?>
</td></tr>
<tr><td>COMMENTS: <?php echo htmlspecialchars($session['comments'])?></td></tr>
</tbody></table>

<table class="d_data">
<tbody>
<tr>
<th>HOSPT NO</th>
<th>PATIENT</th>
<th>AGE</th>
<th>WARD</th>
<th>GA or LA</th>
<th>PRIORITY</th>
<th>PROCEDURES AND COMMENTS</th>
<th>ADMISSION TIME</th>
</tr>
<?php
				}

								$previousSequenceId = $session['sequenceId'];
								$previousDate = $date;

				if (!empty($session['patientId'])) {
?>
<tr>
<td><?php echo $session['patientHosNum'] ?></td>
<td><?php echo $session['patientName'] ?></td>
<td><?php echo htmlspecialchars($session['patientAge']) ?></td>
<td><?php echo htmlspecialchars($session['ward']) ?></td>
<td><?php echo htmlspecialchars($session['anaesthetic']) ?></td>
<td><?php echo $session['priority']?></td>
<td style="max-width: 500px; word-wrap:break-word; overflow: hidden;">
<?php 
	$eye_string = $session['eye_long']. ' eye';
	if(strtolower($session['eye_long']) == 'both') $eye_string .= 's'; 
?>
<?php echo !empty($session['procedures']) ? '['.$eye_string.'] '.htmlspecialchars($session['procedures_long']) : 'No procedures'?><br />
<?php echo htmlspecialchars($session['operationComments']) ?>
<td><?php echo $session['admissionTime'] ?></td>
</tr>
<?php
			}
		}
	}
}
?>
</table>
</div> <!-- #diaryTemplate -->
<!-- ================================================ -->

<!-- * * * * * * * * end of DIARY  * * * * * * * * * -->
<!-- ================================================ -->
<div style="page-break-after:always"></div>
