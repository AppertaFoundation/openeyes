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
<?php $this->beginContent('//patient/event_container', array('no_face' => true)); ?>

    <?php
        // Event actions
    if ($this->checkPrintAccess()) {
        // TODO: need to check if the event is draft!
        $elementLetter = ElementLetter::model()->findByAttributes(array('event_id' => $this->event->id));

        if ($elementLetter->draft) {
            $this->event_actions[] = EventAction::button('Print Draft', 'print', null, array('class' => 'small'));
        } else {
            $this->event_actions[] = EventAction::button('Print', 'print', null, array('class' => 'button small'));
            $this->event_actions[] = EventAction::button(
                'Print all',
                'printall',
                null,
                array(
                    'id' => 'et_print_all',
                    'class' => 'small',
                )
            );

            // check if the current institution is in the list of institutions that can export letters, or if the list is empty add the current institution
            // (empty = always allow export)
            $institution = Institution::model()->getCurrent();
            $institutions = !empty(Yii::app()->params['correspondence_export_institutions']) ? Yii::app()->params['correspondence_export_institutions'] : [$institution->remote_id];

            if (in_array($institution->remote_id, $institutions) && $elementLetter->exportUrl !== null) {
                $this->event_actions[] = EventAction::button('Export', 'export', null, ['id' => 'et_export', 'class' => 'small']);
            }
        }
    }
    ?>

    <?php if ($this->event->delete_pending) {?>
        <div class="alert-box alert with-icon">
            This event is pending deletion and has been locked.
        </div>
    <?php }?>

    <input type="hidden" id="moduleCSSPath" value="<?php echo $this->assetPath?>css" />

    <?php $this->renderOpenElements($this->action->id); ?>
<?php $this->renderPartial('//default/delete');?>
<?php $this->endContent();?>
