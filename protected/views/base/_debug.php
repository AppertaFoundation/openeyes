<!---
<?php
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

$commit = preg_replace('/[\s\t].*$/s','',@file_get_contents(@$_SERVER['DOCUMENT_ROOT']."/.git/FETCH_HEAD"));
?>
Server: <?php echo $hostname?>

Date: <?php echo date('d.m.Y H:i:s')?>

Commit: <?php echo $commit?>

User agent: <?php echo @$_SERVER['HTTP_USER_AGENT']?>

Client IP: <?php echo @$_SERVER['REMOTE_ADDR']?>

Username: <?php echo $username?>

Firm: <?php echo $firm?>

-->
