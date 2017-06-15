<?php
$display_queue = $ticket->getDisplayQueueAssignment();
?>
<div class="panel ticketsummary-panel">
    <div class="row data-row">
        <div class="large-2 column left">
            <div class="data-label"><?= $ticket->getDisplayQueue()->name.' ('.Helper::convertDate2NHS($ticket->getDisplayQueueAssignment()->assignment_date).')' ?></div>
        </div>
        <div class="large-10 column left">
            <div class="data-value">
                <textarea class="noresize" readonly rows="5"><?php echo $display_queue->notes; ?></textarea>
            </div>
        </div>
    </div>
    <?php if ($display_queue->report) {?>
        <div class="row data-row">
            <div class="large-2 column">
                <div class="data-label">Clinic Info:</div>
            </div>
            <div class="large-10 column left">
                <div class="data-value"><?= $display_queue->report ?></div>
            </div>
        </div>
    <?php } ?>

    <?php if ($ticket->priority) {?>
        <div class="row data-row">
            <div class="large-2 column">
                <div class="data-label">Priority:</div>
            </div>
            <div class="large-10 column left">
                <div class="data-value" style="color: <?= $ticket->priority->colour?>">
                    <?= $ticket->priority->name ?>
                </div>
            </div>
        </div>
    <?php }?>

    <?php if ($ticket->hasHistory()) {
        $notes_width = 6;
        ?>
        <hr style="margin: 0px 0px 4px 0px;"/>
        <?php foreach ($ticket->queue_assignments as $old_ass) {
            if ($old_ass->id == $display_queue->id) {
                continue;
            }
            ?>
            <div class="row data-row<?php if ($old_ass->id == $ticket->getDisplayQueueAssignment()->id) {?> current_queue<?php }?>" style="font-style: italic;">
                <div class="large-2 column">
                    <div class="data-label"><?= $old_ass->queue->name ?>:</div>
                </div>
                <div class="large-2 column left">
                    <div clas="data-value"><?= Helper::convertDate2NHS($old_ass->assignment_date)?></div>
                </div>
                <?php if ($old_ass->report) {
                    $notes_width = 3;
                    ?>
                    <div class="large-3 column left">
                        <div class="data-value"><?= $old_ass->report ?></div>
                    </div>
                <?php } ?>
                <?php if ($old_ass->notes) {?>
                    <div class="large-<?= $notes_width ?> column left">
                        <div class="data-value"><?= Yii::app()->format->Ntext($old_ass->notes) ?></div>
                    </div>
                <?php }?>
            </div>
        <?php }
    }?>
</div>