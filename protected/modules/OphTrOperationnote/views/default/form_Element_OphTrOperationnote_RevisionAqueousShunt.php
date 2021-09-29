<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$layoutColumns = $form->layoutColumns;
$form->layoutColumns = array('label' => 3, 'field' => 3);
?>
<div class="element-fields">
    <?php $form->dropDownList($element, 'plate_pos_id', $element::PLATE_POSITIONS); ?>
    <?php $form->checkBox($element, 'is_shunt_explanted', [], array('field' => 3)) ?>
    <?php $form->dropDownList($element, 'final_tube_position_id', $element::TUBE_POSITIONS, ['empty' => '-- Please select --']); ?>
    <?php $form->dropDownList($element, 'intraluminal_stent_id', $element::RIPCORD_SUTURES); ?>
    <?php $form->checkBox($element, 'is_visco_in_ac', [], array('field' => 3)) ?>
    <?php $form->checkBox($element, 'is_flow_tested', [], array('field' => 3)) ?>
    <?php $form->textArea($element, 'comments', [], false, array('field' => 3), array('label' => 3, 'field' => 6)) ?>

</div>
<?php $form->layoutColumns = $layoutColumns;?>
