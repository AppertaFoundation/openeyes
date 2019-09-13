<?php
foreach ($this->data as $elem) {
    if (is_array($elem)) : ?>
        <?= $elem['content']?>
        <?php if (isset($elem['small_data'])) : ?>
            <small><?= $elem['small_data'] ?></small>
        <?php endif ?>
        <?php if (isset($elem['extra_data'])) : ?>
            <div class="extra-small-data">
                <?= $elem['extra_data'] ?>
            </div>
        <?php endif; ?>
    <?php else : ?>
        <?= $elem ?>
    <?php endif;
}

