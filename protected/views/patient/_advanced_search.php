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

$form=$this->beginWidget('CActiveForm', array(
	'id'=>'patient-adv-search-form',
	'enableAjaxValidation'=>true,
	'focus'=>'#Patient_hos_num',
	//'action' => Yii::app()->createUrl('patient/results')
));?>
	<div id="search_patient_details" class="form_greyBox clearfix">
		<div style="margin-bottom: 12px;"><label style="font-size: 17px;">Or, search by patient name:</label></div>
		<div class="form_column">
			<div class="inputLayout clearfix">
				<?php echo CHtml::label('Last name:<span class="labelRequired">Last name is required</span>', 'last_name');?>
				<?php echo CHtml::textField('Patient[last_name]', '', array('style'=>'width: 150px;', 'class' => 'topPadding'));?>
			</div>
			<div class="inputLayout clearfix">
				<?php echo CHtml::label('First name:<span class="labelRequired">First name is required</span>', 'first_name');?>
				<?php echo CHtml::textField('Patient[first_name]', '', array('style'=>'width: 150px;', 'class' => 'topPaddingSmall'));?>
			</div>
		</div>
		<div class="form_button">
			<button type="submit" style="margin-top: -33px; float: right; display: block;" class="classy blue tall" id="findPatient_details" tabindex="2"><span class="button-span button-span-blue">Find patient</span></button>
			<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="float: right; margin-right: 10.4em; margin-top: -1.8em; display: none;" />
		</div>
	</div>
<?php $this->endWidget();?>
<script type="text/javascript">
	$('#dob_day').watermark('DD');
	$('#dob_month').watermark('MM');
	$('#dob_year').watermark('YYYY');
</script>
