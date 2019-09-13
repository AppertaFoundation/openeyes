<?php
    /**
     * @var $css_class string
     * @var $data_view string
     */
?>
<div class="oe-wb-widget <?= $css_class . ($this->colour ? ' ' . $this->colour : '')?>">
    <h3>
        <?= $this->title ?>
        <?php if ($this->editable) : ?>
            <div class="edit-widget-btn" data-whiteboard-event-id="<?=$this->event_id?>">
                <i class="oe-i pencil medium pro-theme"></i>
            </div>
        <?php endif; ?>
    </h3>
    <div class="wb-data">
        <?php $this->render($data_view); ?>
    </div>
</div>