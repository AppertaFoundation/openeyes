<?php if ($this->highlight_colour) : ?>
    <span class="highlighter <?= $this->highlight_colour ?>">
<?php endif; ?>
    <?php if (is_array($this->data)) : ?>
        <?= $this->data['content']?>
    <?php else : ?>
        <?= $this->data ?>
    <?php endif; ?>

<?php if ($this->highlight_colour) : ?>
    </span>
<?php endif; ?>
<?php if (is_array($this->data)) : ?>
    <?php if (isset($this->data['extra_data'])) : ?>
        <div class="extra-data">
            <?= $this->data['extra_data'] ?>
        </div>
    <?php elseif (isset($this->data['small_data'])) :?>
        <small><?= $this->data['small_data'] ?></small>
    <?php endif; ?>
<?php endif; ?>
