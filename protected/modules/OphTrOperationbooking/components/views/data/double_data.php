<?php
foreach ($this->data as $elem) {
    echo $elem['content'];
    if (isset($elem['small_data'])) : ?>
        <small><?= $elem['small_data'] ?></small>
    <?php endif ?>
    <?php if (isset($elem['extra_data'])) : ?>
        <div class="extra-small-data">
            <?= $elem['extra_data'] ?>
        </div>
    <?php endif;
}

