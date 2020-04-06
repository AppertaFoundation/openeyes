<?php
/**
 * @var $this RiskCard
 */
$total_risks = 0;
$risks = $this->data->getPatientRisksDisplay($total_risks);
if ($this->data->alpha_blocker_name !== 'No Alpha Blockers' && $this->data->alpha_blocker_name !== 'Not checked') {
    $total_risks++;
}

if ($this->data->anticoagulant_name !== 'No Anticoagulants' && $this->data->anticoagulant_name !== 'Not checked') {
    $total_risks++;
}
$all_risk_ids = $this->data->booking->getAllBookingRiskIds();
?>
<div class="oe-wb-special risks">
    <h3>Allergies (<?= in_array($this->data->allergies, ['None', 'Unknown']) ? 0 : count(explode(', ', $this->data->allergies))?>)</h3>
    <?php if ($this->data->allergies === 'None') { ?>
        <div class="alert-box success">No Allergies</div>
    <?php } else { ?>
        <?php foreach (explode(', ', $this->data->allergies) as $allergy) { ?>
            <?php if ($allergy === 'Unknown') {?>
                <div class="alert-box info">
                    Allergy status unknown
                </div>
            <?php } else { ?>
                <div class="alert-box warning">
                    <?= $allergy ?>
                </div>
            <?php }
        }
    } ?>
    <hr class="divider"/>
    <h3>Risks (<?= $total_risks ?>)</h3>
    <?php if ($this->data->alpha_blocker_name !== 'No Alpha Blockers' && $this->data->alpha_blocker_name !== 'Not checked') { ?>
        <div class="alert-box warning">
            Alphablocker - <?= $this->data->alpha_blocker_name ?>
        </div>
    <?php }
    if ($this->data->anticoagulant_name !== 'No Anticoagulants' && $this->data->anticoagulant_name !== 'Not checked') { ?>
        <div class="alert-box warning">
            Anticoagulants - <?= $this->data->anticoagulant_name ?>
        </div>
    <?php }
    echo $risks;
    if ($this->data->anticoagulant_name === 'No Anticoagulants'
        && in_array($this->getAnticoagulantRisk()->id, $all_risk_ids, true)) { ?>
        <div class="alert-box success">
            No Anticoagulants
        </div>
    <?php }
    if ($this->data->alpha_blocker_name === 'No Alpha Blockers'
        && in_array($this->getAlphaBlockerRisk()->id, $all_risk_ids, true)) { ?>
        <div class="alert-box success">
            No Alpha Blockers
        </div>
    <?php } ?>
</div>
