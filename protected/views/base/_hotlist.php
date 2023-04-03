<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$openHotlistItems = UserHotlistItem::model()->getHotlistItems(1);
$closedHotlistItems = UserHotlistItem::model()->getHotlistItems(0, date('Y-m-d'));
$core_api = new CoreAPI();
$institution_id = Institution::model()->getCurrent()->id;
$site_id = Yii::app()->session['selected_site_id'];
$display_primary_number_usage_code = SettingMetadata::model()->getSetting('display_primary_number_usage_code');
$user = User::model()->findByPk(Yii::app()->user->id);

$messaging_api = new OEModule\OphCoMessaging\components\OphCoMessaging_API();
list($message_counts, $total_unread_messages) = $messaging_api->getMessageCounts($user);

$is_editing_event = false;
if ($this instanceof BaseEventTypeController) {
    $is_editing_event = in_array($this->action->id, ["update", "create"]);
}
?>
<div class="oe-hotlist-panel" id="js-hotlist-panel">
    <?php $this->beginWidget('CActiveForm', [
        'id' => 'hotlist-search-form',
        'action' => Yii::app()->createUrl('site/search'),
    ]); ?>
        <div class="hotlist-search-patient">
            <div class="search-patient">
                <?=\CHtml::textField('query', '', [
                        'autocomplete' => 'off',
                        'id' => 'hotlist-search-text-field',
                        'class' => 'search',
                        'placeholder' => 'Search',
                  ]); ?>
                <button type="submit" id="js-hotlist-find-patient" class="button pro-theme" data-test="hotlist-find-patient-btn">Find Patient</button>
            </div>
        </div>
    <?php $this->endWidget(); ?>
    <div class="patient-activity">
    <?php
    if ($total_unread_messages > 0) {
        $total_urgent_messages = 0;

        foreach ($message_counts as $mailbox_id => $counts) {
            $total_urgent_messages = $total_urgent_messages + ($counts['count_unread_urgent'] ?? 0);
        }
        ?>
        <div class="flag-urgent-messages">
            <a href="/" class="button<?= $total_urgent_messages > 0 ? ' urgent' : ''?>">
                Messages: <?= $total_unread_messages ?> unread <?= $total_urgent_messages > 0 ? ('(' . $total_urgent_messages . ' urgent)') : '' ?>
            </a>
        </div>
    <?php } ?>
        <div class="patients-open">
            <div class="overview">
                <h3>Open
                    <small class="count"><?= count($openHotlistItems) ?></small>
                </h3>
            </div>

            <!-- Open Items -->
            <table class="activity-list open">
                <tbody>

                <?php foreach ($openHotlistItems as $hotlistItem) : ?>
                    <?php echo $this->renderPartial('//base/_hotlist_item', [
                        'hotlistItem' => $hotlistItem,
                        'core_api' => $core_api,
                        'institution_id' => $institution_id,
                        'site_id' => $site_id,
                        'display_primary_number_usage_code' => $display_primary_number_usage_code,
                        ]); ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Closed Items. users can select date to view all patients closed on that date -->
        <div class="patients-closed">

            <div class="overview flex-layout">
                <h3>Closed: <span class="for-date" id="js-pickmeup-closed-date">Today</span>
                    <small class="count"><?= count($closedHotlistItems) ?></small>
                </h3>
                <div class="closed-search">
                    <span class="closed-date" id="js-hotlist-closed-today">Today</span>
                    <span class="closed-date" id="js-hotlist-closed-select">Select date</span>
                    <div id="js-pickmeup-datepicker" style="display: none;"></div>
                </div>
            </div>

            <table class="activity-list closed">
                <tbody>
                <?php foreach ($closedHotlistItems as $hotlistItem) : ?>
                    <?php echo $this->renderPartial('//base/_hotlist_item', [
                        'hotlistItem' => $hotlistItem,
                        'core_api' => $core_api,
                        'institution_id' => $institution_id,
                        'site_id' => $site_id,
                        'display_primary_number_usage_code' => $display_primary_number_usage_code,
                        ]); ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div id="hotlist-quicklook" class="hotlist-quicklook"></div>
    </div>
</div>

<script>
    window.is_editing_event = <?= json_encode($is_editing_event) ?>;
</script>
