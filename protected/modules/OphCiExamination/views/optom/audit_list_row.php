<?php
    $user = User::model()->findByPk($log->last_modified_user_id);
?>
<tr class="<?php echo (($i % 2 == 0) ? 'even' : 'odd'); ?>">
    <td><?php echo  Helper::convertMySQL2NHS($log->last_modified_date, '').' '.substr($log->last_modified_date, 11, 8)?></td>
    <td><?php echo $user->first_name.' '.$user->last_name ?></td>
    <td><?php echo $log->invoice_status ? $log->invoice_status->name : ' - ' ?></td>
    <td><?php echo $log->comment ?></td>
</tr>
