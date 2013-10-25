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
<?php echo CHtml::dropDownList(get_class($element).'_'.$field.'_sign', ($element->$field > 0) ? 1 : -1, CHtml::listData(OphCiExamination_Refraction_Sign::model()->findAll(array('order'=>'display_order')), 'name', 'value'))?>
<?php echo CHtml::dropDownList(get_class($element).'_'.$field.'_integer', abs((int) $element->$field), CHtml::listData(OphCiExamination_Refraction_Integer::model()->findAll(array('order'=>'display_order')),'value','value'))?>
<?php echo CHtml::dropDownList(get_class($element).'_'.$field.'_fraction', number_format(abs($element->$field) - (abs((int) $element->$field)),2), CHtml::listData(OphCiExamination_Refraction_Fraction::model()->findAll(array('order'=>'display_order')),'name','value'))?>
<?php echo CHtml::activeHiddenField($element, $field)?>
