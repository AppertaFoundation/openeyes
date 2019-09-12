<?php
?>
<div class="openclose-icon-btn up" id="js-wb3-openclose-actions">
</div>
<?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'enableAjaxValidation' => false,
    ))?>
<button class="pro-theme">
    <span class="icon-text">Biometry</span>
</button>
<?php if ($this->isRefreshable()) :?>
    <button class="pro-theme"
            formaction="/OphTrOperationbooking/whiteboard/reload/<?=$this->getWhiteboard()->event_id?>">
        <span class="icon-text">Refresh</span>
        <i class="oe-i rotate-left pro-theme medium pad-left"></i>
    </button>
<?php endif; ?>
<button class="pro-theme red" id="exit-button">
    <span class="icon-text">Exit</span>
    <i class="oe-i remove-circle pro-theme medium pad-left"></i>
</button>
<?php $this->endWidget(); ?>
