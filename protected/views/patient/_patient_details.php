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
        <h3 class="element-title">Personal Details</h3>
    </header>
    <div class="element-data full-width js-toggle-body">
        <table class="standard">
            <colgroup>
                <col class="cols-1">
                <col class="cols-2">
            </colgroup>
            <tbody>
            <tr class="data-group">
                <td class="data-label">First name(s):</td>
                <td class="data-value"><?= $this->patient->first_name ?></td>
            </tr>
            <tr class="data-group">
                <td class="data-label">Last name:</td>
                <td class="data-value"><?= $this->patient->last_name ?></td>
            </tr>
            <tr class="data-group">
                <td class="data-label">Address:</td>
                <td class="data-value"><?= $this->patient->getSummaryAddress() ?></td>
            </tr>
            <tr class="data-group">
                <td class="data-label">Date of Birth:</td>
                <td class="data-value"><?= ($this->patient->dob) ? $this->patient->NHSDate('dob') : 'Unknown' ?></td>
            </tr>
            <tr class="data-group">
                <td class="data-label"><?= $this->patient->isDeceased() ? "Deceased:" : "Age:" ?></td>
                <td class="data-value"><?= $this->patient->isDeceased() ? "Yes" : $this->patient->getAge() ?></td>
            </tr>
            <?php if ($this->patient->isDeceased()) :?>
                <tr class="data-group">
                    <td class="data-label">Date of Death:</td>
                    <td class="data-value"><?= $this->patient->date_of_death ? $this->patient->NHSDate('date_of_death').' (Age '.$this->patient->getAge().')' : "Date of Patient's death unknown." ?></td>
                </tr>
            <?php endif; ?>
            <tr class="data-group">
                <td class="data-label">Sex:</td>
                <td class="data-value"><?= $this->patient->getGenderString() ?></td>
            </tr>
            <tr class="data-group">
                <td class="data-label">Ethnic Group:</td>
                <td class="data-value"><?= $this->patient->getEthnicGroupString() ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</section>
