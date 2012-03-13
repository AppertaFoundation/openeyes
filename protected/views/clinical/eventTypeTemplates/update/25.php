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

$this->header(true,array('event'=>$event));

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
		'patient' => $patient, 'newRecord' => false, 'subspecialty' => $subspecialty, 'subsections' => $subsections,
		'procedures' => $procedures)
	);
}

?>
<div id="clinical-update_es_" class="alertBox" style="display:none"><p>Please fix the following input errors:</p>
<ul><li>&nbsp;</li></ul></div>
<div class="cleartall"></div>
<div class="form_button">
	<img class="loader" style="display: none;" src="/img/ajax-loader.gif" alt="loading..." />&nbsp;
	<button type="submit" class="classy green venti" id="saveOperation" name="saveOperation"><span class="button-span button-span-green">Save</span></button>
	<button type="submit" class="classy red venti" id="cancelOperation" name="cancelOperation"><span class="button-span button-span-red">Cancel</span></button>
</div>
<?php
$this->endWidget(); ?>
<script type="text/javascript">
	$('a.edit-save').unbind('click').click(function() {
		$.ajax({
			'url': '<?php echo Yii::app()->createUrl('clinical/update', array('id'=>$id)); ?>',
			'type': 'POST',
			'data': $('#clinical-update').serialize(),
			'success': function(data) {
				if (data.match(/^[0-9]+$/)) {
					window.location.href = '/patient/episodes/<?php echo $patient->id?>/event/'+data;
					return false;
				}
				try {
					displayErrors(data);
				} catch (e) {
					return false;
				}
			}
		});
		return false;
	});

	function displayErrors(data) {
		arr = $.parseJSON(data);
		if (!$.isEmptyObject(arr)) {
			$('#clinical-update_es_ ul').html('');

			$.each(arr, function(index, value) {
				element = index.replace('Element', '');
				element = element.substr(0, element.indexOf('_'));
				list = '<li>' + element + ': ' + value + '</li>';
				$('#clinical-update_es_ ul').append(list);
			});
			$('#clinical-update_es_').show();
			return false;
		} else {
			$('#clinical-update_es_ ul').html('');
			$('#clinical-update_es_').hide();
		}
	}

	$('a.edit-cancel').unbind('click').click(function() {
		if (last_item_type == 'url') {
			window.location.href = last_item_id;
		} else if (last_item_type == 'episode') {
			load_episode_summary(last_item_id);
		} else if (last_item_type == 'event') {
			view_event(last_item_id);
		}
		return false;
	});

	$(document).ready(function() {
		$('input').change(function() {
			edited();
		});

		$('select').change(function() {
			edited();
		});

		$('textarea').bind('keyup',function() {
			edited();
		});
	});
</script>
<?php $this->footer(true,array('event'=>$event))?>
