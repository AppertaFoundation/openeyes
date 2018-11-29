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
    <div class="data-group">
      <?php if (!$this->patient->hasRiskStatus()) { ?>
        <div class="data-value flex-layout flex-top">Patient has no known risks.</div>
      <?php } else { ?>
          <div class="data-value flex-layout flex-top">
            <div class="cols-11">
            <div class="cols-11" id="js-listview-risks-pro" style>
<!--                Anticoagulants and alpha blockers being mandatory risk items to be displayed, we check if $element contains these in either yes, or no and if it doesn't in either, we display it as unchecked forcefully-->
                <?php
                $anticoagulants = false;
                $alphablockers = false;
                $entries = array_merge($element->getEntriesDisplay('present') , $element->getEntriesDisplay('not_present'));

                foreach ($entries as $entry) {
                    if (strpos($entry . ' ', 'Anticoagulants') !== false) {
                        $anticoagulants = true;
                    }

                    if (strpos($entry, 'Alpha blockers') !== false) {
                        $alphablockers = true;
                    }
                }

                if ($anticoagulants == false) {
                    $not_checked_required_risks[] = 'Anticoagulants';
                }
                if ($alphablockers == false) {
                    $not_checked_required_risks[] = 'Alpha blockers';
                } ?>
                <ul class="dot-list large">
                    <?php if ($element->present) { ?>
                        <li>Present:</li>
                        <?php foreach ($element->getEntriesDisplay('present') as $entry) { ?>
                            <li><?= $entry ?></li>
                        <?php };
                    } ?>
                </ul>
                <ul class="dot-list large">
                    <?php if ($element->not_checked) { ?>
                        <li>Not Checked:</li>
                        <?php foreach ($element->getEntriesDisplay('not_checked') as $entry) { ?>
                            <li><?= $entry ?></li>
                        <?php }
                    } else if (isset($not_checked_required_risks)) { ?>
                        <li>Not Checked:</li>
                        <?php foreach ($not_checked_required_risks as $entry) { ?>
                            <li><?= $entry ?></li>
                        <?php } ?>
                    <?php } ?>
                </ul>
                <ul class="dot-list large">
                    <?php if ($element->not_present) { ?>
                        <li>Not Present:</li>
                        <?php foreach ($element->getEntriesDisplay('not_present') as $entry) { ?>
                            <li><?= $entry ?></li>
                        <?php }
                    } ?>
                </ul>
            </div>
            <div class="col-6" id="js-listview-risks-full" style="display: none;">
            <table class="last-left">
              <colgroup>
                <col class="cols-4">
                <col class="cols-4">
                <col class="cols-4">
              </colgroup>
            <thead>
              <tr>
                <th>Present</th>
                <th>Not Checked</th>
                <th>Not Present</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?php if ($element->present) { ?>
                    <span class="large-text"><?= implode('<br>', $element->getEntriesDisplay('present'))?></span>
                    <?php } ?></td>
                <td><?php if ($element->not_checked) { ?>
                    <span class="large-text"><?= implode('<br>', $element->getEntriesDisplay('not_checked')) ?></span>
                    <?php } else if (isset($not_checked_required_risks) && is_array($not_checked_required_risks)){ ?>
                    <span class="large-text"><?= implode('<br>', $not_checked_required_risks) ?></span>
                    <?php } ?>
                </td>
                <td> <?php if ($element->not_present) { ?>
                    <span class="large-text"><?= implode('<br>',$element->getEntriesDisplay('not_present'))?></span>
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

