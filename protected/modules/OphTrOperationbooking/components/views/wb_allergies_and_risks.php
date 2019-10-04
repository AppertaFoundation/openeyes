<?php
/**
 * @var $this->data
 */
$total_risks = 0;
$risks = $this->whiteboard->getPatientRisksDisplay($total_risks);
if ($this->data->alpha_blocker_name !== 'No Alpha Blockers') {
    $total_risks++;
}

if ($this->data->anticoagulant_name !== 'No Anticoagulants') {
    $total_risks++;
}
?>
<div class="oe-wb-special risks">
    <h3>Allergies (<?= count(explode(', ', $this->data->allergies))?>)</h3>
    <?php if ($this->data->allergies === 'None') :?>
        <div class="alert-box success">No Allergies</div>
    <?php else :?>
        <?php foreach (explode(', ', $this->data->allergies) as $allergy) :?>
            <div class="alert-box warning">
                <?= $allergy ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <hr class="divider"/>
    <h3>Risks (<?= $total_risks ?>)</h3>
    <?php if ($this->data->alpha_blocker_name !== 'No Alpha Blockers') : ?>
        <div class="alert-box warning">
            Alphablocker - <?=$this->data->alpha_blocker_name?>
        </div>
    <?php endif; ?>
    <?php if ($this->data->anticoagulant_name !== 'No Anticoagulants') : ?>
        <div class="alert-box warning">
            Anticoagulants - <?=$this->data->anticoagulant_name?>
        </div>
    <?php endif; ?>
    <?php echo $risks ?>
    <?php if ($this->data->anticoagulant_name === 'No Anticoagulants') : ?>
        <div class="alert-box success">
            No Anticoagulants
        </div>
    <?php endif; ?>
    <?php if ($this->data->alpha_blocker_name === 'No Alpha Blockers') : ?>
        <div class="alert-box success">
            No Alpha Blockers
        </div>
    <?php endif; ?>
</div>
