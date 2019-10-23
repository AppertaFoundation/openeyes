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

<div class="row divider">
    <h2><?=$medication_set_name?> Medications:</h2>
</div>

<div class="cols-12">
    <table id="medicationset-medications-list" class="standard">
        <colgroup>
            <col class="cols-1">
            <col class="cols-5">
        </colgroup>
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data_provider->getData() as $medication) { ?>
                <tr>
                    <td><?=$medication->id?></td>
                    <td><?=$medication->preferred_term?></td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot class="pagination-container">
        <tr>
            <td colspan="2">
                <?= \OEHtml::cancelButton("Back", [
                    'data-uri' => '/OphDrPrescription/admin/AutoSetRule/index',
                ]) ?>
            </td>
            <td colspan="6">
                <?php $this->widget('LinkPager', ['pages' => $data_provider->pagination]); ?>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
