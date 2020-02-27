<?php
echo CHtml::activeTextField($model, "[$id]number", array('class' => 'search cols-full', 'placeholder' => $model->getLabel()));
echo CHtml::error($model, "[$id]number");
