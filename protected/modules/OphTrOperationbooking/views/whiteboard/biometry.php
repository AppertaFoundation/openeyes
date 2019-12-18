<?php
    /**
     * @var $booking_id int
     * @var $pages EventImage[]
     * @var $document_event_id int
     */
?>
<header class="oe-header">
    <?php $this->renderPartial($this->getHeaderTemplate(), array(
        'data' => $data
    ));?>
</header>
<main class="oe-whiteboard">
    <?php $this->widget('MultipageView', array(
        'stack_class' => 'whiteboard',
        'nav_title' => 'Biometry',
        'images' => $document->event->previewImages,
    ));?>
    <footer class="wb3-actions down">
        <?php $this->renderPartial('footer', array(
            'biometry' => true,
            'booking_id' => $booking_id,
        )); ?>
    </footer>
</main>
