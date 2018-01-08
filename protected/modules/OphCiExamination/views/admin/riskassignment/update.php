<?php
/* @var $this OphCiExaminationRiskController */
/* @var $model OphCiExaminationRisk */
?>

<div class="box admin">
    <h2>Edit required risk set</h2>
    <?php
        $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => 'OphCiExamination_adminform',
            'enableAjaxValidation' => false,
            'layoutColumns' => array(
                'label' => 2,
                'field' => 5,
            ),
        ));

    $this->renderPartial('/admin/riskassignment/_form', array('form' => $form, 'model'=>$model)); ?>

    <button class="small primary event-action" name="add" type="submit" id="et_save">Save</button>
    <button class="small warning event-action" name="add" type="button" id="et_cancel">Cancel</button>

    <?php
    $this->endWidget();
    ?>
</div>

<script>
    $(document).ready(function(){
        $('#et_cancel').click(function(){
            window.location.href = '/OphCiExamination/oeadmin/RisksAssignment/';
        });
    });
</script>