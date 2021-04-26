<?php
/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 *
 * Passed in parameters, via the return array from Yii::app()->session['user']->getUserDaysLeft();
 * @var string $DaysExpire the number of days before the user's password is expired
 * @var string $DaysLock the number of days before the user's password is locked
 * @var string $DaysStale the number of days before the user's password is stale, is also passed in but not used
 */
?>
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id' => 'version-check-dialog',
        'options' => array(
            'title' => $this->title,
            'dialogClass' => 'dialog',
            'autoOpen' => true,
            'modal' => true,
            'draggable' => false,
            'resizable' => false,
            'width' => 450,
        ),
    ));
?>
	<p>
		Would you like to enable auto version check to share details about the current installation version?
	</p>
    <p>
        Enabling this will send following information: IP address, db version, php version, web server version, user number, patient number, event number, OE version, commit number, git info, branch.
    </p>

	<div class="buttons">
		<button class="secondary small" type="button" id="version_yes">
            Yes Enable
		</button>
		<button class="warning small" type="button" id="version_no">
            No Disable
		</button>
	</div>
<?php $this->endWidget()?>

<script type="text/javascript">
    $('#version_no').click(function () {
        $.ajax({
            'type': 'POST',
            'url': baseUrl + '/admin/changeVersionCheck',
            'data': "value=disable" + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
            complete: function () {
                $('#version-check-dialog').dialog('close');
            }
        });
    });
    $('#version_yes').click(function () {
        $.ajax({
            'type': 'POST',
            'url': baseUrl + '/admin/changeVersionCheck',
            'data': "value=enable" + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
            complete: function () {
                $('#version-check-dialog').dialog('close');
            }
        });
    });
</script>
