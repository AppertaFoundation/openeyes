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
$form_id = 'clinical-create';
$this->beginContent('//patient/event_container', array('no_face'=>true , 'form_id' => $form_id));

$clinical = $clinical = $this->checkAccess('OprnViewClinical');

$warnings = $this->patient->getWarnings($clinical);


$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => $form_id,
            'enableAjaxValidation' => false,
            'focus' => '#procedure_id',
            'layoutColumns' => array(
                'label' => 2,
                'field' => 10,
            ),
        ));
        // Event actions
if (isset($_GET["waiting-list"]) && $_GET["waiting-list"]) {
    $this->event_actions = array(
        EventAction::link(
            'Cancel',
            Yii::app()->createUrl('/OphTrOperationbooking/waitingList/index'),
            array('level' => 'cancel')
        ),
    );
    $this->event_actions[] = EventAction::button(
        'Confirm',
        'confirm',
        array(
            'id' => 'et_confirm',
            'level' => 'confirm',
        ),
        array(
            'form' => $form_id,
        )
    );
} else {
    $this->event_actions[] = EventAction::button(
        'Save',
        'save',
        array(
            'id' => 'et_save',
            'level' => 'save',
        ),
        array(
            'form' => $form_id,
        )
    );
}

?>
        <?php if (isset($_GET["waiting-list"]) && $_GET["waiting-list"]) { ?>
            <input type="hidden" name="schedule_now" id="schedule_now" value="1">
        <?php } ?>
        <?php if (Yii::app()->params['OphTrOperationbooking_duplicate_proc_warn']) {?>
            <input type="hidden" name="event_id" value="<?= $this->event->id ?>" />
        <?php } ?>
<?php if ($warnings) { ?>
        <div class="cols-12 column">
            <div class="alert-box patient with-icon">
                <?php foreach ($warnings as $warn) {?>
                    <strong><?php echo $warn['long_msg']; ?></strong>
                    - <?php echo $warn['details'];
                }?>
            </div>
        </div>
<?php }?>

        <?php  $this->displayErrors($errors)?>

        <?php if (@$eur_res && @$eur_answer_res) {?>
            <input type="hidden" name="eye" value="<?=$eur_res->eye_num;?>">
            <input type="hidden" name="eur_result" value="<?=$eur_res->result;?>">
            <?php foreach ($eur_answer_res as $answer_res) {
                    $index = $answer_res->question_id - 1;
                ?>
                <?=\CHtml::hiddenField('EUREventResult[eurAnswerResults]['. $index .'][answer_id]', $answer_res->answer_id)?>
                <?=\CHtml::hiddenField('EUREventResult[eurAnswerResults]['. $index .'][question_id]', $answer_res->question_id)?>
            <?php }?>
            <?php $this->renderPartial('view_eur', array('eur' => $eur_res, 'answerResults' => $eur_answer_res));?>
        <?php } ?>
        <?php  $this->renderOpenElements($this->action->id, $form); ?>
        <?php  $this->renderOptionalElements($this->action->id, $form); ?>
        <?php  $this->displayErrors($errors, true)?>
    <?php $this->endWidget(); ?>

<?php  $this->endContent(); ?>
