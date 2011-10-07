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
<h3>Episode Summary</h3>
<?php
	if ($editable && empty($episode->end_date)) {
		echo CHtml::link('<span>Close Episode</span>', array('clinical/closeEpisode', 'id' => $episode->id), array('id' => 'closelink', 'class' => 'shinybutton', 'encode' => false, 'style' => 'float: right; margin-top: -30px;')); ?>
		<div id="dialog-confirm" title="Close this episode?" style="display: none;">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This episode will be marked as closed, and no further events will be added to it. Are you sure?</p>
</div><?php
	} ?>
<div class="col_left">Start date:<br/>
<span><?php echo date('jS F, Y', strtotime($episode->start_date)); ?></span>
</div>
<div class="col_right">Principal eye:<br/>
<span><?php echo $eye ?></span>
</div>
<div class="col_left">End date:<br/>
<span><?php echo !empty($episode->end_date) ? date('jS F, Y', strtotime($episode->end_date)) : '(still open)'; ?></span>
</div>
<div class="col_right">Principal diagnosis:<br/>
<span><?php echo $text ?></span>
</div>
<div class="col_left">Specialty:<br/>
<span><?php echo $episode->firm->serviceSpecialtyAssignment->specialty->name; ?></span>
</div>
<div class="col_right">Consultant firm:<br/>
<span><?php echo $episode->firm->name; ?></span>
</div>
<div class="col_right">&nbsp;<br/>&nbsp;</div>
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