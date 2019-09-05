<?php
/* @var $this PatientMergeRequestController */
/* @var $data PatientMergeRequest */
?>

<div class="view">

    <b><?=\CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
    <?=\CHtml::link(CHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
    <br />

    <b><?=\CHtml::encode($data->getAttributeLabel('primary_id')); ?>:</b>
    <?=\CHtml::encode($data->primary_id); ?>
    <br />

    <b><?=\CHtml::encode($data->getAttributeLabel('primary_hos_num')); ?>:</b>
    <?=\CHtml::encode($data->primary_hos_num); ?>
    <br />

    <b><?=\CHtml::encode($data->getAttributeLabel('primary_nhsnum')); ?>:</b>
    <?=\CHtml::encode($data->primary_nhsnum); ?>
    <br />

    <b><?=\CHtml::encode($data->getAttributeLabel('primary_dob')); ?>:</b>
    <?=\CHtml::encode($data->primary_dob); ?>
    <br />

    <b><?=\CHtml::encode($data->getAttributeLabel('primary_gender')); ?>:</b>
    <?=\CHtml::encode($data->primary_gender); ?>
    <br />

    <b><?=\CHtml::encode($data->getAttributeLabel('secondary_id')); ?>:</b>
    <?=\CHtml::encode($data->secondary_id); ?>
    <br />

</div>