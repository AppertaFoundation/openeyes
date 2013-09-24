<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
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

$commit = preg_replace('/[\s\t].*$/s','',@file_get_contents(Yii::app()->basePath."/../.git/FETCH_HEAD"));
$ex = explode('/',file_get_contents(".git/HEAD"));
$branch = array_pop($ex);
?>
<div id="debug-info-modal">
	<p><strong>This information is provided to assist the helpdesk in diagnosing any problems</strong></p>
	<code>
		Served, with love, by: <?php echo $hostname?><br />
		Docroot: <?php echo @$_SERVER['DOCUMENT_ROOT']?><br />
		Base dir: <?php echo Yii::app()->basePath?><br />
		Date: <?php echo date('d.m.Y H:i:s')?><br />
		Commit: <?php echo $commit?><br />
		Branch: <?php echo $branch?><br/>
		User agent: <?php echo wordwrap(@$_SERVER['HTTP_USER_AGENT'], 80, "<br />\n");?>
		Client IP: <?php echo @$_SERVER['REMOTE_ADDR']?><br />
		Username: <?php echo $username?><br />
		Firm: <?php echo $firm?><br />

	</code>
</div>
