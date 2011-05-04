<?php

foreach ($subsections as $section) {
	echo CHtml::tag('option', 
		array('value'=>$section->id), 
		CHtml::encode($section->name), true);
}