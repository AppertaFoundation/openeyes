<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
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

$delete_action = null;
if (isset($this->event_actions)) {
    foreach ($this->event_actions as $i => $event_action) {
        if ($event_action->label == "Delete") {
            $delete_action = $event_action;
            unset($this->event_actions[$i]);
            break;
        }
    }
}

?>
<div class="buttons-right">

    <?php
    $print_actions = array();
    foreach ($this->event_actions as $key => $action) {
        if (isset($action->htmlOptions['name']) && strpos(strtolower($action->htmlOptions['name']), 'print') === 0) {
            $print_actions[] = $action;
        } else {
            echo $action->toHtml();
        }
    }

    if (!empty($print_actions)) {
        echo EventAction::printDropDownButtonAsHtml($print_actions);
    }

    if (isset($delete_action)) {
        echo $delete_action->toHtml();
    }
    ?>
</div>
