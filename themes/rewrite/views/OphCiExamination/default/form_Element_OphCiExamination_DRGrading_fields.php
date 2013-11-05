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
$clinical_retinopathys = OphCiExamination_DRGrading_ClinicalRetinopathy::model()->findAll(array('order'=>'display_order'));
?>
<div class="row field-row">
	<div class="large-4 column">
		<label for="<?php echo get_class($element).'_'.$side.'_clinicalret_id';?>">
			<?php echo $element->getAttributeLabel($side.'_clinicalret_id')?>:
		</label>
	</div>
	<div class="large-8 column">
		<div class="wrapper field-highlight inline<?php if ($element->{$side.'_clinicalret'}) {?> <?php echo $element->{$side . '_clinicalret'}->class?><?php }else{?> none<?php }?>">
			<?php
				$html_options = array('options' => array());
				foreach ($clinical_retinopathys as $clinical) {
					$html_options['options'][(string) $clinical->id] = array('data-val' => $clinical->name, 'class' => $clinical->class);
				}
				echo CHtml::activeDropDownList($element, $side . '_clinicalret_id', CHtml::listData($clinical_retinopathys,'id','name'), $html_options);
			?>
		</div>
		<span class="grade-info-icon" data-info-type="clinicalret"><img src="<?php echo $this->assetPath ?>/img/icon_info.png" style="height:20px" /></span>
		<div class="quicklook grade-info" style="display: none;">
			<?php foreach ($clinical_retinopathys as $clinical) {
				echo '<div style="display: none;" class="' . get_class($element). '_'. $side.'_clinicalret_desc" id="' . get_class($element). '_' . $side . '_clinicalret_desc_' . preg_replace('/\s+/', '', $clinical->name) . '">' . $clinical->description . '</div>';
			}
			?>
		</div>
		<div id="<?php echo get_class($element). '_'. $side.'_all_clinicalret_desc'; ?>" class="grade-info-all" data-select-id="<?php echo get_class($element) . '_' . $side . '_clinicalret_id'; ?>">
			<dl>
				<?php foreach ($clinical_retinopathys as $clinical) { ?>
				<dt class="<?php echo $clinical->class ?>"><a href="#" data-id="<?php echo $clinical->id ?>"><?php echo $clinical->name ?></a></dt>
				<dd class="<?php echo $clinical->class ?>"><?php echo nl2br($clinical->description) ?></dd>
				<?php } ?>
			</dl>
		</div>
	</div>
</div>
<div class="row field-row">
	<div class="large-4 column">
		<label for="<?php echo get_class($element).'_'.$side.'_nscretinopathy_id';?>">
			<?php echo $element->getAttributeLabel($side.'_nscretinopathy_id')?>:
		</label>
	</div>
	<div class="large-8 column">
		<div class="wrapper field-highlight inline<?php if ($element->{$side.'_nscretinopathy'}) {?> <?php echo $element->{$side.'_nscretinopathy'}->class?><?php }else{?> none<?php }?>">
		<?php
			$nscretinopathy_html_options = array('options' => array());
			foreach (OphCiExamination_DRGrading_NSCRetinopathy::model()->findAll(array('order'=>'display_order')) as $retin) {
				$nscretinopathy_html_options['options'][(string) $retin->id] = array('data-val' => $retin->name, 'data-booking' => $retin->booking_weeks, 'class' => $retin->class);
			}
			echo CHtml::activeDropDownList($element, $side.'_nscretinopathy_id', CHtml::listData(OphCiExamination_DRGrading_NSCRetinopathy::model()->findAll(array('order'=>'display_order')),'id','name'), $nscretinopathy_html_options);
		?>
		</div>
		<span class="grade-info-icon" data-info-type="retinopathy"><img src="<?php echo $this->assetPath ?>/img/icon_info.png" style="height:20px" /></span>
		<div class="quicklook grade-info" style="display: none;">
			<?php foreach (OphCiExamination_DRGrading_NSCRetinopathy::model()->findAll(array('order'=>'display_order')) as $retin) {
				echo '<div style="display: none;" class="' . get_class($element). '_'. $side.'_nscretinopathy_desc" id="' . get_class($element). '_' . $side . '_nscretinopathy_desc_' . $retin->name . '">' . $retin->description . '</div>';
			}?>
		</div>
		<div id="<?php echo get_class($element). '_'. $side.'_all_retinopathy_desc'; ?>" class="grade-info-all" data-select-id="<?php echo get_class($element) . '_' . $side . '_nscretinopathy_id'; ?>">
			<dl>
				<?php foreach (OphCiExamination_DRGrading_NSCRetinopathy::model()->findAll(array('order'=>'display_order')) as $retin) {?>
					<dt class="<?php echo $retin->class ?>"><a href="#" data-id="<?php echo $retin->id ?>"><?php echo $retin->name ?></a></dt>
					<dd class="<?php echo $retin->class ?>"><?php echo nl2br($retin->description) ?></dd>
				<?php } ?>
			</dl>
		</div>
	</div>
