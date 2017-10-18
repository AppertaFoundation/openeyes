<?php /**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
$lprocs = OphTrLaser_LaserProcedure::model()->with(array('procedure'))->findAll(array('order' => 'procedure.term asc'));
$procs = array();
foreach ($lprocs as $lproc) {
    $procs[] = $lproc->procedure;
}
$layoutColumns = array(
    'label' => 4,
    'field' => 6,
);
?>

<div class="element-fields element-eyes row">
    <?= $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')) ?>
  <div class="element-eye right-eye column side left<?php if (!$element->hasRight()) { ?> inactive<?php } ?>" data-side="right">
    <div class="active-form">
      <a href="#" class="icon-remove-side remove-side">Remove eye</a>
        <?php
        $form->multiSelectList(
            $element,
            'treatment_right_procedures',
            'right_procedures',
            'id',
            CHtml::listData($procs, 'id', 'term'),
            array(),
            array('empty' => '- Procedures -', 'label' => $element->getAttributeLabel('procedures')),
            false,
            false,
            null,
            false,
            false,
            $layoutColumns
        ); ?>
    </div>
    <div class="inactive-form">
      <div class="add-side">
        <a href="#">
          Add right side <span class="icon-add-side"></span>
        </a>
      </div>
    </div>
  </div>
  <div class="element-eye left-eye column side right<?php if (!$element->hasLeft()) { ?> inactive<?php } ?>" data-side="left">
    <div class="active-form">
      <a href="#" class="icon-remove-side remove-side">Remove eye</a>
        <?php
        $form->multiSelectList(
            $element,
            'treatment_left_procedures',
            'left_procedures',
            'id',
            CHtml::listData($procs, 'id', 'term'),
            array(),
            array('empty' => '- Procedures -', 'label' => $element->getAttributeLabel('procedures')),
            false,
            false,
            null,
            false,
            false,
            $layoutColumns
        );
        ?>
    </div>
    <div class="inactive-form">
      <div class="add-side">
        <a href="#">
          Add left side <span class="icon-add-side"></span>
        </a>
      </div>
    </div>
  </div>
</div>
