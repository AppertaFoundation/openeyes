<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>
<?php
$model = OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_PatientFactor::model();
$factor_answer = OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PatientFactor_Answer::model();?>
<div class="element-fields row">
	<?php
	foreach ($model->findAll('`active` = ?',array(1)) as $factor) {
		?>
		<fieldset class="row field-row">
			<legend class="large-3 column">
				<?php echo $factor->name?>
			</legend>
			<?php
			$is_factor = $factor_answer->getFactorAnswer($factor->id,$element->id);
			$comments = $factor_answer->getComments($factor->id,$element->id);
			$i = $factor->id;

			$value =$factor->require_comments ? '1' : '0';

			?>
			<?php
				echo CHtml::hiddenField("ophcocvi_clinicinfo_patient_factor_id[$i]" , $factor->id, array('id' => 'hiddenInput'));
				echo CHtml::hiddenField("require_comments[$i]" , $value, array('id' => 'hiddenInput'));

			?>

			<div class="large-9 column">
				<label class="inline highlight">
					<?php echo CHtml::radioButton("is_factor[$i]", (isset($is_factor) && $is_factor == 1), array('id' => CHtml::modelName($factor_answer) . '_' . $factor->id . '_1', 'value' => 1))?>
					Yes
				</label>
				<label class="inline highlight">
					<?php echo CHtml::radioButton("is_factor[$i]", (isset($is_factor) && $is_factor == 0), array('id' => CHtml::modelName($factor_answer) . '_' .  $factor->id . '_0', 'value' => 0))?>
					No
				</label>
				<label class="inline highlight">
					<?php echo CHtml::radioButton("is_factor[$i]", (isset($is_factor) && $is_factor == 2), array('id' => CHtml::modelName($factor_answer) . '_' .  $factor->id . '_2', 'value' => 2))?>
					Unknown
				</label>

			</div>
		</fieldset>
		<?php if($factor->require_comments == 1){?>
			<fieldset class="row field-row">
				<legend class="large-3 column">
					<?php echo $factor->comments_label; ?>
				</legend>
				<div class="large-9 column">

					<?php echo  CHtml::textArea( "comments[$i]", ($comments), array('rows'=>3, 'cols'=>75));?>
				</div>
			</fieldset>
		<?php  }		}?>
</div>
<div class="element-fields row">
	<?php echo $form->dropDownList($element, 'employment_status_id', CHtml::listData(OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_EmploymentStatus::model()->findAll('`active` = ?',array(1),array('order'=> 'display_order asc')),'id','name'),array('empty'=>'- Please select -'))?>
	<?php echo $form->dropDownList($element, 'preferred_info_fmt_id', CHtml::listData(OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PreferredInfoFmt::model()->findAll(array('order'=> 'display_order asc')),'id','name'),array('empty'=>'- Please select -'))?>
	<?php echo $form->textField($element, 'info_email', array('size' => '20'))?>
	<?php echo $form->dropDownList($element, 'contact_urgency_id', CHtml::listData(OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_ContactUrgency::model()->findAll(array('order'=> 'display_order asc')),'id','name'),array('empty'=>'- Please select -'))?>
	<?php echo $form->dropDownList($element, 'preferred_language_id', CHtml::listData(Language::model()->findAll(array('order'=> 'name asc')),'id','name'),array('empty'=>'- Please select -'))?>
	<?php echo $form->textArea($element, 'social_service_comments', array('rows' => 6, 'cols' => 80))?>
</div>

