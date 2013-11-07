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

	$layoutColumns = array('label'=>4, 'field'=>8);

	$treatments = $element->getTreatments($side);

	$treat_opts = array(
		'options' => array(),
		'empty'=>'- Please select -',
	);
	foreach ($treatments as $treatment) {
		$treat_opts['options'][(string) $treatment->id] = array('data-treeid' => $treatment->decisiontree_id, 'data-contraindications' => $treatment->contraindications_required);
	}
?>

<?php echo $form->dropDownList($element, $side . '_treatment_id', CHtml::listData($treatments,'id','name'),$treat_opts, false,$layoutColumns); ?>
<?php echo $form->datePicker($element, $side . '_angiogram_baseline_date', array('maxDate' => 'today'), array(),array_merge($layoutColumns, array('field' => 3)))?>

<div id="nice_compliance_<?php echo $side?>" class="row field-row">
	<div class="large-<?php echo $layoutColumns['label']?> column">
		<div class="field-label">NICE Compliance:</div>
	</div>
	<div class="large-<?php echo $layoutColumns['field']?> column">
		<div class="compliance-container">
			<?php $this->renderPartial(
				'form_OphCoTherapyapplication_DecisionTree',
				array('element' => $element, 'data' => $data, 'form' => $form, 'side' => $side),
				false, false
			)?>
		</div>
	</div>
</div>
