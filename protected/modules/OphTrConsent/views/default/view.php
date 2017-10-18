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
if ($this->checkPrintAccess()) {
    $this->event_actions[] = EventAction::printButton();
    $this->event_actions[] = EventAction::button('Print for visually impaired', 'print_va', array(), array('class' => 'button small'));
}
?>
<?php $this->beginContent('//patient/event_container');?>

	<?php if ($this->event->delete_pending) {?>
		<div class="alert-box alert with-icon">
			This event is pending deletion and has been locked.
		</div>
	<?php } elseif (Element_OphTrConsent_Type::model()->find('event_id=?', array($this->event->id))->draft) {?>
		<div class="alert-box alert with-icon">
			This consent form is a draft and can still be edited
		</div>
	<?php }?>

	<?php  $this->renderOpenElements($this->action->id); ?>

	<?php // The "print" value is set by the controller and comes from the user session ?>
	<input type="hidden" name="OphTrConsent_print" id="OphTrConsent_print" value="<?php echo $print;?>" />
	<iframe id="print_iframe" name="print_iframe" style="display: none;"></iframe>

<?php $this->endContent(); ?>
