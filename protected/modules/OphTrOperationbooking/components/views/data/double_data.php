<?php
foreach ($this->data as $elem) {
    if (isset($elem['small_data'])) : ?>
        <?=$elem['content'] ?><small><?= $elem['small_data'] ?></small>
    <?php else : ?>
        <?=$elem['content'] ?>
    <?php endif ?>
    <?php if (isset($elem['extra_data'])) : ?>
        <div class="extra-small-data"><?= $elem['extra_data'] ?></div>
    <?php endif;
}

