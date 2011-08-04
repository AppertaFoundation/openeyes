<?php
if (!empty($episode)) { ?>
<h3>Episode Summary</h3>
<div class="col_left">Start date:<br/>
<span><?php echo date('jS F, Y', strtotime($episode->start_date)); ?></span>
</div>
<div class="col_right">Principal eye:<br/>
<span>Where will this be fetched from?</span>
</div>
<div class="col_left">End date:<br/>
<span><?php echo !empty($episode->end_date) ? $episode->end_date : '(still open)'; ?></span>
</div>
<div class="col_right">Principle diagnosis:<br/>
<span>Where will this be fetched from?</span>
</div>
<div class="col_left">Specialty:<br/>
<span><?php echo $episode->firm->serviceSpecialtyAssignment->specialty->name; ?></span>
</div>
<div class="col_right">Care pathway:<br/>
<span>Where will this be fetched from?</span>
</div>
<div class="col_left">Consultant firm:<br/>
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
}
?>