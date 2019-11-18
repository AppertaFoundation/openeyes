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
            <?php foreach ($document->event->previewImages as $id => $page) : ?>
            <div class="page-num-btn" data-page="<?= $id ?>"><?= $id + 1 ?></div>
            <?php endforeach; ?>
        </div>
        <?php if (count($document->event->previewImages) > 1) : ?>
        <div class="page-scroll">
            <div class="page-scroll-btn up" id="js-scroll-btn-up"></div>
            <div class="page-scroll-btn down" id="js-scroll-btn-down"></div>
        </div>
        <?php endif; ?>
    </nav>
    <div class="multipage-stack whiteboard">
        <?php
        foreach ($document->event->previewImages as $id => $page) : ?>
            <img id="biom-<?= $id + 1?>" src="<?= $page->getImageUrl() ?>" alt="biom-<?= $id + 1?>"/>
        <?php endforeach; ?>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            let id = 0;
            let maxPages = <?= count($document->event->previewImages) ?>;

            function scrollToPage(page) {
                let pageStack = $('.multipage-stack');
                pageStack.animate({
                    scrollTop: pageStack.scrollTop() + $('#biom-' + (page + 1)).position().top
                }, 200, 'swing');
            }

            function scroll(change) {
                let pageStack = $('.multipage-stack');
                let newPos = pageStack.scrollTop() + change;
                pageStack.animate({
                    scrollTop: newPos+'px'
                }, 200, 'swing')
            }

            $('#js-scroll-btn-down').click(function() {
                scroll(200);
            });

            $('#js-scroll-btn-up').click(function() {
                scroll(-200);
            });

            $('.page-num-btn').click(function() {
                id = $(this).data('page');
                scrollToPage(id);
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