</div>
<?php echo $form->radioBoolean($element,$side.'_nscretinopathy_photocoagulation',array(),array('label'=>4,'field'=>8));
$clinical_maculopathys = OphCiExamination_DRGrading_ClinicalMaculopathy::model()->findAll(array('order'=>'display_order'));
$curr_cm = $element->{$side . '_clinicalmac'} ? $element->{$side . '_clinicalmac'} : @$clinical_maculopathys[0];
?>
<div class="row field-row">
	<div class="large-4 column">
		<label for="<?php echo get_class($element).'_'.$side.'_clinicalmac_id';?>">
			<?php echo $element->getAttributelabel($side.'_clinicalmac_id')?>:
		</label>
	</div>
	<div class="large-8 column">
		<div class="wrapper field-highlight inline<?php if ($curr_cm) {?> <?php echo $curr_cm->class?><?php }else{?> none<?php }?>">
			<?php
			$html_options = array('options' => array());
			foreach ($clinical_maculopathys as $clinical) {
				$html_options['options'][(string) $clinical->id] = array('data-val' => $clinical->name, 'class' => $clinical->class);
			}
			echo CHtml::activeDropDownList($element, $side . '_clinicalmac_id', CHtml::listData($clinical_maculopathys,'id','name'), $html_options);
			?>
		</div>
		<!-- REMOVED UNTIL WE ARE PROVIDED WITH APPROPRIATE TEXT FOR THE DESCRIPTIONS
		TODO: code to auto detect when there are no descriptions, so that this works dynamically based on the data.
		<span class="grade-info-icon" data-info-type="clinical"><img src="<?php echo $this->assetPath ?>/img/icon_info.png" style="height:20px" /></span>
		<div class="quicklook grade-info" style="display: none;">
			<?php foreach ($clinical_maculopathys as $clinical) {
				echo '<div style="display: none;" class="' . get_class($element). '_'. $side.'_clinicalmac_desc" id="' . get_class($element). '_' . $side . '_clinicalmac_desc_' . preg_replace('/\s+/', '', $clinical->name) . '">' . $clinical->description . '</div>';
			}
			?>
		</div>

		<div id="<?php echo get_class($element). '_'. $side.'_all_clinicalmac_desc'; ?>" class="grade-info-all" data-select-id="<?php echo get_class($element) . '_' . $side . '_clinicalmac_id'; ?>">
			<dl>
				<?php foreach ($clinical_maculopathys as $clinical) { ?>
					<dt class="<?php echo $clinical->class ?>"><a href="#" data-id="<?php echo $clinical->id ?>"><?php echo $clinical->name ?></a></dt>
					<dd class="<?php echo $clinical->class ?>"><?php echo nl2br($clinical->description) ?></dd>
				<?php } ?>
			</dl>
		</div>
		-->
	</div>
</div>
<div class="row field-row">
	<div class="large-4 column">
		<label for="<?php echo get_class($element).'_'.$side.'_nscmaculopathy_id';?>">
			<?php echo $element->getAttributelabel($side.'_nscmaculopathy_id')?>:
		</label>
	</div>
	<div class="large-8 column">
		<div class="wrapper field-highlight inline<?php if ($element->{$side . '_nscmaculopathy'}) {?> <?php echo $element->{$side . '_nscmaculopathy'}->class?><?php }else{?> none<?php }?>">
		<?php
			$nscmacuopathy_html_options = array('options' => array());
			foreach (OphCiExamination_DRGrading_NSCMaculopathy::model()->findAll(array('order'=>'display_order')) as $macu) {
				$nscmaculopathy_html_options['options'][(string) $macu->id] = array('data-val' => $macu->name, 'data-booking' => $macu->booking_weeks, 'class' => $macu->class);
			}
			echo CHtml::activeDropDownList($element, $side . '_nscmaculopathy_id', CHtml::listData(OphCiExamination_DRGrading_NSCMaculopathy::model()->findAll(array('order'=>'display_order')),'id','name'), $nscmaculopathy_html_options);
		?>
		</div>
		<span class="grade-info-icon" data-info-type="maculopathy"><img src="<?php echo $this->assetPath ?>/img/icon_info.png" style="height:20px" /></span>
		<div class="quicklook grade-info" style="display: none;">
			<?php foreach (OphCiExamination_DRGrading_NSCMaculopathy::model()->findAll(array('order'=>'display_order')) as $macu) {
				echo '<div style="display: none;" class="' . get_class($element) . '_' . $side . '_nscmaculopathy_desc desc" id="' . get_class($element) . '_' . $side . '_nscmaculopathy_desc_' . $macu->name . '">' . $macu->description . '</div>';
			}
			?>
		</div>
		<!-- div containing the full list of descriptions for nsc maculopathy -->
		<div id="<?php echo get_class($element) . '_'. $side.'_all_maculopathy_desc'; ?>" class="grade-info-all" data-select-id="<?php echo get_class($element) . '_' . $side . '_nscmaculopathy_id'; ?>">
			<dl>
				<?php foreach (OphCiExamination_DRGrading_NSCMaculopathy::model()->findAll(array('order'=>'display_order')) as $macu) { ?>
				<dt class="<?php echo $macu->class ?>"><a href="#" data-id="<?php echo $macu->id ?>"><?php echo $macu->name ?></a></dt>
				<dd class="<?php echo $macu->class ?>"><?php echo nl2br($macu->description) ?></dd>
				<?php } ?>
			</dl>
		</div>
	</div>
</div>
<?php echo $form->radioBoolean($element,$side.'_nscmaculopathy_photocoagulation',array(),array('label'=>4,'field'=>8))?>
