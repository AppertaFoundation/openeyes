<?php
    /**
     * @var $booking_id int
     * @var $pages EventImage[]
     */
?>
<header class="oe-header">
    <?php $this->renderPartial($this->getHeaderTemplate(), array(
        'data' => $data
    ));?>
</header>
<main class="oe-whiteboard">
    <nav class="multipage-nav">
        <h3>Biometry</h3>
        <div class="page-jump">
            <?php foreach ($pages as $id => $page) : ?>
            <div class="page-num-btn" data-page="<?= $id ?>"><?= $id + 1 ?></div>
            <?php endforeach; ?>
        </div>
        <?php if (count($pages) > 1) : ?>
        <div class="page-scroll">
            <div class="page-scroll-btn up" id="js-scroll-btn-up"></div>
            <div class="page-scroll-btn down" id="js-scroll-btn-down"></div>
        </div>
        <?php endif; ?>
    </nav>
    <div class="multipage-stack whiteboard">
        <?php
        foreach ($pages as $id => $page) : ?>
            <img src="/eventImage/view/<?= $document_event_id?>" alt="biom-<?= $id + 1?>"/>
        <?php endforeach; ?>
    </div>
    <footer class="wb3-actions down">
        <?php $this->renderPartial('footer', array(
            'biometry' => true,
            'booking_id' => $booking_id,
        )); ?>
    </footer>
</main>
