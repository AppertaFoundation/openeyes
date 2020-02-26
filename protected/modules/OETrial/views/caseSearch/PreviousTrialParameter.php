<?php
$ops = array(
    'IS' => 'Is',
    'IS NOT' => 'Is not',
);

$trials = Trial::getTrialList(isset($model->trialType) ? $model->trialType->id : '');

$statusList = array(
    TrialPatientStatus::model()->find('code = "SHORTLISTED"')->id => 'Shortlisted in',
    TrialPatientStatus::model()->find('code = "ACCEPTED"')->id => 'Accepted in',
    TrialPatientStatus::model()->find('code = "REJECTED"')->id => 'Rejected from',
);

?>
<p>Previous Trials</p>
<div class="flex-layout">
    <?= CHtml::activeDropDownList(
        $model,
        "[$id]operation",
        $ops,
        array('class' => 'cols-3')
    ) ?>
    <?= CHtml::activeDropDownList(
        $model,
        "[$id]status",
        $statusList,
        array('empty' => 'Involved with', 'class' => 'cols-4')
    ) ?>
    <?= CHtml::activeDropDownList(
        $model,
        "[$id]trialTypeId",
        TrialType::getOptions(),
        array('empty' => 'Any Trial', 'onchange' => 'getTrialList(this)', 'class' => 'cols-4')
    ) ?>
</div>
<div class="flex-layout">
    <?php echo CHtml::activeDropDownList($model, "[$id]trial", $trials, array('empty' => 'Any', 'class' => 'cols-5')); ?>
    with
    <?php echo CHtml::activeDropDownList($model, "[$id]treatmentTypeId", TreatmentType::getOptions(), array(
        'empty' => 'Any',
        'style' => $model->trialType && $model->trialType->code = TrialType::NON_INTERVENTION_CODE ? 'display:none;':'',
        'class' => 'cols-3',
    )); ?>
    treatment
</div>

<script type="text/javascript">

    let DOMStrings = {
        parameterClass: '.parameter',
        trialType: '.js-trial-type',
        trialList: '.js-trial-list select',
        treatmentType: '.js-treatment-type-container'
    };

    function getDOM() {
        return DOMStrings;
    }

    // populateTrialList argument receives a boolean to specify whether we need to get the trial list or not.
    function init(target, populateTrialList = true) {
        let DOM = getDOM();
        var parameterNode = $(DOM.parameterClass + '#' + <?= $model->id ?>);

        var trialType = $(target).val();
        var trialList = parameterNode.find(DOM.trialList);
        var treatmentTypeContainer = parameterNode.find(DOM.treatmentType);

        // If user has selected Any Trial as Trial type then hide the trial list
        treatmentTypeContainer.toggle(
            !trialType ||
            trialType === '<?= TrialType::model()->find('code = "INTERVENTION"')->id ?>'
        );

        if (!trialType) {
            trialList.empty();
            trialList.hide();
        } else {
            if(populateTrialList) {
                $.ajax({
                    url: '<?php echo Yii::app()->createUrl('/OETrial/trial/getTrialList'); ?>',
                    type: 'GET',
                    data: {type: trialType},
                    success: function (response) {
                        trialList.empty();
                        trialList.append(response);
                        trialList.show();
                    }
                });
            }
        }
    }

    // Execute the function on loading the page.
    jQuery(document).ready(function(){
        var DOM = getDOM();
        init($(DOM.trialType).children(), false);
    });

    function getTrialList(target) {
        init(target);
    }

</script>