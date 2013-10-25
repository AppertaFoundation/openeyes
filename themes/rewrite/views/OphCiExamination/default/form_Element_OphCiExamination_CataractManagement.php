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
<div id="div_Element_OphCiExamination_CataractManagement_eye_id" class="eventDetail">
	<div class="data">
		<?php echo $form->radioButtons($element, 'eye_id', 'ophciexamination_cataractmanagement_eye',null,false,false,false,false,array('nowrapper'=>true))?>
	</div>
</div>
<div id="div_Element_OphCiExamination_CataractManagement_city_road" class="eventDetail">
	<div class="data">
		<?php echo $form->checkbox($element, 'city_road', array('no-label' => true, 'nowrapper' => true))?>
		<?php echo $element->getAttributeLabel('city_road')?>
		<?php echo $form->checkbox($element, 'satellite', array('no-label' => true, 'nowrapper' => true))?>
		<?php echo $element->getAttributeLabel('satellite')?>
	</div>
</div>
<?php echo $form->checkbox($element, 'fast_track', array('text-align' => 'right', 'no-label' => true))?>
<?php echo $form->slider($element, 'target_postop_refraction', array('min'=>-20,'max'=>20,'step'=>0.5))?>
<?php echo $form->radioBoolean($element, 'correction_discussed')?>
<div id="div_<?php echo get_class($element)?>_suitable_for_surgeon_id"
	class="eventDetail">
	<div class="label">
		<?php echo $element->getAttributeLabel('suitable_for_surgeon_id')?>
		:
	</div>
	<div class="data">
		<?php echo CHtml::activeDropDownList($element,'suitable_for_surgeon_id', CHtml::listData(OphCiExamination_SuitableForSurgeon::model()->findAll(array('order'=>'display_order')),'id','name'),array('empty'=>'- Please select -'))?>
		<?php echo $form->checkbox($element, 'supervised', array('nowrapper' => true))?>
		<?php echo $element->getAttributeLabel('supervised')?>
	</div>
</div>
<?php echo $form->radioBoolean($element, 'previous_refractive_surgery')?>
<?php echo $form->radioBoolean($element, 'vitrectomised_eye')?>
