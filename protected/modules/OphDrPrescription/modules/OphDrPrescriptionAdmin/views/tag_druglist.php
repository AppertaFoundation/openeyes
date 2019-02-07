<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$items_count = count($items);
?>
<div class="data-group" id="div_Tag_drugs">
  <div class="cols-2 column">
    <label>Drugs tagged</label>
  </div>
  <div class="cols-5 column end">
      <?php if ($items_count > 0): ?>
        <table class="generic-admin" id="drugs_list">
          <thead>
          <tr>
            <th>Drug name</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($items as $i): ?>
            <tr>
              <td><?php echo htmlentities($i->name); ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>
          No drugs have been tagged yet.
        </p>
      <?php endif; ?>
  </div>
</div>
