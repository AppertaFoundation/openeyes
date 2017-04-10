<?php
    $version_date = Yii::app()->db->createCommand('select version_date from automatic_examination_event_log_version WHERE version_id = '.$log->version_id)->queryScalar();
?>
<tr class="<?php echo (($i % 2 == 0) ? 'even' : 'edd'); ?>">
    <td><?php echo  Helper::convertMySQL2NHS($version_date, '').' '.substr($version_date, 11, 8)?></td>
    <td><?php echo $log->event->user->getFullName() ?></td>
    <td><?php echo $log->invoice_status ? $log->invoice_status->name : ' - ' ?></td>
    <td><?php echo $log->comment ?></td>
</tr>