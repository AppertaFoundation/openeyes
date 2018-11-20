<?php
/* @var $this PracticeController */
/* @var $model Practice */

$this->pageTitle = 'View Practice';
?>
<div>
    <div class="oe-full-header flex-layout">
        <div class="title wordcaps">
            Practice <b>Summary</b>
        </div>
    </div>

    <div class="oe-full-content oe-new-patient flex-layout flex-top">

        <section class="box patient-info patient-contacts js-toggle-container">
            <h3 class="box-title">Contact Information:</h3>
            <a href="#" class="toggle-trigger toggle-hide js-toggle">
            <span class="icon-showhide">
                Show/hide this section
            </span>
            </a>
            <div class="js-toggle-body">
                <table class="standard">
                    <tbody>
                    <tr>
                        <td>
                            <?php echo CHtml::label('Practice Contact', null); ?>
                        </td>
                        <td>
                            <?php echo CHtml::encode($model->contact->getFullName()); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo CHtml::label('Practice Address', null); ?>
                        </td>
                        <td>
                            <?php echo CHtml::encode($model->getAddressLines()); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo CHtml::label('Code', null); ?>
                        </td>
                        <td>
                            <?php echo CHtml::encode($model->code); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo CHtml::activeLabelEx($model->contact, 'primary_phone'); ?>
                        </td>
                        <td>
                            <?php echo isset($model->contact->primary_phone) ? CHtml::encode($model->contact->primary_phone) : 'Unknown'; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="align-right">
                <?php if (Yii::app()->user->checkAccess('TaskCreatePractice')): ?>
                    <div class="large-4 column end">
                        <div class="box generic">
                            <div class="row">
                                <div class="large-12 column end">
                                    <p><?php echo CHtml::link('Update Practice Details',
                                            $this->createUrl('/practice/update', array('id' => $model->id))); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</div>

