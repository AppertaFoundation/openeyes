<?php
?>
<ul>
    <?php foreach ($this->data as $item) : ?>
        <li><?= $item ?></li>
    <?php endforeach; ?>
</ul>
<?php if ($this->editable) : ?>
    <div class="edit-widget" style="display: none;">
        <?= CHtml::textArea('edit_' . strtolower($this->title), implode("\n", $this->data), array('rows' => 4)) ?>
    </div>
<?php endif; ?>
