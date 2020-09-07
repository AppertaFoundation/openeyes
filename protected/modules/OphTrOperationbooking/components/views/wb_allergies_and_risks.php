<?php
/**
 * @var $this RiskCard
 */
$total_risks = 0;
$risks = $this->data->getPatientRisksDisplay($total_risks);
$lab_results = $this->data->getPatientLabResultsDisplay();
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
                    Status unknown
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
    <?php if ($this->data->anticoagulant_name !== 'No Anticoagulants') { ?>
        <div class="alert-box <?= $this->data->anticoagulant_name === 'Not checked' ? 'info' : 'warning' ?>">
            <?= $this->data->anticoagulant_name === 'Not checked' ? 'Unchecked: Anticoagulants' : "Anticoagulants <br> {$this->data->anticoagulant_name}"?>
        </div>
    <?php }
    echo $risks;
    echo $lab_results;
    if ($this->data->anticoagulant_name === 'No Anticoagulants'
        && in_array($this->getAnticoagulantRisk()->id, $all_risk_ids, true)) { ?>
        <div class="alert-box success">
            Absent: Anticoagulants
        </div>
    <?php } ?>
</div>
