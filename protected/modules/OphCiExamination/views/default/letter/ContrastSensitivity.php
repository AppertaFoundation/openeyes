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

use OEModule\OphCiExamination\models\ContrastSensitivity_Result;
?>

<?php
if (count($element->results) > 0) {
    ?>
    <table>
        <thead>
        <tr>
            <th>Contrast Sensitivity</th>
            <th style="text-align: center">Right</th>
            <th style="text-align: center">Binocular</th>
            <th style="text-align: center">Left</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $ordered_results = $element->getResultsGroupedByTestType();
        foreach ($ordered_results as $test_type => $results) {
            ?>
            <tr>
                <td><?= $test_type ?></td>
                <td style="text-align: center"><?= $results[ContrastSensitivity_Result::RIGHT] ?? "-" ?></td>
                <td style="text-align: center"><?= $results[ContrastSensitivity_Result::BEO] ?? "-" ?></td>
                <td style="text-align: center"><?= $results[ContrastSensitivity_Result::LEFT] ?? "-" ?></td>
            </tr>
            <?php
        };

        if ($element->comments) {
            ?>
            <tr>
                <td colspan="5">
                    <?= $element->comments ?>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <?php
} else {
    ?>No results<?php
}