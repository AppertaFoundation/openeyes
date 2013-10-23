<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<div id="correspondence_out"<?php if ($element->draft) {?> class="draft"<?php }?>>
	<div class="letter_header">
		<?php $this->renderPartial("letter_start", array(
			'site' => $element->site,
			'toAddress' => CHtml::encode($element->address),
			'patient' => $this->patient,
			'date' => $element->date,
			'clinicDate' => $element->clinic_date,
			'directLine' => $element->direct_line,
			'faxNumber' => $element->fax,
		))?>
	</div>

	<?php $this->renderPartial("reply_address", array(
			'site' => $element->site,
	))?>

	<?php $this->renderPartial("print_ElementLetter", array(
			'element' => $element,
	))?>

	<input type="hidden" name="OphCoCorrespondence_printLetter" id="OphCoCorrespondence_printLetter" value="<?php echo $element->print?>" />
	<input type="hidden" name="OphCoCorrespondence_printLetter" id="OphCoCorrespondence_printLetter_all" value="<?php echo $element->print_all?>" />
</div>
