<?php
$helpdesk_phone = ( null !== SettingMetadata::model()->getSetting('helpdesk_phone')) ? SettingMetadata::model()->getSetting('helpdesk_phone') : '';
$helpdesk_email = ( null !== SettingMetadata::model()->getSetting('helpdesk_email')) ? SettingMetadata::model()->getSetting('helpdesk_email') : '';
$helpdesk_hours = ( null !== SettingMetadata::model()->getSetting('helpdesk_hours')) ? SettingMetadata::model()->getSetting('helpdesk_hours') : '(8:00am to 8:00pm)';
?>
<h3>Support Options</h3>
<ul>
    <li>Immediate support <?php echo $helpdesk_hours ?> - Phone <?php echo $helpdesk_phone ?></li>
    <li>Less urgent issues email <a href="mailto:<?php echo $helpdesk_email ?>"><?php echo $helpdesk_email ?></a></li>
</ul>
