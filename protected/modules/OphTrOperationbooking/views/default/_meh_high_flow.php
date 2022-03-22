<?php
    $purifier = new CHtmlPurifier();
?>

<div class="oe-popup-wrap">
    <div class="oe-popup">
        <div class="close-icon-btn"><i class="oe-i remove-circle pro-theme" id="high_flow_modal_close"></i></div>
        <div class="oe-popup-content wide">
            <div class="alert-box issue">Guidance: Low complexity patients will be considered for high flow (fast track)
                lists. If the patient meets any of the exclusion factors from this list below, then they should not be
                considered Low complexity. Please review the criteria carefully before confirming the patient's
                suitability.
            </div>
            <hr class="divider">
            <div>
                <h3>Exclusion criteria</h3>
                <?php foreach ($procedures as $k => $procedure) : ?>
                    <div class="low-complexity-criteria">
                        <span class="title">High flow exclusion criteria for <?php echo CHtml::encode($procedure->term); ?></span>
                        <?php echo $purifier->purify($procedure->low_complexity_criteria); ?>
                    </div>
                <?php endforeach; ?>
            </div>

        </div><!-- .oe-popup-content -->

        <div class="popup-actions">
            <button id="high_flow_modal_yes" class="green hint cols-5 yes">Yes, confirm patient is suitable</button>
            <button id="high_flow_modal_no" class="blue hint cols-5 no">No, patient is not suitable</button>
        </div>
    </div>
</div>
