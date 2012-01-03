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

Yii::app()->clientScript->scriptMap['jquery.js'] = false;
Yii::app()->clientScript->scriptMap['jquery-ui.min.js'] = false;

?>

<div id="theatres">
	<h4>Select a session time:</h4>
	<div id="theatre-times">
<?php

$i = 0;
foreach ($theatres as $name => $sessions) {
?>
		<h5><?php echo $name ?></h5>
		<div id="theatre-times_tab_<?php echo $i ?>" class="sessionTimes">
<?php
	foreach ($sessions as $session) {
		$startTime = substr($session['start_time'], 0, 5);
		$endTime = substr($session['end_time'], 0, 5);
		if ($session['time_available'] >= 0) {
			$status = 'available';
			$class = ' available';
		} else {
			$status = 'overbooked';
			$class = '';
		}
?>
			<div class="timeBlock<?php echo $class ?>" id="bookingSession<?php echo $session['id'] ?>">
				<span class="time"><?php echo $startTime ?> - <?php echo $endTime ?></span>
				<span class="available">(<?php echo abs($session['time_available']) ?> min <?php echo $status ?>)</span>
				<span class="session_id"><?php echo $session['id'] ?></span>
			</div>
<?php
	}
?>
		</div> <!-- #theatre-times_tab_<?php echo $i ?> -->
<?php

	$i++;
}
?>
	</div> <!-- #theatre-times -->
</div> <!-- #theatres -->

<div id="sessionDetails">
</div>

<script type="text/javascript">
	$('div[id^="bookingSession"]').unbind('click').click(function() {
		id = this.id;
		id = id.replace(/bookingSession/,'');

        $.ajax({
            'url': '/booking/list/operation/<?php echo $operation->id ?>/session/' + id,
            'type': 'POST',
            'data': 'operation=<?php echo $operation->id ?>&session=' + id + '&reschedule=<?php echo $reschedule ?>',
            'success': function(data) {
				$('#sessionDetails').html('');
				$('#sessionDetails').html(data);
				$('#sessionDetails').show();
            }
        });

        return true;
	});
</script>
