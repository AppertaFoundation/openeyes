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
    <?= isset($is_popup) ? "<h3>Problems &amp; Plans</h3>" : " <div class=\"subtitle\"> Problems &amp; Plans</div>";?>
    <ul class="problems-plans-sortable" id="problems-plans-sortable">
        <?php foreach ($current_plans_problems as $plan_problem) { ?>
            <li>
                <span class="drag-handle"><i class="oe-i menu medium pro-theme"></i></span>
                <?= $plan_problem->name ?>
                <div class="metadata">
                    <i class="oe-i info small pro-theme js-has-tooltip"
                       data-tooltip-content="Created: <?= \Helper::convertDate2NHS($plan_problem->created_date) ?> <?= ($plan_problem->createdUser ? 'by '.$plan_problem->createdUser->getFullNameAndTitle() : '') ?>"></i>
                </div>
                <div class="remove"><i class="oe-i remove-circle small pro-theme pad" data-plan-id="<?= $plan_problem->id ?>"></i></div>
            </li>
        <?php } ?>
    </ul>

    <?php if ($allow_save) : ?>
        <div class="create-new-problem-plan flex-layout">
            <input class="create-problem-plan" type="text" placeholder="Add Problem or Plan">
            <button class="button hint green js-add-pp-btn"><i class="oe-i plus pro-theme"></i></button>
        </div>
    <?php endif; ?>
    <?php if (count($past_plans_problems) > 0) {
        if (isset($is_popup) ) { ?>
            <h3>Past/closed problems</h3>
            <table class="past-problems-plans">
                <colgroup>
                    <col class="cols-4">
                    <col class="cols-1">
                    <col class="cols-2">
                </colgroup>
                <tbody>
                <?php foreach ($past_plans_problems as $plan_problem) { ?>
                    <tr>
                        <td style="padding: 6px 3px;"><?= $plan_problem->name ?></td>
                        <td>
                            <div class="metadata">
                                <i class="oe-i info small <?= $pro_theme ?> js-has-tooltip"
                                   data-tooltip-content="Created: <?= \Helper::convertDate2NHS($plan_problem->created_date) ?>
                                   <?= ($plan_problem->createdUser ? 'by '.$plan_problem->createdUser->getFullNameAndTitle() : '') ?>
                                   <br /> Closed: <?= \Helper::convertDate2NHS($plan_problem->last_modified_date) ?>
                                   <?= ($plan_problem->lastModifiedUser ? 'by '.$plan_problem->lastModifiedUser->getFullNameAndTitle() : '') ?>">
                                </i>
                            </div>
                        </td>
                        <td class="oe-date">Removed: <?= \Helper::convertDate2NHS($plan_problem->last_modified_date) ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
        } else { ?>
            <div class="collapse-data">
                <div class="collapse-data-header-icon expand">
                    Past/closed problems <small>(<?= sizeof($past_plans_problems) ?>)</small>
                </div>
                <div class="collapse-data-content" style="display: none">
                    <table class="past-problems-plans">
                        <colgroup>
                            <col class="cols-4">
                            <col class="cols-1">
                            <col class="cols-2">
                        </colgroup>
                        <tbody>
                        <?php foreach ($past_plans_problems as $plan_problem) { ?>
                            <tr>
                                <td style="padding: 6px 3px;"><?= $plan_problem->name ?></td>
                                <td>
                                    <div class="metadata">
                                        <i class="oe-i info small <?= $pro_theme ?> js-has-tooltip"
                                           data-tooltip-content="Created: <?= \Helper::convertDate2NHS($plan_problem->created_date) ?>
                                           <?= ($plan_problem->createdUser ? 'by '.$plan_problem->createdUser->getFullNameAndTitle() : '') ?>
                                           <br /> Closed: <?= \Helper::convertDate2NHS($plan_problem->last_modified_date) ?>
                                           <?= ($plan_problem->lastModifiedUser ? 'by '.$plan_problem->lastModifiedUser->getFullNameAndTitle() : '') ?>">
                                        </i>
                                    </div>
                                </td>
                                <td class="oe-date">Removed: <?= \Helper::convertDate2NHS($plan_problem->last_modified_date) ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>

                </div>
            </div>
            <?php
        }?>
    <?php } ?>
</div>

<?php if ($allow_save) : ?>
    <?php Yii::app()->clientScript->registerScript('plansProblems', 'const PlansProblems = new OpenEyes.UI.PlansProblemsController();');?>
<script type="text/html" id="plans-problems-template">
    <li>
        <span class="drag-handle"><i class="oe-i menu medium pro-theme"></i></span>
        {{name}}
        <div class="metadata">
            <i class="oe-i info small pro-theme js-has-tooltip" data-tooltip-content="Created: {{create_at}} {{title}}"></i>
        </div>
        <div class="remove"><i class="oe-i remove-circle small pro-theme pad" data-plan-id="{{id}}"></i></div>
    </li>
</script>
<script type="text/html" id="past-plans-problems-template">
    <tr>
        <td style="padding: 6px 3px;">{{name}}</td>
        <td><div class="metadata">
            <i class="oe-i info small pro-theme js-has-tooltip" data-tooltip-content="Created: {{create_at}} {{title}}<br />Closed: {{last_modified}} {{last_modified_by}}"></i>
        </div>
        </td>
        <td><span class="oe-date">Removed: {{last_modified}}</span></td>
    </tr>
</script>
<?php endif; ?>
