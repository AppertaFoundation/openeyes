<?php /**
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

echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField'));
$lprocs = OphTrLaser_LaserProcedure::model()->with(array('procedure'))->findAll(array('order'=>'procedure.term asc'));
$procs = array();
foreach ($lprocs as $lproc) {
	$procs[] = $lproc->procedure;
}
?>

<div class="element-fields element-eyes row">
	<div class="element-eye right-eye column">
		<a href="#" class="icon-remove-side">Remove eye</a>
		<?php
		$form->multiSelectList(
			$element,
			'treatment_right_procedures',
			'right_procedures', 'id', CHtml::listData($procs, 'id', 'term'), array(), array('empty' => '- Procedures -', 'label' => $element->getAttributeLabel('procedures')));
		?>
	</div>
	<div class="element-eye left-eye column">
		<a href="#" class="icon-remove-side">Remove eye</a>
		<?php
		$form->multiSelectList(
			$element,
			'treatment_left_procedures',
			'left_procedures', 'id', CHtml::listData($procs, 'id', 'term'), array(), array('empty' => '- Procedures -', 'label' => $element->getAttributeLabel('procedures')));
		?>
	</div>
</div>
