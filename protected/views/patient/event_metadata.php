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
$event = $this->event;
$event_type = $event->eventType->name;

// Find all event modifications that occurred per day
// but remove any consecutive changes by the same user on the same day
$modifications = Yii::app()->db->createCommand('
SELECT last_modified_user_id, last_modified_date FROM (
  SELECT
    id,
    last_modified_user_id,
    last_modified_date,
    @prev_user                  prev_user,
    @prev_date                  prev_date,
    @prev_user := last_modified_user_id curr_user,
    @prev_date := last_modified_date curr_date
  FROM event_version, (SELECT
                           @prev_user := NULL,
                           @prev_date := NULL) vars
  WHERE id = :event_id
  ORDER BY last_modified_Date DESC
) lagged_events
WHERE DATE(last_modified_date) != DATE(prev_date) OR last_modified_user_id != prev_user OR prev_user IS NULL'
)->limit(10)->query(array('event_id' => $event->id));
?>

<div id="js-event-audit-trail" class="oe-popup-event-audit-trail" style="display: none;">
  <table>
    <tbody>
    <?php if ($this->event->firm_id): ?>
        <tr>
            <td class="title">Created under</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><?= $this->event->firm->getNameAndSubspecialty(); ?></td>
            <td></td>
            <td></td>
        </tr>
    <?php endif; ?>
    <?php if (!@$hide_created) { ?>
      <tr>
        <td class="title">Created by</td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td><?php echo $event->user->getFullNameAndTitle(); ?></td>
        <td><?php echo $event->NHSDate('created_date') ?></td>
        <td><?php echo date('H:i', strtotime($event->created_date)) ?></td>
      </tr>
    <?php } ?>
    <?php if (!@$hide_modified) { ?>
      <tr>
        <td class="title">Last Modified by</td>
        <td></td>
        <td></td>
      </tr>
        <?php foreach ($modifications as $modification): ?>
            <?php $modified_user = \User::model()->findByPk($modification['last_modified_user_id']); ?>
        <tr>
          <td><?php echo $modified_user->getFullNameAndTitle(); ?></td>
          <td><?php echo Helper::convertMySQL2NHS($modification['last_modified_date']) ?></td>
          <td><?php echo date('H:i', strtotime($modification['last_modified_date'])) ?></td>
        </tr>
        <?php endforeach; ?>
    <?php } ?>
    </tbody>
  </table>
</div>