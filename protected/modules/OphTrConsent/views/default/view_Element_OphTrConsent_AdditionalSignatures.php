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
<?php /** @var \OEModule\OphTrConsent\models\Element_OphTrConsent_AdditionalSignatures $element */ ?>

<div class="element-fields">
    <div class="element-data full-width">
        <div class="cols-10">
            <table class="cols-full last-left">
                <colgroup><col class="cols-6"></colgroup>
                <tbody>
                <?php if ($element->cf_type_id === "2") { ?>
                    <tr>
                        <td><?= $element->getAttributeLabel('child_agreement') ?></td>
                        <td><span class="highlighter"><?= ($element->child_agreement === "1") ? 'Yes' : 'N/A' ?></td></span>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td><?= $element->getAttributeLabel('witness_required') ?></td>
                        <td><?= ($element->witness_required === "1") ? \CHtml::encode($element->witness_name) : '<span class="highlighter">No</span>' ?></td>
                    </tr>
                <?php } ?>

                <tr>
                    <td><?= $element->getAttributeLabel('interpreter_required') ?></td>
                    <td><?= ($element->interpreter_required === "1") ? \CHtml::encode($element->interpreter_name) : '<span class="highlighter">No</span>' ?></td>
                </tr>

                <?php if ($element->cf_type_id === "2") { ?>
                    <tr>
                        <td><?= $element->getAttributeLabel('guardian_required') ?></td>
                        <td><?= ($element->guardian_required === "1") ? \CHtml::encode($element->guardian_name) : '<span class="highlighter">No</span>' ?></td>
                    </tr>
                    <?php if ($element->guardian_required === "1") { ?>
                        <tr>
                            <td>Relationship</td>
                            <td><?= \CHtml::encode($element->guardian_relationship) ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
