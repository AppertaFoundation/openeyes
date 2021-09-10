<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * tdis file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under tde terms of tde GNU Affero General Public License as published by tde Free Software Foundation, eitder version 3 of tde License, or (at your option) any later version.
 * OpenEyes is distributed in tde hope tdat it will be useful, but WItdOUT ANY WARRANTY; witdout even tde implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See tde GNU Affero General Public License for more details.
 * You should have received a copy of tde GNU Affero General Public License along witd OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @autdor OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html tde GNU Affero General Public License V3.0
 */
?>
<table  class="cols-full last-left">
    <colgroup>
        <col class="cols-6">
        <col class="cols-6">
    </colgroup>
    <tbody>
        <tr>
            <td>Contact name: </td>
            <td><?= $contact->getFullName() ?></td>
        </tr>
        <tr>
            <td>Email: </td>
            <td><?= $contact->email ?? 'N\A' ?></td>
        </tr>
        <tr>
            <td>Phone number: </td>
            <td><?= $contact->phone_number ?? 'N\A' ?></td>
        </tr>
        <tr>
            <td>Mobil number: </td>
            <td><?= $contact->mobile_number ?? 'N\A' ?></td>
        </tr>
        <tr>
            <td>Address line 1: </td>
            <td><?= $contact->address_line1 ?? 'N\A' ?></td>
        </tr>
        <tr>
            <td>Address line 2: </td>
            <td><?= $contact->address_line2 ?? 'N\A' ?></td>
        </tr>
        <tr>
            <td>City: </td>
            <td><?= $contact->city ?? 'N\A' ?></td>
        </tr>
        <tr>
            <td>Country: </td>
            <td>
                <?= isset($contact->country) ? $contact->country->name : 'N\A' ?>
            </td>
        </tr>
        <tr>
            <td>Relationship: </td>
            <td><?= \CHtml::encode($contact->consentPatientRelationship->name); ?></td>
        </tr>
    </tbody>
</table>