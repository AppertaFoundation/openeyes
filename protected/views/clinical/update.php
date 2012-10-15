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

$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl . '/js/phrase.js');
Yii::app()->clientScript->registerCoreScript('jquery');
$cs->registerScriptFile($baseUrl.'/js/jquery.watermark.min.js');

$form = $this->beginWidget('CActiveForm', array(
        'id'=>'event-update',
        'enableAjaxValidation'=>false,
        'htmlOptions' => array('class'=>'sliding'),
        'focus'=>'#procedure_id'
));

echo CHtml::hiddenField('action', 'update');
echo CHtml::hiddenField('event_id', $id);

?>
<div id="event-update_es_" class="errorSummary" style="display:none"><p>Please fix the following input errors:</p>
<ul><li>&nbsp;</li></ul></div>
<?php

/**
 * Loop through all the possible element types and display
 */
foreach ($elements as $element) {
	$elementClassName = get_class($element);

	echo $this->renderPartial(
		Yii::app()->createUrl('/elements/' . $elementClassName . '/form'),
		array('model' => $element, 'form' => $form, 'specialties' => $specialties,
			'patient' => $patient)
	);
}

?>
<div class="cleartall"></div>
<button type="submit" class="classy green tall" id="updateEvent"><span class="button-span button-span-green">Update</span></button>
<?php
$this->endWidget();
?>
<script type="text/javascript">
	$('#updateEvent').unbind('click').click(function() {
		$.ajax({
			'url': '<?php echo Yii::app()->createUrl('clinical/update', array('id'=>$id)); ?>',
			'type': 'POST',
			'data': $('#event-update').serialize(),
			'success': function(data) {
				if (data.match(/^[0-9]+$/)) {
					window.location.href = '<?php echo Yii::app()->createUrl('patient/episodes/'.$patient->id.'/event/')?>'+data;
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
			$('#event-update_es_ ul').html('');

			$.each(arr, function(index, value) {
				element = index.replace('Element', '');
				element = element.substr(0, element.indexOf('_'));
				list = '<li>' + element + ': ' + value + '</li>';
				$('#event-update_es_ ul').append(list);
			});
			$('#event-update_es_').show();
			return false;
		} else {
			$('#event-update_es_ ul').html('');
			$('#event-update_es_').hide();
		}
	}
</script>
