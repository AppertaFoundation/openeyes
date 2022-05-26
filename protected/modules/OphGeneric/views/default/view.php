<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphGeneric\models\DeviceInformation;

if ($this->checkPrintAccess()) {
    $this->event_actions[] = EventAction::printButton();
}

// Add the open in forum button if FORUM integration is enabled
$device_information = DeviceInformation::model()->findByAttributes(['event_id' => $this->event->id]);
$sop = isset($device_information->sop_instance_uid) ? $device_information->sop_instance_uid : [];

if (!empty($sop) && \SettingMetadata::model()->getSetting('enable_forum_integration') === 'on') {
    array_unshift(
        $this->event_actions,
        EventAction::link(
            'Open In Forum',
            ('oelauncher:forumsop/' . $sop),
            null,
            ['class' => 'button small']
        )
    );
}

$this->beginContent('//patient/event_container');
?>

<?php if ($this->event->delete_pending) { ?>
    <div class="alert-box alert with-icon">
        This event is pending deletion and has been locked.
    </div>
<?php } ?>

<?php $this->renderOpenElements($this->action->id); ?>
<?php $this->renderPartial('//default/delete'); ?>
<?php $this->endContent() ?>
