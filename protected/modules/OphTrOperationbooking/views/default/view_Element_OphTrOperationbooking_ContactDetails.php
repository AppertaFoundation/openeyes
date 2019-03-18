<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
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

<div class="element-data full-width">
    <div class="cols-10">
        <?php if ($element->patient_booking_contact_number != "") { ?>
            <div class="cols-6 column">
                Patient: <?php echo $element->patient_booking_contact_number; ?>
            </div>
        <?php } ?>
        <?php if ($element->collector_name != "" || $element->collector_contact_number != "") { ?>
            <div class="cols-6 column">
                Person collecting: <?php echo $element->collector_name; ?>
                - <?php echo $element->collector_contact_number; ?>
            </div>
        <?php } ?>
    </div>
</div>
