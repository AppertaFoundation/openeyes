<?php
$session = $operation->booking->session; ?>
<strong>Date of operation:</strong> <?php echo date('F j, Y', strtotime($session->date)); ?><br />
<strong>Session time:</strong> <?php echo substr($session->start_time, 0, 5) . ' - ' . substr($session->end_time, 0, 5); ?><br />
<strong>Duration of operation:</strong> <?php echo $operation->total_duration . ' minutes'; ?>