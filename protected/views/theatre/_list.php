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

//$baseUrl = Yii::app()->baseUrl;
//$cs = Yii::app()->getClientScript();
//$cs->registerCoreScript('jquery');
//$cs->registerCoreScript('jquery.ui');
//$cs->registerCoreScript('jquery.printElement.min');
//$cs->registerCSSFile('/css/jqueryui/theme/jquery-ui.css', 'all');

if (empty($theatres)) {?>
	<p class="fullBox"><strong>No theatre schedules match your search criteria.</strong></p>
<?php } else {
	$panels = array();

	$firstTheatreShown = false;
	foreach ($theatres as $name => $dates) { ?>
		<h3 class="theatre<?php if (!$firstTheatreShown) {?> firstTheatre<?php }?>"><strong><?php echo $name?></strong></h3>
		<?php
		$firstTheatreShown = true;
		foreach ($dates as $date => $sessions) {
			$timestamp = strtotime($date);?>
<?php
			$previousSequenceId = '';
			$timeAvailable = $sessions[0]['sessionDuration'];
			foreach ($sessions as $session) {
				if ($previousSequenceId != $session['sequenceId']) {
					if ($previousSequenceId != '') {
?>
                                                        <tr>
                                                                <th colspan="7" class="footer">Time unallocated: <span><?php echo $timeAvailable ?> min</span></th>
                                                        </tr>
                                                </tbody>
                                        </table>
                                </div>
<?php
					}

?>
<h3 class="sessionDetails"><span class="date"><strong><?php echo date('d M',$timestamp)?></strong> <?php echo date('Y',$timestamp)?></span> - <strong><span class="day"><?php echo date('l',$timestamp)?></span>, <span class="time"><?php echo substr($session['startTime'], 0, 5)?> - <?php echo substr($session['endTime'], 0, 5)?></span></strong> for <?php echo !empty($session['firn_name']) ? $session['firm_name'] : 'Emergency List' ?> <?php echo !empty($session['specialty_name']) ? 'for (' . $session['specialty_name'] . ')' : '' ?> </h3>
                                <div class="theatre-sessions whiteBox clearfix">

                                                <div class="sessionComments" style="display:block; float:right; width:300px; ">
                                                        <form>
                                                                <textarea rows="2" style="width:295px;" id="comments<?php echo $session['sessionId'] ?>"><?php echo $session['comments'] ?></textarea>
                                                        </form>
                                                        <div class="modifyComments"><span class="edit"><a href="#" id="editComments<?php echo $session['sessionId'] ?>" name="<?php echo $session['sessionId'] ?>">Edit comment</a></span></div>
                                                </div>

                                        <table>
                                                <tbody>
                                                        <tr>
                                                                <th>Admit time</th>
                                                                <th>Hospital #</th>
                                                                <th>Patient (Age)</th>
                                                                <th>[Eye] Operation</th>
                                                                <th>Ward</th>
                                                                <th>Alerts</th>
                                                        </tr>
<?php
					$previousSequenceId = $session['sequenceId'];
					$timeAvailable = $session['sessionDuration'];
				}

				if (!empty($session['patientId'])) {
					$timeAvailable -= $session['operationDuration'];
?>
							<tr>
								<td class="session"><?php echo substr($session['admissionTime'], 0, 5)?></td>
								<td class="hospital"><?php echo CHtml::link(
                							$session['patientHosNum'],
									'/patient/episodes/' . $session['patientId'] . '/event/' . $session['eventId']
							       	);
								?></td>
								<td class="patient leftAlign"><?php echo $session['patientName'] . ' (' . $session['patientAge'] . ')'; ?></td>
								<td class="operation leftAlign"><?php echo !empty($session['procedures']) ? '['.$session['eye'].'] '.$session['procedures'] : 'No procedures'?></td>
								<td class="ward"><?php echo $session['ward']; ?></td>
								<td class="alerts">
								<?php
					if ($session['patientGender'] == 'M') {
?>
<img src="/img/_elements/icons/alerts/male.png" alt="male" width="17" height="17" />
<?php
					} else {
?>
<img src="/img/_elements/icons/alerts/female.png" alt="female" width="17" height="17" />
<?php
					}

					if (!empty($session['operationComments']) && preg_match('/\w/', $session['operationComments'])) {
							?><img src="/img/_elements/icons/alerts/comment.png" alt="<?php echo htmlentities($session['operationComments']) ?>" title="<?php echo htmlentities($session['operationComments']) ?>" width="17" height="17" />
<?php
					}

                                	if (!empty($session['overnightStay'])) {
                                                        ?><img src="/img/_elements/icons/alerts/overnight.png" alt="Overnight stay required" width="17" height="17" />
<?php
                                	}

                                	if (!empty($session['consultantRequired'])) {
                                                        ?><img src="/img/_elements/icons/alerts/consultant.png" alt="Consultant required" width="17" height="17" />
<?php
                                	}
				}
?>
							</td>
							</tr>
<?php
			}
?>
							<tr>
								<th colspan="7" class="footer">Time unallocated: <span><?php echo $timeAvailable ?> min</span></th>
							</tr>
						</tbody>
					</table>
				</div>
<?php
		}
	}
}
?>

