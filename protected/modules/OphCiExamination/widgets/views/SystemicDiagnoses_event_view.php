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
$widget = $this;

$checkedRequiredSystemicDiagnoses = $this->getCheckedRequiredSystemicDiagnoses();
$missingRequiredSystemicDiagnoses = $this->getMissingRequiredSystemicDiagnoses()

?>

<div class="element-data">
    <?php if (!$element->orderedDiagnoses && !$checkedRequiredSystemicDiagnoses && !$missingRequiredSystemicDiagnoses && !$element->no_systemic_diagnoses_date) { ?>
      <div class="data-value not-recorded">
          Nil recorded this examination
      </div>
    <?php } elseif ($element->no_systemic_diagnoses_date) { ?>
        <div class="data-value">
            Patient has no known Systemic Diagnoses
        </div>
    <?php } else { ?>
        <div class="data-value">
            <div class="tile-data-overflow">
                <table>
                    <colgroup>
                        <col class="cols-8">
                        <col>
                    </colgroup>
                    <tbody>
                    <?php foreach ($element->orderedDiagnoses as $diag) { ?>
                        <tr>
                            <td>
                                <?= $diag->disorder; ?>
                            </td>
                            <td class="nowrap flex-layout">
                                <?php
                                if(isset($diag->side)){
                                    $this->widget('EyeLateralityWidget', array('laterality' => $diag->side));
                                } else {
                                    echo '<span class="oe-eye-lat-icons"></span>';
                                }
                                ?>
                                <span class="oe-date"><?= $diag->getHTMLformatedDate() ?></span>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php foreach ($checkedRequiredSystemicDiagnoses as $diag) { ?>
                        <tr>
                            <td>
                                <?= $diag->disorder; ?><strong>  (Not present)</strong>
                            </td>
                            <td class="nowrap flex-layout">
                                <?php $this->widget('EyeLateralityWidget', array('eye' => $diag->side)) ?>
                                <span class="oe-date"><?= $diag->getHTMLformatedDate() ?></span>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php foreach ($missingRequiredSystemicDiagnoses as $diag) { ?>
                        <tr>
                            <td>
                                <?= $diag->disorder; ?><strong>  (Not checked)</strong>
                            </td>
                            <td class="nowrap flex-layout">
                                <?php $this->widget('EyeLateralityWidget', array('eye' => $diag->side)) ?>
                                <span class="oe-date"><?= $diag->getHTMLformatedDate() ?></span>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } ?>
</div>
