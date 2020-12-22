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
?>

<?php $model_name = CHtml::modelName($element); ?>

<?php if (!$this->patient->hasAllergyStatus()) : ?>
    <div class="alert-box info">
        <strong>Allergies</strong> - status unknown.
    </div>
<?php elseif ($element->no_allergies_date) : ?>
    <div class="alert-box success">
        <strong>Allergies</strong> - none known.
    </div>
<?php else : ?>
    <div class="alert-box patient">
        <strong>Allergies</strong>
    </div>
    <div class="popup-overflow">
        <table class="risks">
            <colgroup>
                <col class="cols-6">
            </colgroup>
            <tbody>
            <?php foreach ($element->entries as $i => $entry) : ?>
                <?php if ($entry->getDisplayHasAllergy() === 'Present') : ?>
                    <tr>
                        <td>
                            <i class="oe-i warning small pad-right"></i>
                            <?= $entry->getDisplayAllergy() ?>
                        </td>
                        <td><?=$entry->comments ?></td>
                        <td></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
