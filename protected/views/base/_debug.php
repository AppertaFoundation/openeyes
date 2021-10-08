<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<!---
<?php
if (!empty(Yii::app()->session['user_auth'])) {
    $user_auth = Yii::app()->session['user_auth'];
    $user = $user_auth->user;
} else {
    $user_auth = null;
    $user = User::model()->findByPk(Yii::app()->user->id);
}
$firm = Firm::model()->findByPk($this->selectedFirmId);

if (file_exists('/etc/hostname')) {
    $hostname = trim(file_get_contents('/etc/hostname'));
} else {
    $hostname = trim(`hostname`);
}

if (is_object($user_auth)) {
    $username = "$user_auth->username ($user_auth->id)";
    if ($firm) {
        $firm = "$firm->name ($firm->id)";
    } else {
        $firm = 'Not found'; // selectedFirmId seems to not be getting initialised sometimes
    }
} else {
    $username = 'Not logged in';
    $firm = 'Not logged in';
}

$commit = preg_replace('/[\s\t].*$/s', '', @file_get_contents(@$_SERVER['DOCUMENT_ROOT'] . '/.git/FETCH_HEAD'));
?>
Server: <?php echo $hostname?>

Date: <?php echo date('d.m.Y H:i:s')?>

Commit: <?php echo $commit?>

User agent: <?php echo htmlspecialchars(@$_SERVER['HTTP_USER_AGENT'])?>

Client IP: <?php echo htmlspecialchars(@$_SERVER['REMOTE_ADDR'])?>

Username: <?php echo $username?>

Firm: <?php echo $firm?>

-->
