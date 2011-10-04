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

$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl . '/js/phrase.js');
Yii::app()->clientScript->registerCoreScript('jquery');
$cs->registerScriptFile($baseUrl.'/js/jquery.watermark.min.js');

$form = $this->beginWidget('CActiveForm', array(
	'id' => 'clinical-update',
	'enableAjaxValidation' => true,
	'htmlOptions' => array('class' => 'sliding')
	));

echo CHtml::hiddenField('action', 'update');
echo CHtml::hiddenField('event_id', $id);

echo $form->errorSummary($elements);

/**
 * Loop through all the possible element types and display
 */
foreach ($elements as $element) {
	$elementClassName = get_class($element);

	echo $this->renderPartial(
		'/elements/' . $elementClassName . '/_form/' .
		$element->viewNumber, array('model' => $element, 'form' => $form, 'specialties' => $specialties,
		'patient' => $patient, 'newRecord' => false, 'specialty' => $specialty, 'subsections' => $subsections,
		'procedures' => $procedures)
	);
}

// Display referral select box if required
if (isset($referrals) && is_array($referrals)) {
	// There is at least on referral, so include it/them
	if (count($referrals) > 1) {
		// Display a list of referrals for the user to choose from ?>
<div class="box_grey_big_gradient_top"></div>
<div class="box_grey_big_gradient_bottom">
	<span class="referral_red">There is more than one open referral that could apply to this event.</span><p />
	<label for="referral_id">Select the referral that applies to this event:</label>
<?php	echo CHtml::dropDownList('referral_id', '', CHtml::listData($referrals, 'id', 'id')); ?>
	</div>
<?php
	}
}
?>
<div class="cleartall"></div>
<button type="submit" value="submit" class="shinybutton highlighted" id="update"><span>Update operation</span></button>
<?php
$this->endWidget(); ?>
<script type="text/javascript">
	$('#update').click(function() {
		$.ajax({
			'url': '<?php echo Yii::app()->createUrl('clinical/update', array('id'=>$id)); ?>',
			'type': 'POST',
			'data': $('#clinical-update').serialize(),
			'success': function(data) {
				try {
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
				} catch (e) {
					$.fancybox.close();
					document.open();
					document.write(data);
					document.close();
				}
			}
		});
		return false;
	});
</script>
