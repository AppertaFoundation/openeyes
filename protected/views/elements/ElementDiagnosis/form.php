<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<h3 class="withEventIcon"
	style="background: transparent url(<?php echo Yii::app()->createUrl('img/_elements/icons/event/medium/treatment_laser.png')?>) center left no-repeat;">
<?php	if(empty($model->event_id)) { ?>Book Operation<?php } else { ?>Edit Operation<?php } ?>
</h3>
<h4>Select diagnosis</h4>
<?php
if ($this->action->id == 'create' && empty($_POST)) {
	$model->setDefaultOptions();
}
?>
 
<?php echo $form->radioButtons($model, 'eye_id', 'eye');?>

<?php $form->widget('application.widgets.DiagnosisSelection',array(
		'field' => 'disorder_id',
		'element' => $model,
		'options' => CommonOphthalmicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId))
));
?>

<script type="text/javascript">
	$('input[name="ElementDiagnosis[eye]"]').click(function() {
		var disorder = $('input[name="ElementDiagnosis[disorder_id]"]').val();
		if (disorder.length == 0) {
			$('input[name="ElementDiagnosis[disorder_id]"]').focus();
		}
	});
	$('input[name="ElementDiagnosis[disorder_id]"]').watermark('type the first few characters of a diagnosis');
	$('select[name="ElementDiagnosis[disorder_id]"]').change(function() {
		var value = $(this).children(':selected').text();
		$('#enteredDiagnosisText span').html(value);
		$('#savedDiagnosis').val(value);
		$(this).children(':selected').attr('selected', false);
	});
</script>
