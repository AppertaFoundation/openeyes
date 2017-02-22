<div class="element-data">
    <div class="row data-row">
        <div class="large-6 column">
            <div
                class="field-info"><b><?php echo CHtml::encode($element->getAttributeLabel('comments_'.$side)) ?></b>:</div>
        </div>
        <div class="large-6 column end">
            <div class="field-info<?php
            if ($element->{'comments_'.$side}){
                ?> iolDisplay<?php
            }
?>"><?php echo CHtml::encode($element->{'comments_'.$side}) ?></div>
        </div>
    </div>
</div>
<div class="element-data">
    <div class="row data-row">
        <div class="large-6 column">
            <div
                class="field-info"><b><?php echo CHtml::encode($element->getAttributeLabel('target_refraction_'.$side)) ?></b>:</div>
            </div>
        <div class="large-6 column end">
            <div class="field-info"><?php echo CHtml::encode($element->{'target_refraction_'.$side}) ?></div>
        </div>
    </div>
</div>