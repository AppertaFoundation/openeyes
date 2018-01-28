<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
  <!--
	*******  Element Data Type (VIEW): * Risks *
	*******  CSS: "element-data view-risks" (+ any extra css)
	*******  CSS hook used for element specific styling only where required
	*******  Only minimum required DOM and CSS for UI is shown here
	-->
  <div class="element-data full-width">
    <div class="data-row">
      <?php if ($element->no_risks_date) { ?>
        <div class="data-value flex-layout flex-top">Patient has no known risks.</div>
      <?php } else { ?>
          <div class="data-value flex-layout flex-top">
            <div class="cols-11">
            <div class="cols-11" id="js-list-view-risks-pro" style>
              <ul class="dslash-list">
                <li>YES: <?php if ($element->present) {  $element->getEntriesDisplay('present'); } ?></li>
                <li>NO: <?php if ($element->not_checked) {  $element->getEntriesDisplay('not_checked'); } ?></li>
                <li>Not checked: <?php if ($element->not_present) {  $element->getEntriesDisplay('not_present'); } ?></li>
              </ul>
            </div>
            <div class="col-6" id="js-listview-risks-full" style="display: none;">
            <table class="last-left">
            <thead>
              <tr>
                <th class="cols-4">Present</th>
                <th class="cols-4">Not Checked</th>
                <th class="cols-4">Not Present</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?php if ($element->present) { ?>
                    <span class="large-text"><?= $element->getEntriesDisplay('present') ?></span>
                    <?php } ?></td>
                <td><?php if ($element->not_checked) { ?>
                    <span class="large-text"><?= $element->getEntriesDisplay('not_checked') ?></span>
                    <?php } ?></td>
                <td> <?php if ($element->not_present) { ?>
                    <span class="large-text"><?= $element->getEntriesDisplay('not_present') ?></span>
                    <?php } ?></td>
              </tr>
            </tbody>
          </table>
        </div>
            </div>
            <div>
              <i class="oe-i small js-listview-expand-btn expand" data-list="risks"></i>
            </div>
          </div>
      <?php } ?>
    </div>
  </div>

