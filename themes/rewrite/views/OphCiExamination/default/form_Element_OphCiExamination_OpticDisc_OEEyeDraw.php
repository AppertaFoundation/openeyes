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
$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
		'doodleToolBarArray' => array('PeripapillaryAtrophy', 'DiscPallor', 'DiscHaemorrhage', 'NerveFibreDefect', 'OpticDiscPit', 'Papilloedema'),
		'onReadyCommandArray' => array(
				array('addDoodle', array('OpticDisc', array('mode' => 'Basic'))),
				array('deselectDoodles', array()),
		),
		'bindingArray' => array(
			'OpticDisc' => array(
					'mode' => array('id' => $side.'_opticdisc_mode', 'attribute' => 'data-value'),
					'cdRatio' => array('id' => 'Element_OphCiExamination_OpticDisc_'.$side.'_cd_ratio_id', 'attribute' => 'data-value'),
			),
		),
		'idSuffix' => $side.'_'.$element->elementType->id,
		'side' => ($side == 'right') ? 'R' : 'L',
		'mode' => 'edit',
		'model' => $element,
		'attribute' => $side.'_eyedraw',
))?>

<div class="eyedrawFields">
	<div>
		<div class="label">Mode:</div>
		<div class="data">
			<?php echo CHtml::dropDownList($side.'_opticdisc_mode', 'Basic', array('Basic' => 'Basic', 'Expert' => 'Expert'), array(
				'class' => 'opticdisc-mode',
				'options' => array(
					'Basic' => array('data-value' => 'Basic'),
					'Expert' => array('data-value' => 'Expert'),
				),
			)) ?>
		</div>
	</div>
	<div>
		<div class="label">
			<?php echo $element->getAttributeLabel($side.'_cd_ratio_id'); ?>
			:
		</div>
		<div class="data">
			<?php
			$cd_ratio_html_options = array('class' => 'cd-ratio', 'options' => array());
			foreach (OphCiExamination_OpticDisc_CDRatio::model()->findAll(array('order'=>'display_order')) as $ratio) {
				$cd_ratio_html_options['options'][(string) $ratio->id] = array('data-value'=> $ratio->name);
			}
			?>
			<?php echo CHtml::activeDropDownList($element, $side . '_cd_ratio_id', CHtml::listData(OphCiExamination_OpticDisc_CDRatio::model()->findAll(array('order'=>'display_order')),'id','name'), $cd_ratio_html_options) ?>
		</div>
	</div>
	<div>
		<div class="label">
			<?php echo $element->getAttributeLabel($side.'_diameter'); ?>:
		</div>
		<div class="data">
			<?php echo CHtml::activeTextField($element, $side.'_diameter', array('class' => 'diameter')) ?> mm
			(lens <?php echo CHtml::activeDropDownList($element, $side.'_lens_id', $element->getLensOptions(), array('empty' => '--')) ?>)
		</div>
	</div>
	<div>
		<div class="label">
			<?php echo $element->getAttributeLabel($side.'_description'); ?>
			:
		</div>
		<div class="data">
			<?php echo CHtml::activeTextArea($element, $side.'_description', array('rows' => "2", 'cols' => "20", 'class' => 'autosize clearWithEyedraw')) ?>
		</div>
	</div>
	<button class="ed_report">Report</button>
	<button class="ed_clear">Clear</button>
</div>
