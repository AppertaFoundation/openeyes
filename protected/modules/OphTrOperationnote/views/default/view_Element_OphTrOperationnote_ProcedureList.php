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
if ($element->booking_event_id) {
    $whiteboard_display_mode = SettingMetadata::model()->getSetting('opnote_whiteboard_display_mode');
    $disabled = $this->action->id === 'removed' ? 'disabled' : null;
    if ($whiteboard_display_mode === 'CURRENT') {
        array_unshift(
            $this->event_actions,
            EventAction::link(
                'Display Whiteboard',
                '#',
                null,
                array('class' => 'small button' . " $disabled", 'id' => 'js-display-whiteboard', 'data-id' => $element->booking_event_id)
            ),
            EventAction::link(
                'Close Whiteboard',
                '#',
                null,
                array('class' => 'small button' . " $disabled", 'id' => 'js-close-whiteboard', 'data-id' => $element->booking_event_id)
            )
        );
    } else {
        array_unshift(
            $this->event_actions,
            EventAction::link(
                'Display Whiteboard',
                $this->createUrl('default/whiteboard/' . $element->booking_event_id),
                null,
                array('class' => 'small button' . " $disabled", 'target' => '_blank')
            )
        );
    }
}
?>

<section class="element view full">
  <header class="element-header">
    <h3 class="element-title">Procedure & OPCS codes</h3>
  </header>
  <div class="element-data full-width">
    <div class="cols-10">
      <table class="priority-text last-left">
        <tbody>
        <?php foreach ($element->procedures as $procedure) : ?>
          <tr>
            <td><span class="priority-text">
              <?php echo $element->eye->adjective ?>
              <?php echo $procedure->term ?>
              </span></td>
            <td><span class="priority-text">
                    <?= implode(', ', array_map(function ($x) {
                        return $x->name;
                    }, $procedure->opcsCodes)); ?>
              </span></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>


