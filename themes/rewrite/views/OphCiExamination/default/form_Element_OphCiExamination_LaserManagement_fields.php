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

<div class="eventDetail lasertype">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_lasertype_id'); ?></div>
	<div class="data"><?php echo CHtml::activeDropDownList($element,$side . '_lasertype_id', CHtml::listData($lasertypes,'id','name'), array('options' => $lasertype_options, 'empty'=>'- Please select -'))?></div>
</div>

<?php 
	$show_other = false;
	if (@$_POST[get_class($element)]) {
		if ($lasertype = OphCiExamination_LaserManagement_LaserType::model()->findByPk((int)@$_POST[get_class($element)][$side . '_lasertype_id'])) {
			$show_other = $lasertype->other;
		}
	} else {
		if ($lasertype = $element->{$side . '_lasertype'}) {
			$show_other = $lasertype->other;
		}
	}
?>

<div class="eventDetail lasertype_other<?php if (!$show_other) { echo " hidden"; }?>">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_lasertype_other'); ?></div>
	<div class="data"><?php echo $form->textField($element, $side . '_lasertype_other',array('max' => 120, 'nowrapper' => true))?></div>
</div>

<div class="eventDetail comments">
	<div class="label"><?php echo $element->getAttributeLabel($side . '_comments'); ?></div>
	<div class="data"><?php echo $form->textArea($element, $side . '_comments',array('rows' => 4, 'cols' => 50, 'nowrapper' => true))?></div>
</div>