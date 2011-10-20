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
http://www.openeyes.org.uk	 info@openeyes.org.uk
--
*/

?><p><strong>Patient:</strong> <?php echo $patient->first_name . ' ' . $patient->last_name . ' (' . $patient->hos_num . ')'; ?></p>

<?php
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl.'/js/phrase.js');
Yii::app()->clientScript->registerCoreScript('jquery');

Yii::app()->clientScript->scriptMap['jquery.js'] = false;
Yii::app()->clientScript->scriptMap['jquery-ui.css'] = false;

$form = $this->beginWidget('CActiveForm', array(
        'id'=>'event-create',
        'enableAjaxValidation'=>true,
        'htmlOptions' => array('class'=>'sliding'),
        'focus'=>'#procedure_id'
));

echo CHtml::hiddenField('action', 'create');
echo CHtml::hiddenField('event_type_id', $eventTypeId);
echo CHtml::hiddenField('patient_id', $patient->id);
echo CHtml::hiddenField('firm_id', $firm->id);

/**
 * Loop through all the possible element types and display
 */

foreach ($elements as $element) {
	$elementClassName = get_class($element);

	echo $this->renderPartial(
		'/elements/' . $elementClassName . '/_form/' . $element->viewNumber,
		array(
			'model' => $element,
			'form' => $form,
			'specialties' => $specialties,
			'patient' => $patient
		)
	);
}

?>
<div class="cleartall"></div>
<button type="submit" value="submit" class="shinybutton highlighted" id="createEvent"><span>Create</span></button>
<?php
$this->endWidget();
?>
<script type="text/javascript">
        $('button.fancybox').fancybox([]);

        $('#createEvent').unbind('click').click(function() {
                $.ajax({
                        'url': '<?php echo Yii::app()->createUrl('clinical/create', array('event_type_id'=>$eventTypeId)); ?>',
                        'type': 'POST',
                        'data': $('#event-create').serialize(),
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
                        $('#event-create_es_ ul').html('');

                        $.each(arr, function(index, value) {
                                element = index.replace('Element', '');
                                element = element.substr(0, element.indexOf('_'));
                                list = '<li>' + element + ': ' + value + '</li>';
                                $('#event-create_es_ ul').append(list);
                        });
                        $('#event-create_es_').show();
                        return false;
                } else {
                        $('#event-create_es_ ul').html('');
                        $('#event-create_es_').hide();
                }
        }
</script>
