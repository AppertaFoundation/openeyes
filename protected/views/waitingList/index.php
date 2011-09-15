<?php

Yii::app()->clientScript->registerCoreScript('jquery');

?>
<h3 class="title">Waiting list</h3>

<div id="theatreList">
<?php

if (empty($operations)) { ?>
<h2 class="theatre">The waiting list for this service is empty.</h2>
<?php
} else {
?>
    <table>
    <tr>
        <th class="repeat leftAlign">Patient</th>
	<th class="repeat leftAlign">Hosnum</th>
	<th class="repeat leftAlign">Procedures</th>
	<th class="repeat leftAlign">Eye</th>
	<th class="repeat leftAlign">Consultant</th>
	<th class="repeat leftAlign">Decision Date</th>
	<th class="repeat leftAlign">Book Status</th>
    </tr>
<?php
        foreach ($operations as $id => $operation) {
?>
    <tr>
        <td class="patient leftAlign">
<?php
        echo CHtml::link(
                $operation['first_name'] . ' ' . $operation['last_name'],
                Yii::app()->createUrl('patient/view', array(
                                        'id' => $operation['pid'],
					'tabId' => 1,
					'eventId' => $operation['evid']
                ))
        );
?>
</td><td>
<?php echo $operation['hos_num'] ?>
</td><td>
<?php echo $operation['List'] ?>
</td><td>
<?php echo $operation['eye'] ?>
</td><td>
CONSULTANT
</td><td>
<?php echo $operation['decision_date'] ?>
</td><td>
<?php echo $operation['status'] ?>
</td></tr>
<?php
        }
?>
</table>
<?php
}
?>
</div>
