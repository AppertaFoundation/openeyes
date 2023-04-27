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

$li_id = 'js-sideDraft' . $draft->id;
$event_name = $draft->getEventName();
$event_path = Yii::app()->createUrl($draft->originating_url . "&draft_id=" . $draft->id);
?>
<li id="<?= $li_id ?>"
    class="event draft"
    data-draft-id="<?= $draft->id ?>"
    data-draft-date="<?= $draft->last_modified_date ?>"
    data-created-date="<?= $draft->created_date ?>"
    data-draft-year-display="<?= substr($draft->NHSDate('last_modified_date'), -4) ?>"
    data-draft-date-display="<?= $draft->NHSDate('last_modified_date') ?>"
    data-event-type="<?= $event_name ?>"
    data-institution="<?= $draft->institution->name ?>"
    data-subspecialty="<?= $subspecialty_name ?>"
    data-event-icon='<?= $draft->getEventIcon('medium') ?>'
    data-test="sidebar-draft"
>
    <div class="tooltip quicklook" style="display: none; ">
        <div class="event-name"><?= $event_name ?></div>

        <div class="event-issue draft">
            Draft - last worked on by: <?= $draft->last_updated_user->getFullName() ?>
        </div>
        <div class="event-name">Institution: <strong><?= $draft->institution ?? '-' ?></strong></div>
        <div class="event-name">Site: <strong><?= $draft->site ?? '-' ?></strong></div>
    </div>

    <a href="<?= $event_path ?>" data-id="<?= $draft->id ?>">
        <?php
        $event_type_icon_class = 'draft';
        ?>
        <span class="event-type <?= $event_type_icon_class ?>">
            <?= $draft->getEventIcon() ?>
        </span>
        <span class="event-extra"></span>
        <span class="event-date">
            <?= $draft->NHSDateAsHTML('last_modified_date') ?>
        </span>
        <span class="tag"><?= $tag ?></span>
    </a>
</li>
