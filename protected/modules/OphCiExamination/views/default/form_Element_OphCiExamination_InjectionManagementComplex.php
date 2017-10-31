<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
$no_treatment_reasons = $element->getNoTreatmentReasons();
$no_treatment_reasons_opts = array(
    'options' => array(),
    'empty' => '- Please select -',
    'nowrapper' => true,
);
foreach ($no_treatment_reasons as $ntr) {
    $no_treatment_reasons_opts['options'][$ntr->id] = array('data-other' => $ntr->other ? '1' : '0');
}
?>


<?php
// build up data structures for the two levels of disorders that are mapped through the therapydisorder lookup
$l1_disorders = $element->getLevel1Disorders();
$l1_options = array();
$l2_disorders = array();

foreach ($l1_disorders as $disorder) {
    if ($td_l2 = $element->getLevel2Disorders($disorder)) {
        $jsn_arry = array();
        foreach ($td_l2 as $l2) {
            $jsn_arry[] = array('id' => $l2->id, 'term' => $l2->term);
        }
        $l1_options[$disorder->id] = array('data-level2' => CJSON::encode($jsn_arry));
        $l2_disorders[$disorder->id] = $td_l2;
    }
}
?>
<div class="sub-element-fields element-eyes row">
	<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
	<div class="element-eye right-eye column left side<?php if (!$element->hasRight()) {
    ?> inactive<?php 
}?>" data-side="right">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<?php $this->renderPartial($element->form_view.'_fields', array(
                'side' => 'right',
                'element' => $element,
                'form' => $form,
                'no_treatment_reasons' => $no_treatment_reasons,
                'no_treatment_reasons_opts' => $no_treatment_reasons_opts,
                'l1_disorders' => $l1_disorders,
                'l1_opts' => $l1_options,
                'l2_disorders' => $l2_disorders,
            ))?>
		</div>
		<div class="inactive-form">
			<div class="add-side">
				<a href="#">
					Add right side <span class="icon-add-side"></span>
				</a>
			</div>
		</div>
	</div>
	<div class="element-eye left-eye column right side<?php if (!$element->hasLeft()) {
    ?> inactive<?php 
}?>" data-side="left">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<?php $this->renderPartial($element->form_view.'_fields', array(
                'side' => 'left',
                'element' => $element,
                'form' => $form,
                'no_treatment_reasons' => $no_treatment_reasons,
                'no_treatment_reasons_opts' => $no_treatment_reasons_opts,
                'l1_disorders' => $l1_disorders,
                'l1_opts' => $l1_options,
                'l2_disorders' => $l2_disorders,
            ))?>
		</div>
		<div class="inactive-form">
			<div class="add-side">
				<a href="#">
					Add left side <span class="icon-add-side"></span>
				</a>
			</div>
		</div>
	</div>
</div>
