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
<div class="segmented">
	<?php
    preg_match('/OphCiExamination_Refraction_(.*?)_Integer/', $model, $m);
    $type = strtolower($m[1]);
    echo CHtml::dropDownList(CHTML::modelName($element).'_'.$side.'_'.$field.'_sign', ($element->{$side."_".$field} > 0) ? 1 : -1, CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_Refraction_Sign::model()->findAll(), 'name', 'value'), array('class'=>'inline signField', 'data-type' => $type));
    $model = 'OEModule\OphCiExamination\models\\'.$model;
    $sign_id = ($element->{$side."_".$field} > 0) ? 1 : 2;
    echo CHtml::dropDownList(CHTML::modelName($element).'_'.$side.'_'.$field.'_integer', abs((int) $element->{$side."_".$field}), CHtml::listData($model::model()->findAll('sign_id='.$sign_id), 'value', 'value'), array('class'=>'inline'));
    echo CHtml::dropDownList(CHTML::modelName($element).'_'.$side.'_'.$field.'_fraction', number_format(abs($element->{$side."_".$field}) - (abs((int) $element->{$side."_".$field})), 2), CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_Refraction_Fraction::model()->findAll(), 'name', 'value'), array('class'=>'inline'));
    echo CHtml::activeHiddenField($element, $side."_".$field);
    ?>
</div>
