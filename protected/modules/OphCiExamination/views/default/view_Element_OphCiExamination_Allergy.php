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

<div class="field-row row">
    <div class="large-12 column">
        <table>
            <?php if ($this->patient->allergyAssignments) {
            ?>
            <thead>
            <tr>
                <th>Name</th>
                <th>Comments</th>
            </tr>
            </thead>
            <tbody id="OphCiExamination_allergy">
            <?php
            foreach ($this->patient->allergyAssignments as $aa) {
                    ?>
                    <script type="text/javascript">
                        removeAllergyFromSelect(<?= $aa->allergy->id?>, '<?= $aa->allergy->name ?>');
                    </script>
                    <tr data-assignment-id="<?= $aa->id ?>" data-allergy-id="<?= $aa->allergy->id ?>"
                        data-allergy-name="<?= $aa->allergy->name ?>">
                        <td><?= CHtml::encode($aa->name) ?>
                        </td>
                        <td><?= CHtml::encode($aa->comments) ?></td>
                    </tr>
                    <?php
                }
            } else { ?>
                <tr>
                    <td colspan="2">Patient has no known allergies</td>
                </tr>
            <?php }
            ?>
            </tbody>
        </table>
    </div>
</div>
