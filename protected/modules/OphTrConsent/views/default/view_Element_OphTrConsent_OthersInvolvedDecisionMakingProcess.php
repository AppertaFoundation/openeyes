<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<div class="element-data full-width">
    <div class="flex">
        <div class="cols-12">
            <table class="cols-full last-left">
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-3">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-2">
                    <col class="cols-1">
                </colgroup>
                <thead>
                    <th>Relationship</th>
                    <th>Contact Person</th>
                    <th>Contact Method</th>
                    <th>Signature required</th>
                    <th>Comment</th>
                </thead>
                <tbody>
                    <?php foreach( $element->consentContact as $row_id => $contact ) { ?>
                        <tr>
                            <td><?= $contact->getRelationshipName() ?></td>
                            <td>
                                <?= $contact->getFullName() ?>
                                <i class="oe-i comments-who small pad-right js-has-tooltip"
                                   data-tooltip-content="<small>Contact info </small><br /><?= htmlspecialchars($contact->getContactInfo()) ?>"
                                ></i>
                            </td>

                            <td><?= $contact->getContactMethodName() ?></td>
                            <td><?= \CHtml::encode($contact->getSignatureRequiredString()); ?></td>
                            <td><?= \CHtml::encode($contact->comment) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

