<?php
/* @var $this OphCiExaminationRiskController */
/* @var $model OphCiExaminationRisk */
/* @var $form CActiveForm */
?>

<?php
    echo $form->textField($model, "name");
    echo "<br>";

    $options = CHtml::listData(\Subspecialty::model()->findAll(), 'id', 'name');
    echo $form->dropDownList($model, "subspecialty_id", $options, array('empty' => '-- select --'));
    $options = CHtml::listData(\Firm::model()->findAll(), 'id', 'name');
    echo $form->dropDownList($model, "firm_id", $options, array('empty' => '-- select --'));
    //$options = CHtml::listData(\EpisodeStatus::model()->findAll(), 'id', 'name');
    //echo $form->dropDownList($model, "episode_status_id", $options, array('empty' => '-- select --'));
    echo "<br>";


    ?>
    <div id="div_OEModule_OphCiExamination_models_OphCiExaminationRisk_episode_status_id" class="row field-row">

        <div class="large-2 column">
            <label for="OEModule_OphCiExamination_models_OphCiExaminationRisk_gender">Gender:</label>
        </div>

        <div class="large-5 column end">
            <?php
            $gender_models = Gender::model()->findAll();
            $options = $genders = CHtml::listData($gender_models, function ($gender_model) {
                return CHtml::encode($gender_model->name)[0];
            }, 'name');

            echo CHtml::dropDownList("OEModule_OphCiExamination_models_OphCiExaminationRisk[gender]", $model->gender, $options, array('empty' => '-- select --'));

            ?>
        </div>
    </div>
    <br>

    <div id="div_OEModule_OphCiExamination_models_OphCiExaminationRisk_age_min" class="row field-row">

        <div class="large-2 column">
            <label for="OEModule_OphCiExamination_models_OphCiExaminationRisk_age_min">Age range:</label>
        </div>

      <div class="large-5 column end" style="font-size: 0.8125rem;">
          Min: <?=$form->numberField($model, 'age_min', array('size' => 3, 'style' => 'display:inline-block;width:55px;margin-right:10px;', 'nowrapper' => true, 'type' => 'number', 'min' => 'null'));?>
          Max: <?=$form->numberField($model, 'age_max', array('size' => 3, 'style' => 'display:inline-block;width:55px;', 'nowrapper' => true, 'type' => 'number', 'min' => 'null'));?>
      </div>
    </div>