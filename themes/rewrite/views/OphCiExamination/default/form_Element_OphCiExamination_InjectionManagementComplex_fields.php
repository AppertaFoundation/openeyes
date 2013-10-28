<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<?php
$no_treatment = $element->{$side . '_no_treatment'};
$no_treatment_reason = $element->{$side . '_no_treatment_reason'};
if (isset($_POST[get_class($element)])) {
	$no_treatment = $_POST[get_class($element)][$side . '_no_treatment'];
	$no_treatment_reason = OphCiExamination_InjectionManagementComplex_NoTreatmentReason::model()->findByPk((int)@$_POST[get_class($element)][$side . '_no_treatment_reason_id']);
}
$show_no_treatment_reason_other = false;
if ($no_treatment_reason && $no_treatment_reason->other) {
	$show_no_treatment_reason_other = true;
}
?>

<div id="div_<?php echo get_class($element) . "_" . $side?>_no_treatment"
	 class="eventDetail <?php echo get_class($element) ?>_no_treatment">
	<div class="label">
		<?php echo $element->getAttributeLabel($side . '_no_treatment') ?>:
	</div>
	<div class="data">
		<?php
		echo $form->checkbox($element, $side . '_no_treatment', array('nowrapper' => true));
		?>
	</div>
</div>

<div id="div_<?php echo get_class($element) . "_" . $side?>_no_treatment_reason_id"
	 class="eventDetail <?php echo get_class($element) ?>_no_treatment_reason_id"<?php if (!$no_treatment) {?> style="display: none;"<?php }?>>
	<div class="label">
		<?php echo $element->getAttributeLabel($side . '_no_treatment_reason_id') ?>
	</div>
	<div class="data">
		<?php echo $form->dropDownlist($element, $side . '_no_treatment_reason_id',
			CHtml::listData($no_treatment_reasons,'id','name'),
			$no_treatment_reasons_opts) ?>
	</div>
</div>

<div id="div_<?php echo get_class($element) . "_" . $side ?>_no_treatment_reason_other"
	 class="eventDetail <?php echo get_class($element) ?>_no_treatment_reason_other" <?php if (!$show_no_treatment_reason_other) {?> style="display: none;"<?php }?>>
	<div class="label">
		<?php echo $element->getAttributeLabel($side . '_no_treatment_reason_other') ?>
	</div>
	<div class="data">
		<?php echo $form->textArea($element, $side . '_no_treatment_reason_other', array('rows' => 4, 'cols' => 50, 'nowrapper' => true)) ?>
	</div>
</div>

<div id="div_<?php echo get_class($element) . '_' . $side ?>_treatment_fields">
	<div class="eventDetail elementField diagnosis_id clearfix">
		<div class="label" style="vertical-align: top;"><?php echo $element->getAttributeLabel($side . '_diagnosis1_id'); ?>:</div>
		<div class="data" style="display: inline-block;">
		<?php $form->widget('application.widgets.DiagnosisSelection',array(
				'field' => $side . '_diagnosis1_id',
				'element' => $element,
				'options' => CHtml::listData($l1_disorders,'id','term'),
				'layout' => 'search',
				'default' => false,
				'dropdownOptions' => array('empty'=>'- Please select -', 'options' => $l1_opts, 'style' => 'margin-bottom: 10px; width: 240px;'),
		));?>
		</div>
	</div>

	<div class="eventDetail elementField<?php if (!array_key_exists($element->{$side . '_diagnosis1_id'}, $l2_disorders) ) { echo " hidden"; }?>" id="<?php echo $side ?>_diagnosis2_wrapper">
		<div class="label" style="vertical-align: top;"><?php echo $element->getAttributeLabel($side . '_diagnosis2_id'); ?>:</div>
		<div class="data" style="display: inline-block;">
			<?php
			$l2_attrs =  array('empty'=>'- Please select -', 'style' => 'margin-bottom: 10px; width: 240px;');
			$l2_opts = array();
			if (array_key_exists($element->{$side . '_diagnosis1_id'}, $l2_disorders)) {
				$l2_opts = $l2_disorders[$element->{$side . '_diagnosis1_id'}];
				// this is used in the javascript for checking the second level list is correct.
				$l2_attrs['data-parent_id'] = $element->{$side . '_diagnosis1_id'};
			}
			$form->widget('application.widgets.DiagnosisSelection',array(
				'field' => $side . '_diagnosis2_id',
				'element' => $element,
				'options' => CHtml::listData($l2_opts,'id','term'),
				'layout' => 'search',
				'default' => false,
				'dropdownOptions' => $l2_attrs,
			));?>

		</div>
	</div>

	<?php
	$questions = $element->getInjectionQuestionsForSide($side);

	$this->renderPartial('form_' . get_class($element) . '_questions',
					array('side' => $side, 'element' => $element, 'form' => $form, 'questions' => $questions));
	?>

	<?php
	if ($treatments = $element->getInjectionTreatments($side)) {
		?>
		<div class="eventDetail elementField diagnosis_id clearfix">
			<div class="label" style="vertical-align: top;"><?php echo $element->getAttributeLabel($side . '_treatment_id'); ?>:</div>
			<div class="data">
				<?php $form->dropDownlist($element, $side . '_treatment_id',
					CHtml::listData($treatments,'id','name'),
					array('empty'=>'- Please select -', 'nowrapper' => true));
				?>
			</div>
		</div>

	<?php
	}
	?>

	<?php
		$html_options = array(
			'style' => 'margin-bottom: 10px; width: 240px;',
			'options' => array(),
			'empty' => '- Please select -',
			'div_id' =>  get_class($element) . '_' . $side . '_risks',
			'label' => 'Risks');
		$risks = $element->getRisksForSide($side);
		foreach ($risks as $risk) {
			$html_options['options'][(string) $risk->id] = array('data-order' => $risk->display_order);
		}
		echo $form->multiSelectList($element, get_class($element) . '[' . $side . '_risks]', $side . '_risks', 'id', CHtml::listData($risks,'id','name'), array(), $html_options)
	?>

	<div class="eventDetail comments">
		<div class="label"><?php echo $element->getAttributeLabel($side . '_comments'); ?></div>
		<div class="data"><?php echo $form->textArea($element, $side . '_comments',array('rows' => 4, 'cols' => 50, 'nowrapper' => true))?></div>
	</div>
</div>
