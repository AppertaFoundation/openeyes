<?php
$content = is_array($this->data) ? $this->data['content'] : $this->data;
if ($this->highlight_colour) : ?>
    <span class="highlighter <?= $this->highlight_colour ?>"><?= $content ?></span>
<?php else :
    echo $content;
endif;
if (is_array($this->data)) {
    if (isset($this->data['extra_data'])) : ?>
        <div class="extra-data"><?= $this->data['extra_data'] ?></div>
    <?php elseif (isset($this->data['small_data'])) :?>
        <small><?= $this->data['small_data'] ?></small>
    <?php endif; ?>
<?php } ?>
