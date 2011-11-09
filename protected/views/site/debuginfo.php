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
$branch = array_pop(explode('/',file_get_contents(".git/HEAD")));
?>
<div id="debug-info-modal">
	<code>
		<strong>This information is provided to assist the helpdesk in diagnosing any problems</strong><br />
		Served, with love, by: <?php echo $hostname?><br />
		Docroot: <?php echo @$_SERVER['DOCUMENT_ROOT']?><br />
		Date: <?php echo date('d.m.Y H:i:s')?><br />
		Commit: <?php echo $commit?><br />
		Branch: <?php echo $branch?><br/>
		User agent: <?php echo wordwrap(@$_SERVER['HTTP_USER_AGENT'], 80, "<br />\n");?>
		Client IP: <?php echo @$_SERVER['REMOTE_ADDR']?><br />
		Username: <?php echo $username?><br />
		Firm: <?php echo $firm?><br />
		
	</code>
</div>
