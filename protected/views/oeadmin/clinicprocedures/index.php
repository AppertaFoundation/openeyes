<?php
/**
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php if (!$procedures) : ?>
    <div>
        <div class="row divider">
            <div class="alert-box issue"><b>No results found</b></div>
        </div>
    </div>
<?php endif; ?>

<div class="row divider cols-full">
    <?php if (Yii::app()->user->hasFlash('success')) {?>
        <div id="flash-success" class="alert-box success">
            <?= Yii::app()->user->getFlash('success'); ?>
        </div>
    <?php } ?>
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>" />
    <table class="standard">
        <colgroup>
            <col class="cols-4">
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-3">
        </colgroup>
        <thead>
        <tr>
            <th>Procedures</th>
            <th>Institution</th>
            <th>Context</th>
            <th>Subspecialty</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($procedures as $key => $procedure) {
            $clinic_procedure = $procedure->clinic_procedure;
            ?>
        <tr id="<?= $key ?>" class="clickable" data-id="<?= $procedure->id ?>"
            data-uri="oeadmin/ClinicProcedure/edit/<?= $procedure->id ?>">
            <td><?= $procedure->term ?></td>
            <td><?= ($clinic_procedure && $clinic_procedure->institution) ? $clinic_procedure->institution->name : 'All' ?></td>
            <td><?= ($clinic_procedure && $clinic_procedure->firm) ? $clinic_procedure->firm->name : 'All' ?></td>
            <td><?= ($clinic_procedure && $clinic_procedure->subspecialty) ? $clinic_procedure->subspecialty->name : 'All' ?></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
