<?php
/**
 * @var Worklist[] $worklists
 */
?>

<div class="oe-full-header flex-layout">
    <div class="title wordcaps">Worklists</div>
</div>

<div class="oe-full-content subgrid oe-worklists">
    <main class="oe-full-main">
        <?php foreach ($worklists as $worklist): ?>
            <?php echo $this->renderPartial('_worklist', array('worklist' => $worklist, 'is_printing' => true)); ?>
        <?php endforeach; ?>
    </main>
</div>
