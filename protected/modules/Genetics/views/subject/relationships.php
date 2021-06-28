<?php
$assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $this->getModule()->name . '.assets'));
Yii::app()->clientScript->registerScriptFile($assetPath . '/js/OpenEyes.Genetics.Relationships.js');
Yii::app()->clientScript->registerScriptFile($assetPath . '/js/relationships.js');
?>
<div class="data-group  flex-layout cols-full">
    <div class="cols-7 column">
        <label for="genetics_patient_lookup">Relationships:</label>
    </div>
    <div class="cols-4 column">
        <input type="text" name="genetics_patient_lookup" id="genetics_patient_lookup" placeholder="Search for a related patient">
        <div style="display:none" class="no-result-patients warning alert-box">
          <div class="small-12 column text-center">
            No results found.
          </div>
        </div>
        <ul id="relationships_list">
            <?php if ($model->relationships) :?>
                <?php foreach ($model->relationships as $relationship) :?>
                    <li>
                        <span class="genetics_relationship_remove">
                            <i class="oe-i remove-circle small" title="Remove Relationship"></i>
                        </span>
                        <input type="hidden" name="GeneticsPatient[relationships][<?=$relationship->related_to_id?>][related_to_id]" value="<?=$relationship->related_to_id?>">
                        <input type="hidden" name="GeneticsPatient[relationships][<?=$relationship->related_to_id?>][relationship_id]" value="<?=$relationship->relationship_id?>">
                        <?= $relationship->relation->patient->fullName ?> is a  <?=$relationship->relationship->relationship?> to the subject.
                    </li>
                <?php endforeach?>
            <?php endif;?>
        </ul>
    </div>
    <div class="cols-1 column end">
        <img class="loader-relation" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
    </div>
</div>
