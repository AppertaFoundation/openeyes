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

Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/phrase.js');

$form = $this->beginWidget('CActiveForm', array(
	'id' => 'clinical-update',
	'enableAjaxValidation' => true,
	'htmlOptions' => array('class' => 'sliding')
));

echo CHtml::hiddenField('action', 'update');
echo CHtml::hiddenField('event_id', $id);
?>
<?php

/**
 * Loop through all the possible element types and display
 */
foreach ($elements as $element) {
	$elementClassName = get_class($element);

	echo $this->renderPartial(
		'/elements/' . $elementClassName . '/form',
		array('model' => $element, 'form' => $form, 'specialties' => $specialties,
		'newRecord' => false, 'subspecialty' => $subspecialty, 'subsections' => $subsections,
		'procedures' => $procedures)
	);
}

if (isset($errors) && !empty($errors)) {?>
	<div id="clinical-create_es_" class="alertBox">
		<p>Please fix the following input errors:</p>
		<?php foreach ($errors as $field => $errs) {?>
			<ul>
				<li>
					<?php echo $field.': '.$errs[0]?>
				</li>
			</ul>
		<?php }?>
	</div>
<?php }?>

<div class="cleartall"></div>
<div class="form_button">
	<img class="loader" style="display: none;" src="/img/ajax-loader.gif" alt="loading..." />&nbsp;
	<button type="submit" class="classy green venti" id="saveOperation" name="saveOperation"><span class="button-span button-span-green">Save</span></button>
	<button type="submit" class="classy red venti" id="cancelOperation" name="cancelOperation"><span class="button-span button-span-red">Cancel</span></button>
</div>
<?php
$this->endWidget(); ?>
<script type="text/javascript">
	$('#saveOperation').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			return true;
		}
		return false;
	});

	$('#cancelOperation').unbind('click').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			return true;
		}
		return false;
	});
</script>
<?php $this->footer(true,array('event'=>$event,'editing'=>true))?>
