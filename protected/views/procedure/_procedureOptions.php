<?php

foreach ($procedures as $procedure) {
	echo CHtml::tag('option', 
		array('value'=>$procedure->id), 
		CHtml::encode($procedure->term . ' - ' . $procedure->short_format), true);
}