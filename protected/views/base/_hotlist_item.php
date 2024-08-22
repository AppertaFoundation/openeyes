<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
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

/**
 * @var UserHotlistItem $hotlistItem
 * @var CoreAPI $core_api
 * @var int $institution_id
 * @var int $site_id
 * @var string $display_primary_number_usage_code
 */

?>
<?php
$primary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_primary_number_usage_code, $hotlistItem->patient->id, $institution_id, $site_id);
?>

<tr class="js-hotlist-<?= $hotlistItem->is_open ? 'open' : 'closed' ?>-patient"
    data-id="<?= $hotlistItem->id ?>"
    data-test="hotlist-patient"
    data-patient-href="<?= $core_api->generatePatientLandingPageLink($hotlistItem->patient) ?>"
>
    <td><?= CHtml::encode(PatientIdentifierHelper::getIdentifierValue($primary_identifier)) ?></td>
    <td>
        <?= CHtml::encode($hotlistItem->patient->getHSCICName()) ?>
    </td>
    <td>
        <div class="js-hotlist-comment-readonly">
            <?= CHtml::encode(substr($hotlistItem->user_comment ?? '', 0, 30) .
                (strlen($hotlistItem->user_comment ?? '') > 30 ? '...' : '')) ?>
        </div>

    </td>
    <td>
        <?php $i_class = $hotlistItem->user_comment ? 'comments-added active' : 'comments';?>
        <i class="oe-i <?=$i_class;?> pad medium pro-theme js-patient-comments js-add-hotlist-comment"></i>

        <?php if ($hotlistItem->is_open) : ?>
            <i class="oe-i remove-circle medium pro-theme pad js-close-hotlist-item"></i>
        <?php else : ?>
            <i class="oe-i plus-circle medium pro-theme pad js-open-hotlist-item"></i>
        <?php endif; ?>
    </td>
</tr>
<tr class="hotlist-comment js-hotlist-comment"
    data-id="<?= $hotlistItem->id ?>"
    style="display: none;">
    <td colspan="4">
        <?= CHtml::activeTextArea($hotlistItem, 'user_comment', [
                'placeholder' => 'Comments', 'class' => 'cols-full autosize']); ?>
    </td>
</tr>