<div id="printable" style="display: none;">
OPERATION LIST FORM
<br /><br />
<?php

$previousSequenceId = '';

foreach ($theatres as $name => $dates) {
        $firstTheatreShown = false;
        foreach ($theatres as $name => $dates) {
                $firstTheatreShown = true;
                foreach ($dates as $date => $sessions) {
                        foreach ($sessions as $session) {
                                if ($previousSequenceId != $session['sequenceId']) {
                                        if ($previousSequenceId != '') {
?>
</table>
<?php
                                        }
?>
<table>
<tr><td>
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
</table>

<table>
<tr><td>
SURGICAL FIRM: <?php echo htmlspecialchars($session['firm_name'], ENT_QUOTES) ?>
</td><td>
ANAESTHETIST:
</td><td>
&nbsp;
</td><td>
DATE:
</td><td>
<?php echo date('Y/m/d') ?>
</td></tr>
</table>

<table>
<tbody>
<tr>
<th>HOSPT NO</th>
<th>PATIENT</th>
<th>AGE</th>
<th>WARD</th>
<th>GA or LA</th>
<th>PROCEDURES AND COMMENTS</th>
<th>ADMISSION TIME</th>
</tr>
<?php
                                        $previousSequenceId = $session['sequenceId'];
                                        $timeAvailable = $session['sessionDuration'];
                                }

                                if (!empty($session['patientId'])) {
                                        $timeAvailable -= $session['operationDuration'];
?>
<tr>
<td><?php echo $session['patientHosNum'] ?></td>
<td><?php echo htmlspecialchars($session['patientName']) ?></td>
<td><?php echo htmlspecialchars($session['patientAge']) ?></td>
<td><?php echo htmlspecialchars($session['ward']) ?></td>
<td><?php echo htmlspecialchars($session['anaesthetic']) ?></td>
<td>
<?php echo !empty($session['procedures']) ? '['.$session['eye'].'] '.htmlspecialchars($session['procedures']) : 'No procedures'?><br />
<?php echo htmlspecialchars($session['operationComments']) ?>
<td><?php echo $session['admissionTime'] ?></td>
</td></tr>
<?php
                                }
                        }
                }
        }
}
?>
</div>

<script type="text/javascript">
    $('a[id^="editComments"]').click(function() {
        id = this.name;
        value = $('#comments' + this.name).val();

        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('theatre/updateSessionComments'); ?>',
            'type': 'POST',
            'data': 'id=' + id + '&comments=' + value,
            'success': function(data) {
                return true;
            }
        });

        return true;
    });
</script>
