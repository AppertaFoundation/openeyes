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
<div class="close-icon-btn">
    <i class="oe-i remove-circle medium"></i>
</div>
<div class="select-icon-btn">
    <i class="oe-i menu selected"></i>
</div>
<button class="button hint green add-icon-btn">
  <i class="oe-i plus pro-theme"></i>
</button>
<table class="select-options">
    <tbody>
        <tr>
<?php
    foreach ($this->getAttributes($element, $this->firm->serviceSubspecialtyAssignment->subspecialty_id) as $attribute) { ?>
            <td>
              <div class="flex-layout flex-top flex-left">
                <ul class="add-options" data-multi="false" data-clickadd="false">
            <?php foreach ($attribute->getAttributeOptions() as $option) {?>
                  <li data-str="<?php echo (string)$option->slug ;?>">
                    <span class="restrict-width"><?php echo (string)$option->slug ; ?></span>
                  </li>
              <?php }
            } ?>
                </ul>
              </div>
            </td>
        </tr>
    </tbody>
</table>