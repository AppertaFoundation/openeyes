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

?><div class="heading">
<span class="emphasize">Book Operation:</span> Select diagnosis
</div>

<?php

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
		$value = $diagnosis->disorder->term . ' - ' . $diagnosis->disorder->fully_specified_name;
		$eye = $diagnosis->eye;
		$disorderId = $diagnosis->disorder->id;
	}
} else {
	if (isset($model->disorder)) {
		$value = $model->disorder->term . ' - ' . $model->disorder->fully_specified_name;
		$eye = $model->eye;
		$disorderId = $model->disorder->id;
	}

	$diagnosis = $model;
}

?>
<div class="box_grey_big_gradient_top"></div>
<div class="box_grey_big_gradient_bottom">
	<div class="label">Select eye(s):</div>
	<div class="data"><?php echo CHtml::activeRadioButtonList($diagnosis, 'eye', $model->getEyeOptions(),
		array('separator' => ' &nbsp; ')); ?>
	</div>
	<div class="cleartall"></div>
	<div class="label">Enter diagnosis:</div>
	<div class="data"><span></span>
<?php
$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
    'name'=>'ElementDiagnosis[disorder_id]',
    'id'=>'ElementDiagnosis_disorder_id_0',
    'value'=>$value,
    'sourceUrl'=>array('disorder/autocomplete'),
    'htmlOptions'=>array(
        'style'=>'height:20px;width:400px;font:10pt Arial;'
    ),
));
?></div><span class="tooltip"><a href="#"><img src="/images/icon_info.png" /><span>Type the first few characters of a disorder into the <strong>enter diagnosis</strong> text box. When you see the required disorder displayed - <strong>click</strong> to select.</span></a></span>
</div>
<script type="text/javascript">
	$('input[name="ElementDiagnosis[eye]"]').click(function() {
		var disorder = $('input[name="ElementDiagnosis[disorder_id]"]').val();
		if (disorder.length == 0) {
			$('input[name="ElementDiagnosis[disorder_id]"]').focus();
		}
	});
</script>
