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
?>

<?php
	$no_treatment_reasons = $element->getNoTreatmentReasons();
	$no_treatment_reasons_opts = array(
		'options' => array(),
		'empty'=>'- Please select -',
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

<div class="cols2 clearfix">
	<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
	<div
		class="side left eventDetail<?php if (!$element->hasRight()) { ?> inactive<?php } ?>"
		data-side="right">
		<div class="activeForm">
			<a href="#" class="removeSide">-</a>
			<?php $this->renderPartial('form_' . get_class($element) . '_fields',
				array('side' => 'right', 'element' => $element, 'form' => $form,
					'no_treatment_reasons' => $no_treatment_reasons, 'no_treatment_reasons_opts' => $no_treatment_reasons_opts,
					'l1_disorders' => $l1_disorders, 'l1_opts' => $l1_options, 'l2_disorders' => $l2_disorders,
				)); ?>
		</div>
		<div class="inactiveForm">
			<a href="#">Add right side</a>
		</div>
	</div>
	<div
		class="side right eventDetail<?php if (!$element->hasLeft()) { ?> inactive<?php } ?>"
		data-side="left">
		<div class="activeForm">
			<a href="#" class="removeSide">-</a>
			<?php $this->renderPartial('form_' . get_class($element) . '_fields',
				array('side' => 'left', 'element' => $element, 'form' => $form,
					'no_treatment_reasons' => $no_treatment_reasons, 'no_treatment_reasons_opts' => $no_treatment_reasons_opts,
					'l1_disorders' => $l1_disorders, 'l1_opts' => $l1_options, 'l2_disorders' => $l2_disorders,
				)); ?>
		</div>
		<div class="inactiveForm">
			<a href="#">Add left side</a>
		</div>
	</div>
</div>
