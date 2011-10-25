<?php

if (trim(`uname`) == "Darwin") {
	$git = "/opt/local/bin/git";
} else {
	$git = "/usr/bin/git";
}

if (!empty(Yii::app()->session['user'])) {
	$user = Yii::app()->session['user'];
} else {
	$user = User::model()->findByPk(Yii::app()->user->id);
}
$firm = Firm::model()->findByPk($this->selectedFirmId);

if (file_exists("/etc/hostname")) {
	$hostname = trim(file_get_contents("/etc/hostname"));
} else {
	$hostname = trim(`hostname`);
}

if (is_object($user)) {
	$username = "$user->username ($user->id)";
	$firm = "$firm->name ($firm->id)";
} else {
	$username = 'Not logged in';
	$firm = 'Not logged in';
}
?>
<div id="debug-info-modal">
	<ul>
		<li>Server: <?php echo $hostname?></li>
		<li>Docroot: <?php echo @$_SERVER['DOCUMENT_ROOT']?></li>
		<li>Date: <?php echo date('d.m.Y H:i:s')?></li>
		<li>Commit: <?php echo trim(`$git log |head -n1 |cut -d ' ' -f2`)?></li>
		<li>User agent: <?php echo @$_SERVER['HTTP_USER_AGENT']?></li>
		<li>Client IP: <?php echo @$_SERVER['REMOTE_ADDR']?></li>
		<li>Username: <?php echo $username?></li>
		<li>Firm: <?php echo $firm?></li>
	</ul>
</div>
