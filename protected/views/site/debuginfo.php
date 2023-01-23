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
<?php
$user_auth = null;
if (!empty(Yii::app()->session['user_auth'])) {
    $user_auth = Yii::app()->session['user_auth'];
}
$firm = Firm::model()->findByPk($this->selectedFirmId);

if (file_exists('/etc/hostname')) {
    $hostname = trim(file_get_contents('/etc/hostname'));
} else {
    $hostname = trim(`hostname`);
}

if (is_object($user_auth)) {
    $username = "$user_auth->username ($user_auth->id)";
    $firm = "$firm->name ($firm->id)";
} else {
    $username = 'Not logged in';
    $firm = 'Not logged in';
}

$thisEnv = Yii::app()->params['environment'];

$buildinfo = "";


if (file_exists('buildinfo.txt')) {
    $buildinfo = '<pre>' . htmlspecialchars(@file_get_contents('buildinfo.txt')) . '</pre>';
} elseif (file_exists('.git/HEAD')) {
    $commit = preg_replace('/[\s\t].*$/s', '', @file_get_contents(Yii::app()->basePath . '/../.git/FETCH_HEAD'));
    $buildinfo .= "Commit: " . htmlspecialchars($commit) . '</br>';
    $buildinfo .= "Commit Date: " . htmlspecialchars(exec(" git log -1 --format=%cd " . $commit)) . ' <br/> ';
    if (strtolower($thisEnv == 'dev')) {
        $lines = shell_exec(Yii::app()->basePath . '/scripts/oe-which.sh');
        $branch = htmlspecialchars(trim(strtr($lines, array(
            chr(27) . '[32m' => '',
            chr(27) . '[39m' => '',
            '--' => ':',
            'Done' => ''
        ))));
    } else {
        if (file_exists('.git/HEAD')) {
            $ex = explode('/', file_get_contents('.git/HEAD'));
            $branch = htmlspecialchars(array_pop($ex));
        }
    }
} else {
    echo "nothing";
}

$imageinfo = '';
if (file_exists('/imageinfo.txt')) {
    $imageinfo = htmlspecialchars(trim(@file_get_contents('/imageinfo.txt')));
}

?>
<div id="debug-info-modal ">
    <p><strong>This information is provided to assist the helpdesk in diagnosing any problems</strong></p>
    <div class="js-to-copy-to-clipboard">
        Served by: <?php echo $hostname?><br />
        Date: <?php echo date('d.m.Y H:i:s') . " (" . date_default_timezone_get() . ")" ?><br />
        OpenEyes Version: <?= Yii::App()->params['oe_version'] ?><br />
        User agent: <?php echo htmlspecialchars(@$_SERVER['HTTP_USER_AGENT']) . "<br/>";?>
        Client IP: <?php echo htmlspecialchars(@$_SERVER['REMOTE_ADDR'])?><br />
        Username: <?php echo $username?><br />
        Firm: <?php echo $firm?><br />
        <?= $buildinfo ?>
        <table style="margin: 0;">
            <colgroup>
                <col class="cols-1">
                <col class="cols-10">
            </colgroup>
            <tbody>
                <tr>
                    <td>Branches: </td>
                    <td><pre><?= $branch ?></pre></td>
                </tr>
            </tbody>
        </table>
        <table style="margin: 0;">
            <colgroup>
                <col class="cols-1">
                <col class="cols-10">
            </colgroup>
            <tbody>
                <tr>
                    <td>Modules running: </td>
                    <td>
                        <div style="column-count: 3">
                            <?php foreach (Yii::app()->modules as $key => $val) {
                                echo $key . "<br/>";
                            } ?>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br />
    <p><?= $imageinfo ? 'Image info: ' . $imageinfo : '' ?></p>
    <p class="js-copy-to-clipboard" data-copy-content-selector=".js-to-copy-to-clipboard" style="cursor: pointer;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19 19" class="oe-i-e" style="background: transparent;">
            <title>Copy to clipboard</title>
            <style>*{fill:#fff;}</style>
            <path d="M15,8.13V15H8.13V8.13H15m2-2H6.13V17H17V6.13Z"/>
            <polygon points="4 10.87 4 4 10.87 4 10.87 5.13 12.87 5.13 12.87 2 2 2 2 12.87 5.13 12.87 5.13 10.87 4 10.87"/>
        </svg>
    </p>
    
</div>
