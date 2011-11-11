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


if (!empty($episode)) {
	$diagnosis = $episode->getPrincipalDiagnosis();

	if (empty($diagnosis)) {
					$eye = 'No diagnosis';
					$text = 'No diagnosis';
	} else {
					$eye = $diagnosis->getEyeText();
					$text = $diagnosis->disorder->term;
	}
?>
<h3>Episode Summary (<?php echo $episode->firm->serviceSpecialtyAssignment->specialty->name?>)</h3>

<h4>Start date:</h4>
<div class="eventHighlight">
	<h4><?php echo date('d M Y', strtotime($episode->start_date))?></h4>
</div>

<h4>Principal eye:</h4>
<div class="eventHighlight">
	<h4><?php echo $eye?></h4>
</div>

<h4>End date:</h4>
<div class="eventHighlight">
	<h4><?php echo !empty($episode->end_date) ? $episode->end_date : '(still open)'?></h4>
</div>

<h4>Principal diagnosis:</h4>
<div class="eventHighlight">
	<h4><?php echo $text?></h4>
</div>

<h4>Specialty:</h4>
<div class="eventHighlight">
	<h4><?php echo $episode->firm->serviceSpecialtyAssignment->specialty->name?></h4>
</div>

<h4>Consultant firm:</h4>
<div class="eventHighlight">
	<h4><?php echo $episode->firm->name?></h4>
</div>
<?php
	try {
		echo $this->renderPartial(
			'/clinical/episodeSummaries/' . $episode->firm->serviceSpecialtyAssignment->specialty_id,
			array('episode' => $episode)
		);
	} catch (Exception $e) {
		// If there is no extra episode summary detail page for this specialty we don't care
	}
} else {
	// hide the episode border ?>
<script type="text/javascript">
	$('div#episodes_details').hide();
</script>
<?php
} ?>
<script type="text/javascript">
	$('#closelink').click(function() {
		$('#dialog-confirm').dialog({
			resizable: false,
			height: 140,
			modal: false,
			buttons: {
				"Close episode": function() {
					$.ajax({
						url: $('#closelink').attr('href'),
						type: 'GET',
						success: function(data) {
							$('#episodes_details').show();
							$('#episodes_details').html(data);
						}
					});
					$(this).dialog('close');
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			},
				open: function() {
					$(this).parents('.ui-dialog-buttonpane button:eq(1)').focus();
				}
		});
		return false;
	});
	</script>
</script>

<?php if (empty($episode->end_date)) {?>
	<div style="margin-top:40px; text-align:right; position:relative; ">
		<button id="close-episode" type="submit" value="submit" class="wBtn_close-episode ir">Close Episode</button>

		<div id="close-episode-popup" class="popup red" style="display: none;">
			<p style="text-align:left;">You are closing this episode. This can not be undone. Once an episode is closed it can not be re-opened.</p>
			<p><strong>Are you sure?</strong></p>
			<div class="action_options">
				<span class="aBtn"><a id="yes-close-episode" href="#"><strong>Yes, I am</strong></a></span>
				<span class="aBtn"><a id="no-close-episode" href="#"><strong>No, cancel this.</strong></a></span>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$('#close-episode').unbind('click').click(function(e) {
			e.preventDefault();
			$('#close-episode-popup').slideToggle(100);
			return false;
		});

		$('#no-close-episode').unbind('click').click(function(e) {
			e.preventDefault();
			$('#close-episode-popup').slideToggle(100);
			return false;
		});

		$('#yes-close-episode').unbind('click').click(function(e) {
			e.preventDefault();
			$('#close-episode-popup').slideToggle(100);
			$.ajax({
				url: '/clinical/closeepisode/<?php echo $episode->id?>',
				success: function(data) {
					$('#event_content').html(data);
					return false;
				}
			});

			return false;
		});
	</script>
<?php }?>
