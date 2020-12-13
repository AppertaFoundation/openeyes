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
$dataAttribute = '';
if (isset($this->data['dataAttribute'])) {
    $dataAttribute = 'data-'. $this->data['dataAttribute']['name'] . '="' . htmlspecialchars(json_encode($this->data['dataAttribute']['value'])) . '"';
}
?>
<div class="<?= $widget_css ?>" <?= $dataAttribute ?> >
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
    <?php if (isset($this->data['is_overflow_btn_required']) && $this->data['is_overflow_btn_required'] ) { ?>
        <div class="overflow-icon-btn"></div>
    <?php } ?>
</div>