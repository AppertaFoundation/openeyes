<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$li_id = 'js-sideEvent' . $event->id;
?>
<li id="<?= $li_id ?>"
    class="<?= implode(' ', $event_li_css) ?> complete"
    data-event-id="<?= $event->id ?>"
    data-event-date="<?= $event->event_date ?>"
    data-created-date="<?= $event->created_date ?>"
    data-event-year-display="<?= substr($event->NHSDate('event_date'), -4) ?>"
    data-event-date-display="<?= $event->NHSDate('event_date') ?>"
    data-event-type="<?= $event_name ?>"
    data-institution="<?= $event->institution->name ?>"
    data-subspecialty="<?= $subspecialty_name ?>"
    data-event-icon='<?= $event->getEventIcon('medium') ?>'
    <?php if ($event_image !== null) { ?>
        data-event-image-url="<?= $event_image->getImageUrl() ?>"
    <?php } ?>
    data-test="sidebar-event"
>
    <div class="tooltip quicklook" style="display: none; ">
        <div class="event-name"><?= $event_name ?></div>
        <div class="event-info"><?= str_replace("\n", "<br/>", $event->info) ?></div>
        <?php $event_icon_class = '';
        $event_issue_text = $event->getIssueText();
        $event_issue_class = 'event-issue';
        if ($event->hasIssue()) {
            $event_issue_class .= ($event->hasIssue('ready') ? ' ready' : ' alert');
        }
        /**
         * getting variable: 'event_icon_class', 'event_issue_class', 'event_issue_text'
         */
        extract($event->getDetailedIssueText($event_icon_class, $event_issue_text, $event_issue_class));

        if (!empty($event_issue_text)) { ?>
            <div class="<?= $event_issue_class ?>">
                <?= $event_issue_text ?>
            </div>
        <?php } ?>
        <div class="event-name">Institution: <strong><?= $event->institution ?? '-' ?></strong></div>
        <div class="event-name">Site: <strong><?= $event->site ?? '-' ?></strong></div>
    </div>

    <a href="<?= $event_path ?>" data-id="<?= $event->id ?>">
        <?php
        if ($event->hasIssue()) {
            if ($event->hasIssue('ready')) {
                $event_icon_class .= ' ready';
            } elseif ($eur = EUREventResults::model()->find('event_id=?', [$event->id]) && $event->hasIssue('EUR Failed')) {
                $event_icon_class .= ' cancelled';
            } elseif ($event->hasIssue('Consent Withdrawn')) {
                $event_icon_class .= ' cancelled';
            } else {
                $event_icon_class .= ' alert';
            }
            if ($event->hasIssue('draft')) {
                $event_icon_class .= ' draft';
            }
        }
        if ($virtual_clinic_event) {
            $event_icon_class .= ' virtual-clinic';
        }

        $event_type_icon_class = 'js-event-a' . $event_icon_class;
        ?>
        <span class="event-type <?= $event_type_icon_class ?>">
            <?= $event->getEventIcon() ?>
        </span>
        <span class="event-extra">
            <?php
            $api = Yii::app()->moduleAPI->get($event->eventType->class_name);

            if ($api !== false && method_exists($api, 'getLaterality')) {
                $this->widget('EyeLateralityWidget', [
                    'show_if_both_eyes_are_null' =>
                      !property_exists($api, 'show_if_both_eyes_are_null') ||
                      $api->show_if_both_eyes_are_null,
                    'eye' => $api->getLaterality($event->id),
                    'pad' => '',
                ]);
            } ?>
        </span>
        <span class="event-date <?= ($event->isEventDateDifferentFromCreated()) ? ' backdated' : '' ?>">
            <?= $event->getEventDate() ?>
        </span>
        <span class="tag"><?= $tag ?></span>
    </a>
</li>
