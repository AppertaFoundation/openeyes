<?php
/* @var $this PracticeController */
/* @var $model Practice */

$this->pageTitle = 'View Practice';
?>
<div class="oe-home">
    <div class="oe-full-header flex-layout">
        <div class="title wordcaps">
            Practice <b>Summary</b>
        </div>
    </div>

    <div class="oe-full-content oe-new-patient flex-layout flex-top">
        <nav class="cols-3">
            <ul>
                <li>
                    <a href="/practice/index">Back to Practices</a>
                </li>
            </ul>
        </nav>
        <section class="box patient-info patient-contacts js-toggle-container">
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
              <a  href="<?= $this->createUrl('/practice/update', array('id' => $model->id))?>">
                  <button class="button hint blue pad pro-theme"

                >
                    Update Practice Details
                </button>
              </a>
                <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</div>

