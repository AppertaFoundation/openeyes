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

	$treatments = $element->getTreatments($side);

	$treat_opts = array(
		'options' => array(),
		'empty'=>'- Please select -',
		'nowrapper' => true,
	);
	foreach ($treatments as $treatment) {
		$treat_opts['options'][(string) $treatment->id] = array('data-treeid' => $treatment->decisiontree_id, 'data-contraindications' => $treatment->contraindications_required);
	}
?>

<div class="elementField">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_treatment_id'); ?></div>
	<div class="data"><?php echo $form->dropDownList($element, $side . '_treatment_id', CHtml::listData($treatments,'id','name'),$treat_opts); ?></div>
</div>

<div class="elementField">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_angiogram_baseline_date'); ?></div>
	<div class="data"><?php echo $form->datePicker($element, $side . '_angiogram_baseline_date', array('maxDate' => 'today'), array('style'=>'width: 110px;', 'nowrapper' => true))?></div>
</div>

<div id="nice_compliance_<?php echo $side?>" class="elementField">
	<div class="label">NICE Compliance</div>
	<div class="data compliance-container">
		<?php $this->renderPartial(
			'form_OphCoTherapyapplication_DecisionTree',
			array('element' => $element, 'data' => $data, 'form' => $form, 'side' => $side),
			false, false
		)?>

	</div>

</div>
