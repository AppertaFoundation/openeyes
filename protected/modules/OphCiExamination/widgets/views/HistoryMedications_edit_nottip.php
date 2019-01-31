<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
$element_name = $element->getElementTypeName();
if ($this->missing_prescription_items) {
    $start = $this->is_latest_element
        ? 'A newer prescription event exists'
        : 'Newer examination and prescription event(s) exist';
    $end = 'please create a new Examination. New prescription items are only included in new Examination events.';
} else {
    $start = 'A newer examination event exists';
    $end = 'please go to the latest Examination, or create a new Examination';
}
?>

<p class="alert-box warning" style="margin-bottom: 0px;">Changes will not affect the current <?= $element_name ?> state for patient.
    <?= $start ?> for this patient. To update the current state of <?= $element_name ?>, <?= $end ?>">
</p>
