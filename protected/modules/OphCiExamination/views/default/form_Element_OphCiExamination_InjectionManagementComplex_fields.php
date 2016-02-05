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
$show_no_treatment_reason_other = false;
if ($no_treatment_reason && $no_treatment_reason->other) {
    $show_no_treatment_reason_other = true;
}
$layoutColumns = array(
    'label' => 3,
    'field' => 9
);
$el_model_name = CHtml::modelName($element);
?>

<fieldset class="row field-row jsNoTreatment <?php echo $el_model_name ?>_no_treatment">
	<legend class="large-3 column">
		Treatment:
	</legend>
	<div class="large-9 column">
		<?php echo $form->checkBox($element, $side . '_no_treatment', array('nowrapper' => true))?>
	</div>
</fieldset>

<div class="row field-row <?php echo $el_model_name ?>_no_treatment_reason_id" id="div_<?php echo $el_model_name . "_" . $side?>_no_treatment_reason_id"<?php if (!$no_treatment) {
    ?> style="display: none;"<?php 
}?>>
	<div class="large-3 column">
		<label for="<?php echo $el_model_name.'_'.$side.'_no_treatment_reason_id';?>">
			<?php echo $element->getAttributeLabel($side . '_no_treatment_reason_id')?>:
		</label>
	</div>
	<div class="large-8 column end">
		<?php echo $form->dropDownlist($element, $side . '_no_treatment_reason_id', CHtml::listData($no_treatment_reasons, 'id', 'name'), $no_treatment_reasons_opts, array('nowrapper' => true))?>
	</div>
</div>

<div class="row field-row <?php echo $el_model_name ?>_no_treatment_reason_other" id="div_<?php echo $el_model_name . "_" . $side ?>_no_treatment_reason_other"<?php if (!$show_no_treatment_reason_other) {
    ?> style="display: none;"<?php 
}?>>
	<div class="large-3 column">
		<label for="<?php echo $el_model_name.'_'.$side.'_no_treatment_reason_other';?>">
			<?php echo $element->getAttributeLabel($side . '_no_treatment_reason_other')?>:
		</label>
	</div>
	<div class="large-9 column">
		<?php echo $form->textArea($element, $side . '_no_treatment_reason_other', array('nowrapper' => true))?>
	</div>
</div>

<div id="div_<?php echo $el_model_name . '_' . $side ?>_treatment_fields">

	<div class="diagnosis_id">
		<?php $form->widget('application.widgets.DiagnosisSelection', array(
                'field' => $side . '_diagnosis1_id',
                'element' => $element,
                'options' => CHtml::listData($l1_disorders, 'id', 'term'),
                'layout' => 'search',
                'default' => false,
                'dropdownOptions' => array('empty'=>'- Please select -', 'options' => $l1_opts),
                'label' => true,
                'layoutColumns' => array(
                    'label' => 3,
                    'field' => 9
                )
        ))?>
	</div>

	<div class="<?php if (!array_key_exists($element->{$side . '_diagnosis1_id'}, $l2_disorders)) {
    echo "hidden";
}?>" id="<?php echo $side ?>_diagnosis2_wrapper">
		<?php
            $l2_attrs =  array('empty'=>'- Please select -');
            $l2_opts = array();
            if (array_key_exists($element->{$side . '_diagnosis1_id'}, $l2_disorders)) {
                $l2_opts = $l2_disorders[$element->{$side . '_diagnosis1_id'}];
                // this is used in the javascript for checking the second level list is correct.
                $l2_attrs['data-parent_id'] = $element->{$side . '_diagnosis1_id'};
            }?>

			<?php $form->widget('application.widgets.DiagnosisSelection', array(
                    'field' => $side . '_diagnosis2_id',
                    'element' => $element,
                    'options' => CHtml::listData($l2_opts, 'id', 'term'),
                    'layout' => 'search',
                    'default' => false,
                    'dropdownOptions' => $l2_attrs,
                    'label' => true,
                    'layoutColumns' => array(
                        'label' => 3,
                        'field' => 9
                    )
            ))
        ?>
	</div>

	<?php
    $questions = $element->getInjectionQuestionsForSide($side);

    $this->renderPartial($element->form_view . '_questions', array('side' => $side, 'element' => $element, 'form' => $form, 'questions' => $questions))?>

	<?php if ($treatments = $element->getInjectionTreatments($side)) {
    ?>
		<?php echo $form->dropDownList($element, $side . '_treatment_id', CHtml::listData($treatments, 'id', 'name'), array('empty'=>'- Please select -'), false, array('label' => 3, 'field' =>6))?>
	<?php 
}?>

	<?php
        $html_options = array(
            'options' => array(),
            'empty' => '- Please select -',
            'div_id' =>  $el_model_name . '_' . $side . '_risks',
            'label' => 'Risks');
        $risks = $element->getRisksForSide($side);
        foreach ($risks as $risk) {
            $html_options['options'][(string) $risk->id] = array('data-order' => $risk->display_order);
        }
        echo $form->multiSelectList($element, $el_model_name . '[' . $side . '_risks]', $side . '_risks', 'id', CHtml::listData($risks, 'id', 'name'), array(), $html_options, false, false, null, false, false, array('label' => 3, 'field' => 6))?>

	<?php echo $form->textArea($element, $side . '_comments', array(), false, array('placeholder' => 'Enter comments'), array('label' => 3, 'field' => 9))?>
</div>
