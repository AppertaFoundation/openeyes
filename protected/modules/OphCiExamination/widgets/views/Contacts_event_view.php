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

<?php

$model_name = CHtml::modelName($element);
$element_errors = $element->getErrors();
?>
<div class="element-fields full-width" id="<?= $model_name ?>_element">
    <div class="data-group cols-full">


        <div class="cols-full">
            <table id="<?= $model_name ?>_entry_table"
                   class=" cols-full <?php echo $element_errors ? 'highlighted-error error' : '' ?>">
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-2">
                    <col>
                    <col>
                    <col class="cols-2">
                </colgroup>
                <thead>
                <tr>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Comments</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <?php $gp_contact = $this->patient->gp->contact; ?>
                    <td><?= $gp_contact->label ? $gp_contact->label->name : "" ?></td>
                    <td><?= $gp_contact->getFullName() ?></td>
                    <td><?= $gp_contact->address ? $gp_contact->address->email : "" ?></td>
                    <td><?= $gp_contact->primary_phone ?></td>
                    <td><?= $gp_contact->address ? $gp_contact->address->getLetterLine() : "" ?></td>
                    <td></td>
                </tr>
                <?php
                foreach ($this->contact_assignments as $contact_assignment) { ?>
                    <?php $contact = $contact_assignment->contact; ?>
                    <tr>
                        <td><?= $contact->label ? $contact->label->name : "" ?></td>
                        <td><?= $contact->getFullName() ?></td>
                        <td><?= $contact->address ? $contact->address->email : "" ?></td>
                        <td><?= $contact->primary_phone ?></td>
                        <td><?= $contact->address ? $contact->address->getLetterLine() : "" ?></td>
                        <td style="overflow-wrap:break-word;"><?= $contact_assignment->comment ? Yii::app()->format->Ntext($contact_assignment->comment): "" ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
