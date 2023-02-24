<?php
/**
 * @var $path_steps PathwayStepType[]
 * @var $pathways PathwayType[]
 * @var $standard_steps PathwayStepType[]
 * @var $custom_steps PathwayStepType[]
 */
?>
<div class="oec-adder">
    <div class="close-btn"></div>
    <?php if ($show_pathway_selected) { ?>
    <div class="add-to">
        <span class="num"></span> selected
    </div>
    <?php } ?>
    <div class="insert-steps">
        <div class="row">
            <input class="assign-to search" type="text" placeholder="Assign to..."/>
            <div class="spinner-loader" style="display: none;">
                <i class="spinner"></i>
            </div>
            <ul id="js-assignee-list" class="btn-list"></ul>
        </div>
        <div class="row">
            <h4>Path</h4>
            <ul class="btn-list">
                <?php foreach ($path_steps as $step) { ?>
                    <li class="js-step" data-id="<?= $step->id ?>">
                        <?= $step->small_icon ? '<i class="oe-i small pad-right ' . $step->small_icon . '"></i>' : null ?>
                        <?= $step->long_name ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <?php if (isset($pathways)) { ?>
        <div class="row">
            <h4>Preset Pathways</h4>
            <ul class="btn-list">
                <li id="add-preset-pathway">Add common pathway</li>
            </ul>
        </div>
        <?php } ?>
        <div class="row">
            <h4>Standard</h4>
            <ul class="btn-list">
                <?php foreach ($standard_steps as $step) { ?>
                    <li class="js-step" data-id="<?= $step->id ?>" data-test="<?= str_replace(' ', '-', strtolower($step->long_name)); ?>">
                        <?= $step->small_icon ? '<i class="oe-i small pad-right ' . $step->small_icon . '"></i>' : null ?>
                        <?= $step->long_name ?>
                    </li>
                <?php } ?>
                <?php foreach ($custom_steps as $step) { ?>
                    <li class="js-step" data-id="<?= $step->id ?>">
                        <?= $step->small_icon ? '<i class="oe-i small pad-right ' . $step->small_icon . '"></i>' : null ?>
                        <?= $step->long_name ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <?php if ($show_undo_step) { ?>
        <div class="row">
            <h4>Remove step from patient</h4>
            <ul class="btn-list">
                <li id="undo-add-step" class="red">Remove last "todo" pathway step</li>
            </ul>
        </div>
        <?php } ?>
    </div>
</div>
