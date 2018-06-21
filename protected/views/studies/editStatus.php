<?php
?>

<div class="box">
    <h2>Edit Status</h2>

    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'edit-study-status-form',
        'enableAjaxValidation' => false,
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    ));
    ?>
    <input type="hidden" name="return" value="<?=Yii::app()->request->getQuery('return', '')?>">
    <div class="data-group">
        <div class="cols-2 column">
            <label>Subject:</label>
        </div>
        <div class="cols-5 column end">
            <?= $pivot->subject->patient->fullName ?>
        </div>
    </div>
    <div class="data-group">
        <div class="cols-2 column">
            <label>Study:</label>
        </div>
        <div class="cols-5 column end">
            <?= $pivot->study->name ?>
        </div>
    </div>
    <?php
    $form->dropDownList(
        $pivot,
        'participation_status_id',
        CHtml::encodeArray(
            CHtml::listData(
                StudyParticipationStatus::model()->findAll(),
                'id',
                'status'
            )
        )
    );

    $form->checkBox($pivot, 'is_consent_given', array());

    $form->dropDownList(
        $pivot,
        'consent_received_by',
        CHtml::encodeArray(
            CHtml::listData(
                User::model()->findAll(),
                'id',
                'fullName'
            )
        ),
        array('empty' => '-- Select --')
    );

    $form->textArea($pivot, 'comments');

    $form->formActions();
    ?>

    <?php $this->endWidget() ?>
</div>