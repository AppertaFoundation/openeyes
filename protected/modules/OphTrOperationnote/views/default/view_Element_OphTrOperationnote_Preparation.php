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

<?php if ($element->getSetting('fife')) { ?>
  <h4><?php echo $element->elementType->name ?></h4>
  <div class="colsX clearfix">
    <div class="colStack">
      <h4><?=\CHtml::encode($element->getAttributeLabel('spo2')) ?></h4>
      <div class="eventHighlight">
        <h4><?=\CHtml::encode($element->spo2) ?>%</h4>
      </div>
    </div>
    <div class="colStack">
      <h4><?=\CHtml::encode($element->getAttributeLabel('oxygen')) ?></h4>
      <div class="eventHighlight">
        <h4><?=\CHtml::encode($element->oxygen) ?>%</h4>
      </div>
    </div>
    <div class="colStack">
      <h4><?=\CHtml::encode($element->getAttributeLabel('pulse')) ?></h4>
      <div class="eventHighlight">
        <h4><?=\CHtml::encode($element->pulse) ?></h4>
      </div>
    </div>
    <div class="colStack">
      <h4><?=\CHtml::encode($element->getAttributeLabel('intraocular_solution_id')) ?></h4>
      <div class="eventHighlight">
        <h4><?php echo $element->intraocular_solution ? $element->intraocular_solution->name : 'Not specified' ?></h4>
      </div>
    </div>
    <div class="colStack">
      <h4><?=\CHtml::encode($element->getAttributeLabel('skin_preparation_id')) ?></h4>
      <div class="eventHighlight">
        <h4><?php echo $element->skin_preparation ? $element->skin_preparation->name : 'Not specified' ?></h4>
      </div>
    </div>
  </div>
<?php } ?>
