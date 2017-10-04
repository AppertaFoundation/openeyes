<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="element-fields">
	<?php echo $form->dropDownList($element, 'respiratory_rate_id', 'OphOuAnaestheticsatisfactionaudit_VitalSigns_RespiratoryRate', array('empty' => '- Select -'), false, array('field' => 3))?>
	<?php echo $form->dropDownList($element, 'oxygen_saturation_id', 'OphOuAnaestheticsatisfactionaudit_VitalSigns_OxygenSaturation', array('empty' => '- Select -'), false, array('field' => 3))?>
	<?php echo $form->dropDownList($element, 'systolic_id', 'OphOuAnaestheticsatisfactionaudit_VitalSigns_Systolic', array('empty' => '- Select -'), false, array('field' => 3))?>
	<?php echo $form->dropDownList($element, 'body_temp_id', 'OphOuAnaestheticsatisfactionaudit_VitalSigns_BodyTemp', array('empty' => '- Select -'), false, array('field' => 3))?>
	<?php echo $form->dropDownList($element, 'heart_rate_id', 'OphOuAnaestheticsatisfactionaudit_VitalSigns_HeartRate', array('empty' => '- Select -'), false, array('field' => 3))?>
	<?php echo $form->dropDownList($element, 'conscious_lvl_id', 'OphOuAnaestheticsatisfactionaudit_VitalSigns_ConsciousLvl', array('empty' => '- Select -'), false, array('field' => 3))?>
</div>