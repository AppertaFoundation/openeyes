<?php
/* @var $this GpController */
/* @var $model Gp */
/* @var $context String */
$this->pageTitle = 'Create Practitioner';
?>
<div class="oe-home oe-allow-for-fixing-hotlist">
    <div class="oe-full-header flex-layout">
        <div class="title wordcaps">
            Add&nbsp;<b>Practitioner</b>
        </div>

    </div>

    <div class="oe-full-content oe-new-patient flex-layout flex-top">
        <div class="patient-content">
            <?php $this->renderPartial('_form', array('model' => $model, 'gp' => $gp, 'context' => $context)); ?>
        </div>
    </div>

</div>
