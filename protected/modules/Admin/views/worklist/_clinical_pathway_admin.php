<?php
/**
 * @var $pathway_type PathwayType
 */
?>

<div class="pathway">
    <?php foreach ($pathway_type->default_steps as $step) { ?>
        <span class="oe-pathstep-btn <?= "todo {$step->step_type->type}" ?>" data-pathstep-type-id="<?= $step->id ?>"
              data-pathway-id="<?= $pathway_type->id ?>">
            <span class="step<?= $step->step_type->large_icon ? " {$step->step_type->large_icon}" : '' ?>">
                <?= !$step->step_type->large_icon ? $step->short_name : '' ?>
            </span>
        </span>
    <?php } ?>
</div>
