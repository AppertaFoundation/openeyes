<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="element-data full-width">
    <table class="last-left">
        <colgroup>
            <col class="cols-3"/>
            <col class="cols-8"/>
        </colgroup>
        <tbody>
        <tr>
            <td>
                <span class="data-label fade"><?= $element->getAttributeLabel('correspondence_in_large_letters') ?></span>
            </td>
            <td>
                <span class="data-value large-text"><?= $element->correspondence_in_large_letters ? 'Yes' : 'No' ?></span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="data-label fade"><?= $element->getAttributeLabel('agrees_to_insecure_email_correspondence') ?></span>
            </td>
            <td>
                <span class="data-value large-text"><?= $element->agrees_to_insecure_email_correspondence ? 'Yes' : 'No' ?></span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="data-label fade"><?= $element->getAttributeLabel('language_id') ?></span>
            </td>
            <td>
                <span class="data-value large-text"><?= (!is_null($element->language_id)) ? $element->language->name : 'Unknown' ?></span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="data-label fade"><?= $element->getAttributeLabel('interpreter_required_id') ?></span>
            </td>
            <td>
                <span class="data-value large-text"><?= (!is_null($element->interpreter_required_id)) ? $element->interpreter_required->name : 'N/A' ?></span>
            </td>
        </tr>
        </tbody>
    </table>
</div>