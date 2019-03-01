<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 *  You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<table class="label-value">
    <tbody>
        <tr>
            <td>
                <div class="data-label">
                    <?= \CHtml::encode($element->getAttributeLabel('consultant_id')) ?>
                </div>
            </td>
            <td>
                <?php echo $element->consultant ? $element->consultant->name : 'None' ?>
            </td>
        </tr>
        <?php if ($site = $element->site) { ?>
            <tr>
                <td>
                    <div class="data-label">
                        <?= \CHtml::encode($element->getAttributeLabel('site_id')) ?>:
                    </div>
                </td>
                <td>
                    <?php echo $site->name ?>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td>
                <div class="data-label">
                    <?= \CHtml::encode($element->getAttributeLabel('patient_sharedata_consent')) ?>:
                </div>
            </td>
            <td>
                <div class="data-value <?php echo is_null($element->patient_sharedata_consent) ? 'not-recorded' : '' ?>">
                    <?php echo is_null($element->patient_sharedata_consent) ? 'Not recorded' : ($element->patient_sharedata_consent ? 'Yes' : 'No') ?>
                </div>
            </td>
        </tr>
    </tbody>
</table>