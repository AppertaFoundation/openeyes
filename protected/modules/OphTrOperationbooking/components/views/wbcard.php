<?php
    /**
     * @var $css_class string
     * @var $data_view string
     */
if (!$this->data) {
    $widget_css = 'oe-wb-empty-widget';
} else {
    $widget_css = 'oe-wb-widget ' . $css_class . ($this->colour ? ' ' . $this->colour : '');
}
?>
<div class="<?= $widget_css ?>">
    <?php if ($this->data) : ?>
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
    <?php else : ?>
        <!-- empty widget placeholder -->
    <?php endif; ?>
</div>