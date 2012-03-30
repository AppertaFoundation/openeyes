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

<div class="<?php echo $element->elementType->class_name?>"<?php if (@$hidden) {?> style="display: none;"<?php }?>>
	<h4 class="elementTypeName"><?php echo $element->elementType->name ?></h4>

	<?php echo $form->dropDownList($element, 'gas_type_id', CHtml::listData(GasType::model()->findAll(),'id','name'),array('empty'=>'- Please select -'))?>
	<?php echo $form->dropDownList($element, 'gas_percentage_id', CHtml::listData(GasPercentage::model()->findAll(),'id','value'),array('empty'=>'- Please select -'))?>
	<?php echo $form->dropDownList($element, 'gas_volume_id', CHtml::listData(GasVolume::model()->findAll(),'id','value'),array('empty'=>'- Please select -'))?>
</div>
