<?php
?>
<div class="multipage-stack<?= $this->stack_class ? ' ' . $this->stack_class : null ?>">
    <?php foreach ($this->images as $id => $image) :
        $pageNum = $id + 1;?>
        <?php if ($this->full_width) : ?>
            <img
                id="multipage-img-mp_8-p<?= $pageNum ?>"
                src="<?= $image->getImageUrl() ?>"
                width="100%"
                height="auto"
                alt="p<?= $pageNum ?>"/>
        <?php else : ?>
        <img
            id="multipage-img-mp_8-p<?= $pageNum ?>"
            src="<?= $image->getImageUrl() ?>"
            alt="p<?= $pageNum ?>"/>
        <?php endif; ?>
    <?php endforeach; ?>
</div>