<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
$this->beginContent('//patient/event_container', array('no_face'=>true));

// Event actions
if ($this->canMarkRead()) {
    $this->event_actions[] = EventAction::link(
        'Mark message as read',
        Yii::app()->createUrl($this->getModule()->name.'/Default/markRead/'.$this->event->id),
        array('level' => 'read'),
        array('class' => 'small')
    );
}

if ($this->checkPrintAccess()) {
    $this->event_actions[] = EventAction::printButton();
}
?>

<?php if ($this->event->delete_pending) {?>
    <div class="alert-box alert with-icon">
        This event is pending deletion and has been locked.
    </div>
<?php }?>

<?php $this->displayErrors(@$errors)?>
<?php $this->renderOpenElements($this->action->id)?>
<?php $this->renderPartial('//default/delete');?>

<?php $this->endContent();?>
