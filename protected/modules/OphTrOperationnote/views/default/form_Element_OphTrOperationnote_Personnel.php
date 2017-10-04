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

<?php if ($element->getSetting('fife')) {?>
	<div class="element-fields">
		<?php echo $form->dropDownListRow(
            $element,
            array(
                'scrub_nurse_id',
                'floor_nurse_id',
                'accompanying_nurse_id',
            ),
            array(
                CHtml::listData($element->scrub_nurses, 'id', 'FullName'),
                CHtml::listData($element->floor_nurses, 'id', 'FullName'),
                CHtml::listData($element->accompanying_nurses, 'id', 'FullName'),
            ),
            array(
                array('empty' => '- Please select -'),
                array('empty' => '- Please select -'),
                array('empty' => '- Please select -'),
            )
        )?>
		<?php echo $form->dropDownList($element, 'operating_department_practitioner_id', CHtml::listData($element->operating_department_practitioners, 'id', 'FullName'), array('empty' => '- Please select -'), $element->operating_department_practitioner_id)?>
	</div>
<?php }?>
