
<div class="mdl-grid">
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand patient">
                <h2 class="mdl-card__title-text">Patient Details</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?=$data->patient_name?> <br />
                <?=date_create_from_format('Y-m-d', $data->date_of_birth)->format('j M Y')?> <br />
                <?=$data->hos_num?>
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
                <h2 class="mdl-card__title-text">IOL Model &amp; Formula</h2>
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
                <?=$data->alpha_blocker_name?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand risk">
                <h2 class="mdl-card__title-text">Anticoagulants</h2>
            </div>
            <div class="mdl-card__supporting-text">
                <?=$data->anticoagulant_name?> <br>
                INR: <?=$data->inr?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand risk">
                <h2 class="mdl-card__title-text">Alerts/Risks</h2>
            </div>
            <div class="mdl-card__supporting-text" id="comments">
                <?php echo $this->getWhiteboard()->getPatientRisksDisplay(); ?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col mdl-cell--4-col-tablet editable">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand comment">
                <h2 class="mdl-card__title-text">Predicted additional equipment</h2>
                <?php if ($this->getWhiteboard()->isEditable()) :?>
                    <div class="mdl-layout-spacer"></div>
                    <i class="material-icons right" data-whiteboard-event-id="<?=$data->event_id?>">create</i>
                <?php endif; ?>
            </div>
            <div class="mdl-card__supporting-text" id="predicted_additional_equipment">
                <?php if ($data->predicted_additional_equipment) : ?>
                    <?=nl2br($data->predicted_additional_equipment)?>
                <?php else :?>
                    None
                <?php endif;?>
            </div>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--4-col editable">
        <div class="mdl-card mdl-shadow--2dp">
            <div class="mdl-card__title mdl-card--expand comment">
                <h2 class="mdl-card__title-text">Comments</h2>
                <?php if ($this->getWhiteboard()->isEditable()) :?>
                    <div class="mdl-layout-spacer"></div>
                    <i class="material-icons right" data-whiteboard-event-id="<?=$data->event_id?>">create</i>
                <?php endif; ?>
            </div>
            <div class="mdl-card__supporting-text" id="comments">
                <?=nl2br($data->comments)?>
            </div>
        </div>
    </div>
</div>
