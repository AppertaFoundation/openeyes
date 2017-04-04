<tr class="<?php
echo (($i % 2 == 0) ? 'even' : 'edd');
echo ' '.strtolower($log->colour);?>"
    id="audit<?php echo $log->id?>" <?php if (@$hidden) {?> style="display: none;"<?php }?>>
    <td>
        <a href="#" id="auditItem<?php echo $log->id?>" class="auditItem">
            <?php echo $log->NHSDate('created_date').' '.substr($log->created_date, 11, 8)?>
        </a>
    </td>
    <td><?php echo $log->user ? $log->user->first_name.' '.$log->user->last_name : '-'?></td>
    <td><?php echo $log->action->name?></td>
    <td><?php echo $log->target_type ? $log->target_type->name : ''?></td>
    <td>
        <?php if ($log->event) { ?>
            <a href="/<?php echo $log->event->eventType->class_name?>/default/view/<?php echo $log->event_id?>">
                <?php echo $log->event->eventType->name?>
            </a>
        <?php } else {?>
            -
        <?php }?>
    </td>
</tr>