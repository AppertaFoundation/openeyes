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
<div class="problems-plans">
    <ul class="problems-plans-sortable" id="problems-plans-sortable">
        <?php foreach ($plans_problems as $plan_problem) {
            if ($plan_problem->active) { ?>
            <li>
                <span class="drag-handle" style="position: absolute;"><i class="oe-i menu medium pro-theme"></i></span>
                <span style="display:inline-block;margin-left:30px;"><?= $plan_problem->name ?></span>
                <div class="metadata">
                    <i class="oe-i info small pro-theme js-has-tooltip"
                       data-tooltip-content="<?= $plan_problem->createdUser->title ?> <?= $plan_problem->createdUser->first_name ?> <?= $plan_problem->createdUser->last_name ?> <br /> Created: <?= \Helper::convertDate2NHS($plan_problem->created_date) ?>"></i>
                </div>
                <div class="remove"><i class="oe-i remove-circle small pro-theme pad" data-plan-id="<?= $plan_problem->id ?>"></i></div>
            </li>
            <?php }
        } ?>
    </ul>

    <?php if ($allow_save) : ?>
        <div class="create-new-problem-plan flex-layout">
            <input class="create-problem-plan" type="text" placeholder="Add Problem or Plan">
            <button class="button hint green js-add-pp-btn"><i class="oe-i plus pro-theme"></i></button>
        </div>
    <?php endif; ?>
    <br />
    <table class="problems-plans cols-full">
        <colgroup>
            <col class="cols-9">
            <col class="cols-1">
            <col class="cols-2">
        </colgroup>
        <thead>
        <tr>
            <th colspan="2">Past/closed problems</th>
            <th></th>
            <th style="text-align:right;">
                <i class="oe-i small pad js-patient-expand-btn expand <?= $pro_theme ?>"></i>
            </th>
        </tr>
        </thead>
        <tbody style="display: none;">
            <?php foreach ($plans_problems as $plan_problem) {
                if (!$plan_problem->active) {  ?>
                <tr>
                    <td style="padding: 6px 3px;"><?= $plan_problem->name ?></td>
                    <td><div class="metadata">
                        <i class="oe-i info small <?= $pro_theme ?> js-has-tooltip"
                       data-tooltip-content="<?= $plan_problem->createdUser->title ?> <?= $plan_problem->createdUser->first_name ?> <?= $plan_problem->createdUser->last_name ?> <br /> Created: <?= \Helper::convertDate2NHS($plan_problem->created_date) ?>"></i>
                    </div>
                    </td>
                    <td><span class="oe-date">Removed: <?= \Helper::convertDate2NHS($plan_problem->last_modified_date) ?></span></td>
                </tr>
                <?php }
            } ?>
        </tbody>
    </table>
</div>

<?php if ($allow_save) : ?>
<script>
    const PlansProblems = new OpenEyes.UI.PlansProblemsController();
</script>
<?php endif; ?>