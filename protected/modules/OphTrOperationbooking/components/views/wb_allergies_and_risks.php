<?php
    /**
     * @var $this->data
     */
?>
<div class="oe-wb-special risks">
    <h3>Allergies</h3>
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
    <h3>Risks</h3>
    <?php if ($this->data->alpha_blocker_name !== 'Not checked') : ?>
        <div class="alert-box warning">
            Alphablocker - <?=$this->data->alpha_blocker_name?>
        </div>
    <?php endif; ?>
    <?php if ($this->data->anticoagulant_name !== 'Not checked') : ?>
        <div class="alert-box warning">
            Anticoagulants - <?=$this->data->anticoagulant_name?>
        </div>
    <?php endif; ?>
    <?php if ($this->data->inr !== 'None') :?>
        <div class="alert-box warning">INR: <?=$this->data->inr?></div>
    <?php endif; ?>
    <?php echo $this->whiteboard->getPatientRisksDisplay(); ?>
    <?php if ($this->data->anticoagulant_name === 'No') : ?>
        <div class="alert-box success">
            No Anticoagulants
        </div>
    <?php endif; ?>
    <?php if ($this->data->alpha_blocker_name === 'No') : ?>
        <div class="alert-box success">
            No Alpha Blockers
        </div>
    <?php endif; ?>
</div>
