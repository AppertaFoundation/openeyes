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

$this->layout = 'main'; ?>
<?php Yii::app()->getClientScript()->registerScriptFile(Yii::app()->baseUrl.'/js/phrase.js')?>
<h2>Patient search</h2>
<div class="centralColumn">
	<p><strong>Find a patient.</strong> Either by hospital number, NHS number or firstname and surname.</p>
	<?php $this->renderPartial('//base/_messages'); ?>
	<?php if ($_SERVER['REQUEST_URI'] == Yii::app()->baseUrl.'/patient/results/error') {?>
		<div id="patient-search-error" class="alertBox">
			Please enter either a valid hospital number or a firstname and lastname.
		</div>
	<?php }else if ($_SERVER['REQUEST_URI'] == Yii::app()->baseUrl.'/patient/no-results') {?>
		<div id="patient-search-error" class="alertBox">
			Sorry, No patients found for that search.
		</div>
	<?php }else if ($_SERVER['REQUEST_URI'] == Yii::app()->baseUrl.'/patient/no-results-pas') {?>
		<div id="pas-error" class="alertBox">
			Sorry, the PAS is down. Unable to search for patients.
		</div>
	<?php }else if ($_SERVER['REQUEST_URI'] == Yii::app()->baseUrl.'/patient/no-results-address') {?>
		<div id="pas-address-error" class="alertBox">
			Sorry, the patient has no address defined in PAS and so cannot be loaded.
		</div>
	<?php }else{?>
		<div id="patient-search-error" class="alertBox" style="display: none;">
		</div>
	<?php }?>
	<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'patient-search',
		'enableAjaxValidation'=>true,
		'focus'=>'#query',
		'action' => Yii::app()->createUrl('patient/results')
	));?>
	<div id="search_patient_id" class="form_greyBox bigInput">
		<?php
			echo CHtml::label('Search:', 'hospital_number');
			echo CHtml::textField('query', '', array('style'=>'width: 304px;'));
			echo CHtml::hiddenField('Patient[hos_num]','');
			echo CHtml::hiddenField('Patient[nhs_num]','');
			echo CHtml::hiddenField('Patient[first_name]','');
			echo CHtml::hiddenField('Patient[last_name]','');
		?>
		<button type="submit" style="float: right; display: block;" class="classy blue tall" id="findPatient_id" tabindex="2"><span class="button-span button-span-blue">Find patient</span></button>
		<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="float: right; margin-right: 10px; margin-top: 9px; display: none;" />
		<?php //$this->endWidget();?>
	</div>
	<?php
	$this->endWidget();
	?>
</div><!-- .centralColumn -->
<div id="search-form" class="">
</div><!-- search-form -->
<div id="patient-list"></div>
<script type="text/javascript">
	$('#findPatient_id').click(function() {
		patient_search();
		return false;
	});

	$('#findPatient_details').click(function() {
		patient_search();
		return false;
	});

	function patient_search() {
		if (!$('#findPatient_id').hasClass('inactive')) {
			var query = $('#query').val();
			if (query.length <1) {
				return false;
			}

			$('#Patient_hos_num').val('');
			$('#Patient_nhs_num').val('');
			$('#Patient_first_name').val('');
			$('#Patient_last_name').val('');

			if (!query.match(/[a-zA-Z]/)) {
				query = query.replace(/[^0-9]+/g,'');
			}

			if (query.match(/^[0-9]+$/)) {
				if (query.length == 10) {
					$('#Patient_nhs_num').val(query);
				} else {
					$('#Patient_hos_num').val(query);
				}
			} else {
				if (!query.match(/ /)) {
					$('#patient-search-error').html('<h3>Please enter a hospital number, NHS number or a firstname and lastname.</h3>');
					$('#patient-search-error').show();
					$('#patient-list').hide();
					$('#query').select();
					return false;
				} else {
					var x = query.split(' ');
					$('#Patient_last_name').val(x.pop());
					$('#Patient_first_name').val(x.join(' '));
				}
			}

			disableButtons();

			$('#patient-search').submit();
			return false;
		}
		return false;
	}
</script>
