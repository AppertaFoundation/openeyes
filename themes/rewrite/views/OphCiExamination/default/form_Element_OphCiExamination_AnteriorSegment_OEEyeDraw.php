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
		'doodleToolBarArray' => array('NuclearCataract','CorticalCataract','PostSubcapCataract','PCIOL','ACIOL','Bleb','PI','Fuchs','RK','LasikFlap','CornealScar','SectorIridectomy','PosteriorSynechia','Rubeosis'),
		'onReadyCommandArray' => array(
				array('addDoodle', array('AntSeg')),
				array('deselectDoodles', array()),
		),
		'bindingArray' => array(
			'AntSeg' => array(
					'pupilSize' => array('id' => 'Element_OphCiExamination_AnteriorSegment_'.$side.'_pupil_id', 'attribute' => 'data-value'),
					'pxe' => array('id' => 'Element_OphCiExamination_AnteriorSegment_'.$side.'_pxe'),
			),
			'NuclearCataract' => array(
					'grade' => array('id' => 'Element_OphCiExamination_AnteriorSegment_'.$side.'_nuclear_id', 'attribute' => 'data-value'),
			),
			'CorticalCataract' => array(
					'grade' => array('id' => 'Element_OphCiExamination_AnteriorSegment_'.$side.'_cortical_id', 'attribute' => 'data-value'),
			),
		),
		'deleteValueArray' => array(
			'Element_OphCiExamination_AnteriorSegment_'.$side.'_nuclear_id' => '',
			'Element_OphCiExamination_AnteriorSegment_'.$side.'_cortical_id' => '',
		),
		'idSuffix' => $side.'_'.$element->elementType->id,
		'side' => ($side == 'right') ? 'R' : 'L',
		'mode' => 'edit',
		'width' => 300,
		'height' => 300,
		'model' => $element,
		'attribute' => $side.'_eyedraw',
));
?>
<div class="eyedrawFields">
	<div class="aligned">
		<div class="label">
			<?php echo $element->getAttributeLabel($side.'_pupil_id'); ?>:
		</div>
		<div class="data">
			<?php
				$html_options = array();
				foreach (OphCiExamination_AnteriorSegment_Pupil::model()->findAll(array('order'=>'display_order')) as $option) {
					$html_options[(string) $option->id] = array('data-value'=> $option->value);
				}
				echo CHtml::activeDropDownList($element, $side.'_pupil_id', CHtml::listData(OphCiExamination_AnteriorSegment_Pupil::model()->findAll(array('order'=>'display_order')),
				'id','name'), array('options' => $html_options));
			?>
		</div>
	</div>
	<div class="aligned">
		<div class="label">
			<?php echo $element->getAttributeLabel($side.'_nuclear_id'); ?>:
		</div>
		<div class="data">
			<?php
				$html_options = array();
				foreach (OphCiExamination_AnteriorSegment_Nuclear::model()->findAll(array('order'=>'display_order')) as $option) {
					$html_options[(string) $option->id] = array('data-value'=> $option->value);
				}
				echo CHtml::activeDropDownList($element, $side.'_nuclear_id', CHtml::listData(OphCiExamination_AnteriorSegment_Nuclear::model()->findAll(array('order'=>'display_order')),
				'id','name'), array('options' => $html_options));
			?>
		</div>
	</div>
	<div class="aligned">
		<div class="label">
			<?php echo $element->getAttributeLabel($side.'_cortical_id'); ?>:
		</div>
		<div class="data">
			<?php
				$html_options = array();
				foreach (OphCiExamination_AnteriorSegment_Cortical::model()->findAll(array('order'=>'display_order')) as $option) {
					$html_options[(string) $option->id] = array('data-value'=> $option->value);
				}
				echo CHtml::activeDropDownList($element, $side.'_cortical_id', CHtml::listData(OphCiExamination_AnteriorSegment_Cortical::model()->findAll(array('order'=>'display_order')),
				'id','name'), array('options' => $html_options));
			?>
		</div>
	</div>
	<div>
		<div class="label">
			<?php echo $element->getAttributeLabel($side.'_description'); ?>:
		</div>
		<div class="data">
			<?php echo CHtml::activeTextArea($element, $side.'_description', array('rows' => "2", 'cols' => "20", 'class' => 'autosize clearWithEyedraw')) ?>
		</div>
	</div>
	<div>
		<div class="data">
			<?php echo CHtml::activeCheckBox($element, $side.'_pxe', array('class' => 'clearWithEyedraw')) ?>
		</div>
		<div class="label">
			<?php echo $element->getAttributeLabel($side.'_pxe'); ?>
		</div>
	</div>
	<div>
		<div class="data">
			<?php echo CHtml::activeCheckBox($element, $side.'_phako', array('class' => 'clearWithEyedraw')) ?>
		</div>
		<div class="label">
			<?php echo $element->getAttributeLabel($side.'_phako'); ?>
		</div>
	</div>
	<button class="ed_report">Report</button>
	<button class="ed_clear">Clear</button>
</div>
