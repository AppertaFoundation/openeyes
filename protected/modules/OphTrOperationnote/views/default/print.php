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

<?php $this->renderPartial('//print/event_header')?>

<div class="container content event">

	<!-- Operation metadata -->
	<?php $this->renderPartial('print_operation_metadata');?>

	<!-- Operation details -->
	<?php $this->renderPartial('print_operation_details');?>

	<!-- Anaesthetic Details -->
	<?php
		$anaesthetic_element = Element_OphTrOperationnote_Anaesthetic::model()->find("event_id = ?", array($this->event->id));
		$this->renderElement($anaesthetic_element, 'print', false, false);
	?>

	<!-- Per-operative drugs -->
	<?php
		$postdrugs_element = Element_OphTrOperationnote_PostOpDrugs::model()->find("event_id = ?", array($this->event->id));
		$this->renderElement($postdrugs_element, 'print', false, false);
	?>

	<!-- Post-op instructions and Comments -->
	<?php
		$comments_element = Element_OphTrOperationnote_Comments::model()->find("event_id = ?", array($this->event->id));
		$this->renderElement($comments_element, 'print', false, false);
	?>

	<!-- Metadata -->
	<?php $this->renderPartial('//print/event_metadata', array(
		'hide_modified' => @$hide_modified
	));?>
</div>
