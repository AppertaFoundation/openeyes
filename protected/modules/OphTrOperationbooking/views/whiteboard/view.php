
<div class="mdl-grid">
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand patient">
                <h2 class="mdl-card__title-text">Patient Details</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <dl>
                    <dt>Name</dt>
                    <dd><?=$data->patient_name?></dd>
                </dl>
                <dl>
                    <dt>Date of Birth</dt>
                    <dd><?=date_create_from_format('Y-m-d', $data->date_of_birth)->format('j M Y')?></dd>
                </dl>
                <dl>
                    <dt>Hospital Number</dt>
                    <dd><?=$data->hos_num?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand patient">
                <h2 class="mdl-card__title-text">Operation Side</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?= $data->eye->name ?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand patient">
                <h2 class="mdl-card__title-text">Operation Type</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?=$data->procedure?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand biometry">
                <h2 class="mdl-card__title-text">IOL Model</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?=$data->iol_model?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand biometry">
                <h2 class="mdl-card__title-text">IOL Power</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?=$data->iol_power?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand biometry">
                <h2 class="mdl-card__title-text">Predicted refractive outcome</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?=$data->predicted_refractive_outcome?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand risk">
                <h2 class="mdl-card__title-text">Allergies</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?=nl2br($data->allergies)?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand risk">
                <h2 class="mdl-card__title-text">Alpha-blockers</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?php if($data->alpha_blockers):?>
                    Yes - <?=$data->alpha_blocker_name?>
                <?php else: ?>
                    No
                <?php endif;?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand comment">
                <h2 class="mdl-card__title-text">Predicted additional equipment</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?php if($data->predicted_additional_equipment): ?>
                    <?=nl2br($data->predicted_additional_equipment)?>
                <?php else:?>
                    None
                <?php endif;?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand risk">
                <h2 class="mdl-card__title-text">Anticoagulants</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?php if($data->anticoagulants):?>
                    Yes - <?=$data->anticoagulant_name?> <br>
                    INR: <?=$data->inr?>
                <?php else: ?>
                    No
                <?php endif;?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--8-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand comment">
                <h2 class="mdl-card__title-text">Comments</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?=nl2br($data->comments)?>
            </div>
        </div>
    </div>
</div>
<div id="dialog-container"></div>
