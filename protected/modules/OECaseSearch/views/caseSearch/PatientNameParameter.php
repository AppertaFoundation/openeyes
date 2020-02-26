<?php
echo CHtml::activeTextField($model, "[$id]patient_name", array('class' => 'search cols-full', 'placeholder' => 'Patient name'));
echo CHtml::error($model, "[$id]patient_name");
