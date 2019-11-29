<?php
/* @var $this GpController */
/* @var $model Gp */

$this->pageTitle = 'View Practitioner';
?>
<div class="oe-home oe-allow-for-fixing-hotlist">
    <div class="oe-full-header flex-layout">
        <div class="title wordcaps">
            <b>Practitioner Summary</b>
        </div>
    </div>
    <div class="oe-full-content oe-new-patient flex-layout flex-top">
        <div class="cols-6 box patient-info js-toggle-container">
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
                            Name:
                        </td>
                        <td>
                            <?php echo CHtml::encode($model->getCorrespondenceName()); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Phone Number:
                        </td>
                        <td>
                            <?php echo isset($model->contact->primary_phone) ? CHtml::encode($model->contact->primary_phone) : 'Unknown'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Role:
                        </td>
                        <td>
                            <?php echo CHtml::encode(isset($model->contact->label) ? $model->contact->label->name : ''); ?>
                        </td>
                    </tr>
                    </tbody>
                    <!--Add the address row here when GPs get tied directly to practices rather than through patient records.-->
                </table>
            </div>
        </div>
        <?php if (Yii::app()->user->checkAccess('TaskCreateGp')) : ?>
            <div class="large-4 column end">
                <div class="box generic">
                    <div class="row">
                        <div class="large-12 column end">
                            <p><?php echo CHtml::link('Update Practitioner Details',
                                    $this->createUrl('/gp/update', array('id' => $model->id))); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
