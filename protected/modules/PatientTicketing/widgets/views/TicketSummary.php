<?php
/**
 * @var OEModule\PatientTicketing\models\Ticket $ticket
 */

$display_queue = $ticket->getDisplayQueueAssignment();
$is_patient_called = null;
$data = json_decode($display_queue->details,true);
if($data) {
    if (array_key_exists('value', $data[0])) {
        if (is_array($data[0]['value'])) {
            $is_patient_called = array_key_exists('is_patient_called', $data[0]['value']) ? $data[0]['value']['is_patient_called'] : null;
        }
    }
}
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
<?php if ($is_patient_called!==null) {?>
    <div class="row divider">
        <div class="large-4 column">
            <div class="data-label">Did you telephone the patient during this review?</div>
        </div>
        <div class="large-6 column left">
            <div class="data-value"><?= $is_patient_called ? 'Yes' : 'No' ?></div>
        </div>
    </div>
<?php } ?>
</td>
<td>
<div class="row">
    <div class="data-value">
        <textarea class="noresize cols-full" readonly cols="35" rows="5"><?php echo $display_queue->notes; ?></textarea>
    </div>
</div>

<?php if ($ticket->hasHistory()) {
    $notes_width = 6; ?>
    <hr style="margin: 0px 0px 4px 0px;"/>
    <?php foreach ($ticket->queue_assignments as $old_assignment) {
        if ($old_assignment->id == $display_queue->id) {
            continue;
        }
        ?>
        <tr class="<?php if ($old_assignment->id == $ticket->getDisplayQueueAssignment()->id) {
            ?>current_queue<?php
                   } ?>"
            style="font-style: italic;">
            <td>
                <div class="data-label"><?= $old_assignment->queue->name ?>:</div>
            </td>
            <td>
                <div class="data-value"><?= Helper::convertDate2NHS($old_assignment->assignment_date) ?></div>
            </td>
            <?php if ($old_assignment->report) {
                $notes_width = 3;
                ?>
                <td>
                    <div class="data-value js-report">
                        <?= $old_assignment->report; ?>
                    </div>
                </td>
            <?php } ?>
            <?php if ($old_assignment->notes) { ?>
                <td>
                    <div class="data-value">
                        <textarea class="noresize cols-full" readonly cols="35" rows="5"><?= $old_assignment->notes ?></textarea>
                    </div>
                </td>
            <?php } ?>
        </tr>
    <?php }
} ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('.js-report table').addClass('borders').removeClass('standard');
    });
</script>
