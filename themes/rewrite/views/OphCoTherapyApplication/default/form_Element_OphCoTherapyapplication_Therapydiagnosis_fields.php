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

<div class="elementField">
	<div class="label" style="vertical-align: top;"><?php echo $element->getAttributeLabel($side . '_diagnosis1_id'); ?></div>
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
<div class="elementField<?php if (!array_key_exists($element->{$side . '_diagnosis1_id'}, $l2_disorders) ) { echo " hidden"; }?>" id="<?php echo $side ?>_diagnosis2_wrapper">
	<div class="label" style="vertical-align: top;"><?php echo $element->getAttributeLabel($side . '_diagnosis2_id'); ?></div>
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
