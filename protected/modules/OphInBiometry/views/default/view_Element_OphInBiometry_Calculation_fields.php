<div class="element-data">
    <div class="data-group">
        <div class="cols-6 column">
            <div
                class="field-info"><b><?=\CHtml::encode($element->getAttributeLabel('comments_'.$side)) ?></b>:</div>
        </div>
        <div class="cols-6 column end">
            <div class="field-info<?php
            if ($element->{'comments_'.$side}){
                ?> iolDisplay<?php
            }
?>"><?=\CHtml::encode($element->{'comments_'.$side}) ?></div>
        </div>
    </div>
</div>
<div class="element-data">
    <div class="data-group">
        <div class="cols-6 column">
            <div
                class="field-info"><b><?=\CHtml::encode($element->getAttributeLabel('target_refraction_'.$side)) ?></b>:</div>
            </div>
        <div class="cols-6 column end">
            <div class="field-info"><?=\CHtml::encode($element->{'target_refraction_'.$side}) ?></div>
        </div>
    </div>
</div>