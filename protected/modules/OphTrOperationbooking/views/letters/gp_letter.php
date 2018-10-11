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

<div class="booking-letter">
	<header>
		<?php $this->renderPartial('../default/letter_start', array(
            'toAddress' => $toAddress,
            'patient' => $patient,
            'date' => date('Y-m-d'),
            'site' => $site,
        ))?>
	</header>

	<?php echo $this->renderPartial('../letters/letter_introduction', array(
            'to' => $to,
            'accessible' => false,
            'patient' => $patient,
            'patient_ref' => true,
    ))?>

	<p>
		This patient was recently referred to this hospital and a decision was made that surgery was appropriate under the care of <?=\CHtml::encode($consultantName) ?>.
	</p>

	<p>
		In accordance with the National requirements our admission system provides patients with the opportunity to agree the date for their operation. We have written twice to ask the patient to contact us to discuss and agree a date but we have had no response.
	</p>

	<p>
		Therefore we have removed this patient from our waiting list and we are referring them back to you.
	</p>

	<?php echo $this->renderPartial('../letters/letter_end'); ?>
</div>
