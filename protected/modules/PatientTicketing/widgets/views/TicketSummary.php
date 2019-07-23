<?php
/**
 * @var OEModule\PatientTicketing\models\Ticket $ticket
 */

$display_queue = $ticket->getDisplayQueueAssignment();
?>

<?php if ($display_queue->report) { ?>
    <div class="row divider">
        <?= $ticket->getDisplayQueue()->name . ' (' . Helper::convertDate2NHS($ticket->getDisplayQueueAssignment()->assignment_date) . ')' ?>
    </div>
    <div class="row divider js-report">
        <h3>Clinic Info</h3>
        <div class="row-divider">
            <div class="data-value">
                <?= $display_queue->report ?>
            </div>
        </div>
    </div>
<?php } ?>

<div class="row">
    <div class="data-value">
        <textarea class="noresize cols-full" readonly cols="35" rows="5"><?php echo $display_queue->notes; ?></textarea>
    </div>
</div>

<?php if ($ticket->hasHistory()) {
    $notes_width = 6; ?>
    <hr style="margin: 0px 0px 4px 0px;"/>
    <?php foreach ($ticket->queue_assignments as $old_ass) {
        if ($old_ass->id == $display_queue->id) {
            continue;
        }
        ?>
        <tr class="<?php if ($old_ass->id == $ticket->getDisplayQueueAssignment()->id) {
            ?>current_queue<?php
                   } ?>"
            style="font-style: italic;">
            <td>
                <div class="data-label"><?= $old_ass->queue->name ?>:</div>
            </td>
            <td>
                <div class="data-value"><?= Helper::convertDate2NHS($old_ass->assignment_date) ?></div>
            </td>
            <?php if ($old_ass->report) {
                $notes_width = 3;
                ?>
                <td>
                    <div class="data-value js-report">
                        <?= $old_ass->report; ?>
                    </div>
                </td>
            <?php } ?>
            <?php if ($old_ass->notes) { ?>
                <td>
                    <div class="data-value">
                        <textarea class="noresize cols-full" readonly cols="35" rows="5"><?= $old_ass->notes ?></textarea>
                    </div>
                </td>
            <?php } ?>
        </tr>
    <?php }
} ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('.js-report table').addClass('borders');
    });
</script>
