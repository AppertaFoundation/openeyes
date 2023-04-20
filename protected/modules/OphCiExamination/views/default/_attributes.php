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
if ($element->elementType->name!="Clinic Outcome") :
    ?>
<div class="close-icon-btn">
  <i class="oe-i remove-circle medium"></i>
</div>
<div class="select-icon-btn">
  <i class="oe-i menu selected"></i>
</div>
<button class="button hint green add-icon-btn" type="button">
  <i class="oe-i plus pro-theme"></i>
</button>
<?php endif; ?>
<table class="select-options cols-full">
  <tbody>
  <tr>
        <?php
        // This is now deprecate and needs to be removed in the future, it is currently only used for editing legacy events see: OE-13648
        $criteria = new CDbCriteria();
        $criteria->addCondition('service_subspecialty_assignment_id IS NOT NULL');
        $firms = Firm::model()->findAllAtLevels(ReferenceData::LEVEL_ALL, $criteria);
        $firm_name = array();
        $firm_sub_id = array();
        foreach ($firms as $firm) {
            array_push($firm_name, $firm->name);
            array_push($firm_sub_id, $firm->serviceSubspecialtyAssignment->subspecialty_id);
            $attributes = $this->getAttributes($element, $firm->serviceSubspecialtyAssignment->subspecialty_id);
        }
        foreach ($this->getAttributes($element, $firm->serviceSubspecialtyAssignment->subspecialty_id) as $attribute) { ?>
    <td>
      <div class="flex-layout flex-top flex-left">
        <ul class="add-options cols-full" data-multi="false" data-clickadd="false">
              <?php foreach ($attribute->getAttributeOptions() as $option) { ?>
              <li data-value="<?php echo (string)$option->slug; ?>">
                <span class="restrict-width"><?php echo (string)$option->slug; ?></span>
              </li>
                <?php }
        } ?>
        </ul>
      </div>
    </td>
  </tr>
  </tbody>
</table>
