<?php
/**
 * @var $total_pages int
 */
?>
<nav class="multipage-nav">
    <?php if ($this->inline_nav) : ?>
        <?php for ($i = 0; $i < $this->total_pages; $i++) :
            $pageNum = $i + 1 ?>
            <div class="page-num-btn" data-page="<?= $pageNum ?>"><?= "{$pageNum}/{$total_pages}"?></div>
        <?php endfor; ?>
    <?php else : ?>
        <h3><?= $this->nav_title ?></h3>
        <div class="page-jump">
            <?php for ($i = 0; $i < $total_pages; $i++) :
                $pageNum = $i + 1
                ?>
                <div class="page-num-btn" data-page="<?= $pageNum ?>"><?= $pageNum ?></div>
            <?php endfor; ?>
            <div class="page-scroll">
                <div class="page-scroll-btn up" id="js-scroll-btn-up"></div>
                <div class="page-scroll-btn down" id="js-scroll-btn-down"></div>
            </div>
        </div>
    <?php endif; ?>
</nav>
