<?php
    /**
     * @var $biometry bool
     */
?>
<div class="openclose-icon-btn up" id="js-wb3-openclose-actions">
</div>
<?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'enableAjaxValidation' => false,
    ))?>
<?php if (!$biometry && $this->getWhiteboard()->biometry_report) : ?>
    <button class="pro-theme large" formaction="/OphTrOperationbooking/whiteboard/biometryReport/<?= $booking_id?>">
        Biometry
    </button>
<?php endif; ?>
<?php if (!$consent && $this->getWhiteboard()->consent) : ?>
    <button class="pro-theme large" formaction="/OphTrOperationbooking/whiteboard/consentForm/<?= $booking_id?>">
        Consent
    </button>
<?php endif; ?>
<?php if ($this->isRefreshable() && !$biometry && !$consent) :?>
    <button class="pro-theme"
            formaction="/OphTrOperationbooking/whiteboard/reload/<?=$this->getWhiteboard()->event_id?>">
        <span class="icon-text">Refresh</span>
        <i class="oe-i rotate-left pro-theme medium pad-left"></i>
    </button>
<?php endif; ?>
<?php if (!$biometry && !$consent) : ?>
    <button class="pro-theme red" id="exit-button">
        <span class="icon-text">Exit</span>
        <i class="oe-i remove-circle pro-theme medium pad-left"></i>
    </button>
<?php else : ?>
    <button class="pro-theme large" formaction="/OphTrOperationbooking/whiteboard/view/<?=$this->getWhiteboard()->event_id?>">
        Overview
    </button>
<?php endif; ?>
<?php $this->endWidget(); ?>
