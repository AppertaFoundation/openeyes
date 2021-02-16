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
<section class="element view full patient-info js-toggle-container">
    <header class="element-header">
        <h3 class="element-title"><?= \SettingMetadata::model()->getSetting('general_practitioner_label') ?: 'General Practitioner'?></h3>
    </header>
    <div class="element-data full-width js-toggle-body">
        <table class="standard">
            <colgroup>
                <col class="cols-1">
                <col class="cols-2">
            </colgroup>
            <tbody>
            <tr class="data-group">
                <td class="data-label">Name:</td>
                <td class="data-value"><?= ($this->patient->gp) ? $this->patient->gp->contact->fullName : 'Unknown' ?></td>
            </tr>
            <?php if (Yii::app()->user->checkAccess('admin')) { ?>
                <tr class="data-group">
                    <td class="data-label"><?= \SettingMetadata::model()->getSetting('gp_label') ?: 'GP'?> Address:</td>
                    <td class="data-value"><?= ($this->patient->gp && $this->patient->gp->contact->address) ? $this->patient->gp->contact->address->letterLine : 'Unknown' ?></td>
                </tr>
                <tr class="data-group">
                    <td class="data-label"><?= \SettingMetadata::model()->getSetting('gp_label') ?: 'GP'?> Telephone:</td>
                    <td class="data-value"><?= ($this->patient->gp && $this->patient->gp->contact->primary_phone) ? $this->patient->gp->contact->primary_phone : 'Unknown' ?></td>
                </tr>
            <?php } ?>
            <tr class="data-group">
                <td class="data-label">Practice Address:</td>
                <td class="data-value"><?= ($this->patient->practice && $this->patient->practice->contact->address) ? $this->patient->practice->contact->address->letterLine : 'Unknown' ?></td>
            </tr>
            <tr class="data-group">
                <td class="data-label">Practice Telephone:</td>
                <td class="data-value"><?= ($this->patient->practice && $this->patient->practice->phone) ? $this->patient->practice->phone : 'Unknown' ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</section>
