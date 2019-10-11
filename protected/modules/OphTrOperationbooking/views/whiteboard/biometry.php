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
            <img id="biom-<?= $id + 1?>" src="/eventImage/view/<?= $document_event_id ?>" alt="biom-<?= $id + 1?>"/>
        <?php endforeach; ?>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            let id = 0;
            let pageStack = $('.multipage-stack');
            let maxPages = <?= count($pages) ?>;
            $('#js-scroll-btn-down').click(function() {
                if (id + 1 < maxPages) {
                    id++;
                    pageStack.animate({
                        scrollTop: pageStack.scrollTop() + $('#biom-' + (id + 1)).position().top
                    });
                }
            });

            $('#js-scroll-btn-up').click(function() {
                if (id > 0) {
                    id--;
                    pageStack.animate({
                        scrollTop: pageStack.scrollTop() + $('#biom-' + (id + 1)).position().top
                    });
                }
            });

            $('.page-num-btn').click(function() {
                id = $(this).data('page');
                pageStack.animate({
                    scrollTop: pageStack.scrollTop() + $('#biom-' + (id + 1)).position().top
                });
            })
        });
    </script>
    <footer class="wb3-actions down">
        <?php $this->renderPartial('footer', array(
            'biometry' => true,
            'booking_id' => $booking_id,
        )); ?>
    </footer>
</main>
