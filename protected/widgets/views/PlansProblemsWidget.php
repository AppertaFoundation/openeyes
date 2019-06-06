<?php
/**
 * Created by PhpStorm.
 * User: Stefan
 * Date: 04/06/2019
 * Time: 16:38
 */

$plans_problems = PlansProblems::model()->display_order()->findAll(["condition"=>"active=1"]);
?>

<div class="problems-plans">
    <ul class="problems-plans-sortable" id="problems-plans-sortable">
        <?php foreach ($plans_problems as $plan_problem) {
            $user_created = User::model()->findByPk($plan_problem->last_modified_user_id); ?>
            <li>
                <span class="drag-handle"><i class="oe-i menu medium pro-theme"></i></span>
                <?= $plan_problem->name ?>
                <div class="metadata">
                    <i class="oe-i info small pro-theme js-has-tooltip"
                       data-tooltip-content="<?= $user_created->title ?> <?= $user_created->last_name ?> <?= $user_created->first_name ?>"></i>
                </div>
                <div class="remove"><i class="oe-i remove-circle small pro-theme pad" data-plan-id="<?= $plan_problem->id ?>"></i></div>
            </li>
        <?php } ?>
    </ul>
    <div class="create-new-problem-plan flex-layout">
        <input class="create-problem-plan" type="text" placeholder="Add Problem or Plan">
        <button class="button hint green js-add-pp-btn"><i class="oe-i plus pro-theme"></i></button>
    </div>

    <button class="button hint green js-save-btn">Save</button>
</div>