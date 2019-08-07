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
<div class="element-data element-eyes">
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
    <div class="<?= $eye_side ?>-eye">
        <?php if ($element->hasEye($eye_side)) { ?>
      <table class="element-table large">
        <colgroup>
          <col class="cols-4">
        </colgroup>
        <tbody>
            <?php foreach ($element->{$eye_side.'_treatments'} as $treatment) {?>
          <tr>
            <td><?php echo $treatment->drug->name ?></td>
            <td>
              <i class="oe-i time small no-click pad"></i>
                <?php echo date('H:i', strtotime($treatment->treatment_time)) ?>
            </td>
            <td><?php echo $treatment->drops ?> drop<?php if ($treatment->drops != 1) {
                ?>s<?php
                } ?>
            </td>
          </tr>
            <?php } ?>
        </tbody>
      </table>
        <?php } else { ?>
          <div class="data-value not-recorded">
            <span class="large-text">None given</span>
          </div>
        <?php } ?>
    </div>
    <?php endforeach; ?>
</div>
