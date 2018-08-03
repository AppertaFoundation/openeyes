<?php
$display_queue = $ticket->getDisplayQueueAssignment();
?>
<table class="cols-full">
    <tr>
        <td>
            <div class="data-label"><?= $ticket->getDisplayQueue()->name.' ('.Helper::convertDate2NHS($ticket->getDisplayQueueAssignment()->assignment_date).')' ?></div>
        </td>
        <td>
            <div class="data-value">
                <textarea class="noresize" readonly cols="35" rows="5"><?php echo $display_queue->notes; ?></textarea>
            </div>
        </td>
    </tr>
    <?php if ($display_queue->report) {?>
        <tr>
            <td>
                <div class="data-label">Clinic Info:</div>
            </td>
            <td>
                <div class="data-value"><?= $display_queue->report ?></div>
            </td>
        </tr>
    <?php } ?>

    <?php if ($ticket->priority) {?>
        <tr>
            <td>
                <div class="data-label">Priority:</div>
            </td>
            <td>
                <div class="data-value" style="color: <?= $ticket->priority->colour?>">
                    <?= $ticket->priority->name ?>
                </div>
            </td>
        </tr>
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
            <tr class="<?php if ($old_ass->id == $ticket->getDisplayQueueAssignment()->id) {?>current_queue<?php }?>" style="font-style: italic;">
                <td>
                    <div class="data-label"><?= $old_ass->queue->name ?>:</div>
                </td>
                <td>
                    <div class="data-value"><?= Helper::convertDate2NHS($old_ass->assignment_date)?></div>
                </td>
                <?php if ($old_ass->report) {
                    $notes_width = 3;
                    ?>
                    <td>
                        <div class="data-value"><?= $old_ass->report ?></div>
                    </td>
                <?php } ?>
                <?php if ($old_ass->notes) {?>
                    <td>
                        <div class="data-value"><?= Yii::app()->format->Ntext($old_ass->notes) ?></div>
                    </td>
                <?php }?>
            </tr>
        <?php }
    }?>
</table>
