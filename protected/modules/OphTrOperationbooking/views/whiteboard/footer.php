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
<?php if (!$biometry) : ?>
    <button class="pro-theme" formaction="/OphTrOperationbooking/whiteboard/biometryReport/<?= $booking_id?>">
        <span class="icon-text">Biometry</span>
    </button>
<?php endif; ?>
<?php if ($this->isRefreshable() && !$biometry) :?>
    <button class="pro-theme"
            formaction="/OphTrOperationbooking/whiteboard/reload/<?=$this->getWhiteboard()->event_id?>">
        <span class="icon-text">Refresh</span>
        <i class="oe-i rotate-left pro-theme medium pad-left"></i>
    </button>
<?php endif; ?>
<?php if (!$biometry) : ?>
    <button class="pro-theme red" id="exit-button">
        <span class="icon-text">Exit</span>
        <i class="oe-i remove-circle pro-theme medium pad-left"></i>
    </button>
<?php else : ?>
    <button class="pro-theme" formaction="/OphTrOperationbooking/whiteboard/view/<?=$this->getWhiteboard()->event_id?>">
        <span class="icon-text">Back</span>
        <i class="oe-i arrow-left-bold pro-theme medium pad-left"></i>
    </button>
<?php endif; ?>
<?php $this->endWidget(); ?>
