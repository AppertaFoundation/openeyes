<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<?php echo $this->renderPartial('/letters/letter_start', array(
		'to' => $to,
		'accessible' => false,
		'patient' => $patient,
		'patient_ref' => true,
		)); ?>

<p>
	This patient was recently referred to this hospital and a decision was
	made that surgery was appropriate under the care of
	<?php echo CHtml::encode($consultantName) ?>.
</p>

<p>In accordance with the National requirements our admission system
	provides patients with the opportunity to agree the date for their
	operation. We have written twice to ask the patient to contact us to
	discuss and agree a date but we have had no response.</p>

<p>Therefore we have removed this patient from our waiting list and we
	are referring them back to you.</p>

<?php echo $this->renderPartial('/letters/letter_end'); ?>
	