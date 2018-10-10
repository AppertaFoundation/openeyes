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
<div class="cols-12">
    <?php if (!$this->patient->hasRiskStatus()) { ?>
        <p class="data-value flex-layout flex-top">Patient has no known risks.</p>
    <?php } else { ?>
        <div class="data-value flex-layout flex-top">
            <div class="cols-12">
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
                            <td>
                                <?php if ($element->present) { ?>
                                    <span class="large-text"><?= implode('<br>', $element->getEntriesDisplay('present')) ?></span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($element->not_checked) { ?>
                                    <span class="large-text"><?= implode('<br>', $element->getEntriesDisplay('not_checked')) ?></span>
                                <?php } ?>
                            </td>
                            <td> 
                                <?php if ($element->not_present) { ?>
                                    <span class="large-text"><?= implode('<br>', $element->getEntriesDisplay('not_present')) ?></span>
                                <?php } ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } ?>
</div>

