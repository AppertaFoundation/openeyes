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

$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
//$cs->registerCSSFile('/css/theatre.css', 'all');
$cs->registerCoreScript('jquery');
$cs->registerCoreScript('jquery.ui');
$cs->registerCSSFile('/css/jqueryui/theme/jquery-ui.css', 'all');
//$cs->registerScriptFile($baseUrl.'/js/jquery.multi-open-accordion-1.5.2.min.js');

if (empty($theatres)) {?>
	<p class="fullBox"><strong>No theatre schedules match your search criteria.</strong></p>
<?php } else {?>
	<p class="fullBox"><strong>Showing results for:&nbsp;</strong>"This Month", "Vitreoretinal Services", "Aylward Bill" </p>
<?php
	$panels = array();
	$firstTheatreShown = false;
	foreach ($theatres as $name => $dates) { ?>
		<h3 class="theatre<?php if (!$firstTheatreShown) {?> firstTheatre<?php }?>"><strong><?php echo $name?></strong></h3>
		<?php
		$firstTheatreShown = true;
		foreach ($dates as $date => $sessions) {
			foreach ($sessions as $session) {
				$timestamp = strtotime($date);?>
				<h3 class="sessionDetails"><span class="date"><strong><?php echo date('d M',$timestamp)?></strong> <?php echo date('Y',$timestamp)?></span> - <strong><span class="day"><?php echo date('l',$timestamp)?></span>, <span class="time"><?php echo substr($session['startTime'], 0, 5)?> - <?php echo substr($session['endTime'], 0, 5)?></span></strong> for Bill Aylward (Vitreoretinal) </h3>
				<div class="theatre-sessions whiteBox clearfix">
					<div class="sessionComments" style="display:block; float:right; width:300px; ">Patient has an Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. - Bill.
						<div class="modifyComments"><span class="date">16:00 16/10/11</span><span class="edit"><a href="#">Edit</a></span></div>
					</div>

					<table>
						<tbody>
							<tr>
								<th>Start</th>
								<th>Hospital #</th>
								<th>Patient (Age)</th>
								<th>[Eye] Operation</th>
								<th>Ward</th>
								<th>Alerts</th>
							</tr>
							<tr>
								<td class="session"><?php echo substr($session['startTime'], 0, 5)?></td>
								<td class="hospital">1001234</td>
								<td class="patient leftAlign">Chay Close (91)</td>
								<td class="operation leftAlign"> [R] Phako/IOL (GA)</td>
								<td class="ward">Sedgwick</td>
								<td class="alerts"><img src="img/_elements/icons/alerts/female.png" alt="female" width="17" height="17" /><img src="img/_elements/icons/alerts/comment.png" alt="comment" width="17" height="17" /></td>
							</tr>
							<tr>
								<th colspan="7" class="footer">Time unallocated: <span>225 min</span></th>
							</tr>
						</tbody>
					</table>
				</div>
			<?php }
		}
	}
}
?>
<script type="text/javascript">
	$('input[id^="editComments"]').click(function() {
		$.ajax({
			'url': '<?php echo Yii::app()->createUrl('waitingList/updateSessionComments'); ?>',
			'type': 'POST',
			'data': $('#commentsForm' + this.name).serialize(),
			'success': function(data) {
				return true;
			}
		});

		return true;
	});
</script>
