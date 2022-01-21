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
$settings = new SettingMetadata();
$this->pageTitle = ((string)SettingMetadata::model()->getSetting('use_short_page_titles') != "on" ? Yii::app()->name . ' - ' : '') . 'Device ready';
$user = Yii::app()->session['user'];
?>

<div class="oe-login">

    <div class="login">

        <h1>Device ready</h1>

        <div class="highlighter inverted flex-c">
            Linked to: <?= $user->first_name . ' ' . $user->last_name; ?>
        </div>

        <div class="info flex-c">
            <b><i class="oe-i sync no-click"></i></b>
        </div>

    </div><!-- login -->

</div>

<script type="text/javascript">
    function onSuccess(msg) {
        const response = JSON.parse(msg);
        if (response.status && response.event_id) {
            let url = `/${response.module_id}/default/sign/${response.event_id}?`;
            delete(response["module_id"], response["event_id"]);
            window.location.href = url + $.param(response) + "&deviceSign=1"
        }
    }

    async function subscribe() {
        let response = await fetch("/site/pollSignatureRequests");
        if (response.status === 502) {
            // Connection timeout error, try reconnecting
            await subscribe();
        } else if (response.status !== 200) {
            // Another error
            // Reconnect a little later
            await new Promise(resolve => setTimeout(resolve, 2000));
            await subscribe();
        } else {
            let message = await response.text();
            onSuccess(message);
        }
    }

    subscribe();
</script>