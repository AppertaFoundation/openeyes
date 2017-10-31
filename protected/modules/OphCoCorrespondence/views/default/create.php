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
    'id' => 'correspondence-create',
    'enableAjaxValidation' => false,
    'layoutColumns' => array(
        'label' => 2,
        'field' => 10,
    ),
));

$actions = array('savedraft' => 'Save draft', 'saveprint' => 'Save and print');
if( isset(Yii::app()->params['OphCoCorrespondence_event_actions']['create']) ){
    $actions = Yii::app()->params['OphCoCorrespondence_event_actions']['create'];
}

foreach($actions as $action_id => $action){
    if($action){
        $this->event_actions[] = EventAction::button(
            $action,
            $action_id,
            array('level' => 'secondary'),
            array('id' => 'et_' . $action_id, 'class' => 'button small', 'form' => 'correspondence-create')
        );
    }
}

?>

<?php if (!$this->patient->practice || !$this->patient->practice->contact->address) { ?>
    <div id="no-practice-address" class="alert-box alert with-icon">
        Patient has no GP practice address, please correct in PAS before creating GP letter.
    </div>
<?php } ?>

<?php $this->displayErrors($errors) ?>

<?php
    $correspondence_create_banner = SettingMetadata::model()->findByAttributes(array('key' => 'correspondence_create_banner'));
    $banner_text = $correspondence_create_banner ? $correspondence_create_banner->getSettingName() : false;
?>
    
<?php if($banner_text): ?>
<div class="row data-row">
    <div class="large-10 correspondence_create_banner column">
        <div class="data-label">
            <?php echo $banner_text; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php $this->renderOpenElements($this->action->id, $form); ?>
<?php $this->renderOptionalElements($this->action->id, $form); ?>
<?php $this->displayErrors($errors, true) ?>

<?php $this->endWidget(); ?>

<?php $this->endContent(); ?>
