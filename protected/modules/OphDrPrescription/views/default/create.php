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
<?php $this->beginContent('//patient/event_container'); ?>

	<?php
        $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => 'prescription-create',
            'enableAjaxValidation' => false,
        ));

        // Event actions
        $this->event_actions[] = EventAction::button('Save draft', 'savedraft', array('level' => 'primary'), array('id' => 'et_save_draft', 'class' => 'button small', 'form' => 'prescription-create'));
        $this->event_actions[] = EventAction::button('Save', 'save', array('level' => 'secondary'), array('id' => 'et_save', 'class' => 'button small', 'form' => 'prescription-create'));
        $this->event_actions[] = EventAction::button('Save and print', 'saveprint', array('level' => 'secondary'), array('id' => 'et_save_print', 'class' => 'button small', 'form' => 'prescription-create'));

        $this->displayErrors($errors)?>

		<?php $this->renderOpenElements($this->action->id, $form); ?>
		<?php $this->renderOptionalElements($this->action->id, $form); ?>

		<?php $this->displayErrors($errors, true)?>
	<?php $this->endWidget(); ?>

	<div id="dialog-confirm-cancel" title="Cancel" class="hide">
		<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>All text entered will be lost. Are you sure?</p>
	</div>

<?php $this->endContent();?>
