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

<?php
if ($this->checkPrintAccess()) {
    $this->event_actions[] = EventAction::printButton();
}

// Add the open in forum button if FORUM integration is enabled
$biometry_imported_events=OphInBiometry_Imported_Events::model()->findByAttributes(array('event_id' => $this->event->id));
$sop=isset($biometry_imported_events->sop_uid) ? $biometry_imported_events->sop_uid : array();

if (!empty($sop) && \SettingMetadata::model()->getSetting('enable_forum_integration') === 'on') {
    array_unshift(
        $this->event_actions,
        EventAction::link(
            'Open In Forum',
            ('oelauncher:forumsop/' . $sop),
            null,
            array('class' => 'button small')
        )
    );
}

if ($this->checkEditAccess()) {
    array_unshift(
        $this->event_actions,
        EventAction::link('Choose Lens', Yii::app()->createUrl($this->module->id.'/default/update/'.$this->event->id), null, array('class' => 'button small'))
    );
}

$this->beginContent('//patient/event_container', array('no_face'=>false));
$this->moduleNameCssClass .= ' highlight-fields';

if ($this->event->delete_pending) { ?>
    <div class="alert-box alert with-icon">
        This event is pending deletion and has been locked.
    </div>
<?php }

if ($this->is_auto) {
    ?>
<div id="surgeon">
    <div class="cols-2 column" style="margin-left: 10px;">
        <div class="data-label">Surgeon:
           <b> <?php
            if (isset(Element_OphInBiometry_IolRefValues::model()->findByAttributes(array('event_id' => $this->event->id))->surgeon_id)) {
                echo OphInBiometry_Surgeon::model()->findByAttributes(
                    array('id' => Element_OphInBiometry_IolRefValues::model()->findByAttributes(array('event_id' => $this->event->id))->surgeon_id)
                )->name;
            }
            ?>
           </b>
        </div>
    </div>
</div>
<?php }

$this->renderOpenElements($this->action->id); ?>
<?php $this->renderPartial('//default/delete');?>
<?php $this->renderPartial('_va_view', ['action' => 'view']);?>
<?php $this->endContent()?>
