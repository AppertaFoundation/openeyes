
<script language="javascript">

elements = new Array(
	'procedure_id',
	'ElementOperation_eye_0',
	'ElementOperation_eye_1',
	'ElementOperation_eye_2',
	'ElementOperation_total_duration',
	'decision_date_day',
	'decision_date_month',
	'decision_date_year',
	'ElementOperation_consultant_required_0',
	'ElementOperation_consultant_required_1',
	'ElementOperation_anaesthetic_type_0',
	'ElementOperation_anaesthetic_type_1',
	'ElementOperation_anaesthetic_type_2',
	'ElementOperation_anaesthetic_type_3',
	'ElementOperation_anaesthetic_type_4',
	'ElementOperation_overnight_stay_0',
	'ElementOperation_overnight_stay_1',
	'ElementOperation_comments',
	'schedule_timeframe1_0',
	'schedule_timeframe1_1',
	'schedule_timeframe2',
	'submitButton'
);

$(document).ready(function() {
	checkDisable();
});

$(function() {
	$('#ElementDiagnosis_disorder_id').change(function() {
		checkDisable();
	});
});
$(function() {
	$('#ElementDiagnosis_eye_0').change(function() {
		checkDisable();

		if ($('#ElementDiagnosis_eye_0').attr('checked')) {
			$('#ElementOperation_eye_0').attr("checked", true);
			$('#ElementOperation_eye_1').attr("disabled", "disabled");
			$('#ElementOperation_eye_2').attr("disabled", "disabled");
		}
	});
});
$(function() {
	$('#ElementDiagnosis_eye_1').change(function() {
		checkDisable();

		if ($('#ElementDiagnosis_eye_1').attr('checked')) {
			$('#ElementOperation_eye_1').attr("checked", true);
			$('#ElementOperation_eye_0').attr("disabled", "disabled");
			$('#ElementOperation_eye_2').attr("disabled", "disabled");
		}
	});
});
$(function() {
	$('#ElementDiagnosis_eye_2').change(function() {
		checkDisable();
	});
});

function checkDisable() {
	if ($('#ElementDiagnosis_disorder_id').val() && (
		$('#ElementDiagnosis_eye_0').attr('checked') ||
		$('#ElementDiagnosis_eye_1').attr('checked') ||
		$('#ElementDiagnosis_eye_2').attr('checked')
	)) {
		enableElements();
	} else {
		disableElements();
	}
}

function disableElements() {
	for (i in elements) {
		$('#' + elements[i]).attr("disabled", "disabled");
	}

	$('input[name=yt1]').attr('disabled', 'disabled');
}

function enableElements() {
	for (i in elements) {
		$('#' + elements[i]).removeAttr("disabled");
	}

	$('input[name=yt1]').removeAttr('disabled');
}

</script>

<div class="header">

<strong>Book Operation:</strong> Select diagnosis
</div>

<?php

// @todo - populate diagnosis eye with appropriate value
$disorderId = '';
$value = '';
$eye = '';

if (empty($model->event_id)) {
	// It's a new event so fetch the most recent element_diagnosis
	$diagnosis = $model->getNewestDiagnosis();

	if (empty($diagnosis->disorder)) {
		// There is no diagnosis for this episode, or no episode, or the diagnosis has no disorder (?)
		$diagnosis = $model;
	} else {
		// There is a diagnosis for this episode
		$value = $diagnosis->disorder->fully_specified_name;
		$eye = $diagnosis->eye;
		$disorderId = $diagnosis->disorder->id;
	}
} else {
	if (isset($model->disorder)) {
		$value = $model->disorder->fully_specified_name;
		$eye = $model->eye;
		$disorderId = $model->disorder->id;
	}

	$diagnosis = $model;
}

?>
<div class="box_grey_big_gradient_top"></div>
<div class="box_grey_big_gradient_bottom">
        <div class="label">Select eye(s):</div>
	<div class="data"><?php echo CHtml::radioButtonList('eye', $eye, $model->getEyeOptions(), array('separator' => ' &nbsp; ')); ?></div>
        <div class="cleartall"></div>
        <div class="label">Enter diagnosis:</div>
        <div class="data"><span></span>
<?php

$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
		'name'=>'disorder',
		'id'=>'disorder',
		'value'=>'',
		'sourceUrl'=>array('disorder/autocomplete'),
		'htmlOptions'=>array(
			'style'=>'height:20px;width:200px;'
		),
		'options'=>array(
			'select'=>"js:function(event, ui) {
	                        $.ajax({
                                	'url': '" . Yii::app()->createUrl('disorder/details') . "',
                                	'type': 'GET',
                                	'data': {'name': ui.item.value},
					'success': function(data) {
						$('#ElementDiagnosis_disorder_id').val(data);
						$('#displayDisorder').html(ui.item.value);

						$('#disorder').val('');
					}
				});
			}"
		),
	)
);

echo CHtml::hiddenField('ElementDiagnosis[disorder_id]', $disorderId);

?>
</div>
<div class="cleartall"></div>
<div class="label">Disorder:</div>
<div class="data" id="displayDisorder"><?php echo $value ?></div>

</div>
