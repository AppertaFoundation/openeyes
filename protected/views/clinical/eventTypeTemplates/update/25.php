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

$this->header(true,array('event'=>$event,'editing'=>true));

Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/phrase.js'));

$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
	'id' => 'clinical-update',
	'enableAjaxValidation' => true,
	'htmlOptions' => array('class' => 'sliding')
));
echo CHtml::hiddenField('action', 'update');
echo CHtml::hiddenField('event_id', $id);

if (isset($errors) && !empty($errors)) {?>
	<div id="clinical-create_es_" class="alertBox">
		<p>Please fix the following input errors:</p>
		<?php foreach ($errors as $field => $errs) {?>
			<ul>
				<?php foreach ($errs as $err) {?>
					<li>
						<?php echo $field.': '.$err?>
					</li>
				<?php }?>
			</ul>
		<?php }?>
	</div>
<?php }?>
<script type="text/javascript">
	// When eye selected in diagnosis, reflect the value in operation details
	$(document).ready(function(){
		$('input[name="ElementDiagnosis[eye_id]"]').change(function(){
			if($(this).siblings('label').text() != 'Both'){
				// Set operation eye selection to current diagnosis eye selection
				id = $('input[name="ElementDiagnosis[eye_id]"]:checked').val();
				$('#ElementOperation_eye_id input[value="'+id+'"]').attr('checked', true);
			}else{
				// Unset operation eye selection if user selected 'Both'
				$('input[name="ElementOperation[eye_id]"]:checked').attr('checked', false);
			}
		});
	});
</script>
<?php

/**
 * Loop through all the possible element types and display
 */
foreach ($elements as $element) {
	$elementClassName = get_class($element);

	echo $this->renderPartial(
		'/elements/' . $elementClassName . '/form',
		array('model' => $element, 'form' => $form, 'specialties' => $specialties,
		'newRecord' => false)
	);
}

if (isset($errors) && !empty($errors)) {?>
	<div id="clinical-create_es_" class="alertBox">
		<p>Please fix the following input errors:</p>
		<?php foreach ($errors as $field => $errs) {?>
			<ul>
				<?php foreach ($errs as $err) {?>
					<li>
						<?php echo $field.': '.$err?>
					</li>
				<?php }?>
			</ul>
		<?php }?>
	</div>
<?php }?>

<div class="cleartall"></div>
<div class="form_button">
	<img class="loader" style="display: none;" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." />&nbsp;
	<button type="submit" class="classy green venti auto" id="saveOperation" name="saveOperation"><span class="button-span button-span-green">Save</span></button>
	<button type="submit" class="classy red venti auto" id="cancelOperation" name="cancelOperation"><span class="button-span button-span-red">Cancel</span></button>
</div>
<?php
$this->endWidget();
$this->footer(true,array('event'=>$event,'editing'=>true));
?>
