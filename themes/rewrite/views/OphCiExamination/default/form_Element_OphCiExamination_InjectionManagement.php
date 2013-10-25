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

<div id="div_<?php echo get_class($element)?>_injection"
	class="eventDetail">
	<div class="label">
		<?php echo $element->getAttributeLabel('injection_status_id') ?>:
	</div>
	<div class="data">
		<?php
		$html_options = array('empty'=>'- Please select -', 'options' => array());
		foreach (OphCiExamination_Management_Status::model()->findAll(array('order'=>'display_order')) as $opt) {
			$html_options['options'][(string) $opt->id] = array('data-deferred' => $opt->deferred, 'data-book' => $opt->book, 'data-event' => $opt->event);
		}
		echo CHtml::activeDropDownList($element,'injection_status_id', CHtml::listData(OphCiExamination_Management_Status::model()->findAll(array('order'=>'display_order')),'id','name'), $html_options)?>
	</div>
</div>
<div id="div_<?php echo get_class($element)?>_injection_deferralreason"
	class="eventDetail"
	<?php if (!($element->injection_status && $element->injection_status->deferred)) { ?>
	style="display: none;"
	<?php }?>
	>
	<div class="label">
		<?php echo $element->getAttributeLabel('injection_deferralreason_id')?>:
	</div>
	<div class="data">
		<?php
		$html_options = array('empty'=>'- Please select -', 'options' => array());
		foreach (OphCiExamination_Management_DeferralReason::model()->findAll(array('order'=>'display_order')) as $opt) {
			$html_options['options'][(string) $opt->id] = array('data-other' => $opt->other);
		}
		echo CHtml::activeDropDownList($element,'injection_deferralreason_id', CHtml::listData(OphCiExamination_Management_DeferralReason::model()->findAll(array('order'=>'display_order')),'id','name'), $html_options)?>
	</div>
</div>
<div id="div_<?php echo get_class($element)?>_injection_deferralreason_other"
	class="eventDetail"
	<?php if (!($element->injection_deferralreason && $element->injection_deferralreason->other)) { ?>
		style="display: none;"
	<?php } ?>
	>
	<div class="label">
		&nbsp;
	</div>
	<div class="data">
		<?php echo $form->textArea($element, 'injection_deferralreason_other', array('rows' => "1", 'cols' => "80", 'class' => 'autosize', 'nowrapper' => true) ) ?>
	</div>
</div>
