<?php
$helpdesk_phone = isset(Yii::app()->params['helpdesk_phone']) ? Yii::app()->params['helpdesk_phone'] : '';
$helpdesk_email = isset(Yii::app()->params['helpdesk_email']) ? Yii::app()->params['helpdesk_email'] : '';
$helpdesk_hours = isset(Yii::app()->params['helpdesk_hours']) ? Yii::app()->params['helpdesk_hours'] : '(8:00am to 8:00pm)';
?>
<h3>Support Options</h3>
<ul>
    <li>Immediate support <?php echo $helpdesk_hours ?> - Phone <?php echo $helpdesk_phone ?></li>
    <li>Less urgent issues email <a href="mailto:<?php echo $helpdesk_email ?>"><?php echo $helpdesk_email ?></a></li>
</ul>