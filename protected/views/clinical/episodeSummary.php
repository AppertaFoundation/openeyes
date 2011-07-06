<h3>Episode Summary</h3>
<div class="data_row">
	<div class="data_label">Start date:</div>
	<div class="date_value"><?php echo date('jS F, Y', strtotime($episode->start_date)); ?></div>
</div>
<div class="data_row">
	<div class="data_label">End date:</div>
	<div class="data_value"><?php echo !empty($episode->end_date) ? $episode->end_date : '(still open)'; ?></div>
</div>
<div class="data_row">
	<div class="data_label">Specialty:</div>
	<div class="data_value"><?php echo $episode->firm->serviceSpecialtyAssignment->specialty->name; ?></div>
</div>
<div class="data_row">
	<div class="data_label">Consultant firm:</div>
	<div class="data_value"><?php echo $episode->firm->name; ?></div>
</div>
<div class="data_row">
	<div class="data_label">Principle eye:</div>
	<div class="data_value">Where will this be fetched from?</div>
</div>
<div class="data_row">
	<div class="data_label">Principle diagnosis:</div>
	<div class="data_value">Where will this be fetched from?</div>
</div>
<div class="data_row">
	<div class="data_label">Care pathway:</div>
	<div class="data_value">Where will this be fetched from?</div>
</div>
<div class="clear"><p/></div>

<?php

try {
	echo $this->renderPartial(
		'/clinical/episodeSummaries/' . $episode->firm->serviceSpecialtyAssignment->specialty_id,
		array('episode' => $episode)
	);
} catch (Exception $e) {
	// If there is no extra episode summary detail page for this specialty we don't care
}
?>
<script type="text/javascript">
	$('ul.events li a').live('click', function() {
		$.ajax({
			url: $(this).attr('href'),
			success: function(data) {
				$('#episodes_details').html(data);
			}
		});
		return false;
	});
	$('.episode span.title').live('click', function() {
		var id = $(this).children('input').val();
		$.ajax({
			url: '<?php echo Yii::app()->createUrl('clinical/episodeSummary'); ?>',
			type: 'GET',
			data: {'id': id},
			success: function(data) {
				$('#episodes_details').html(data);
			}
		});
		return false;
	});
</script>
