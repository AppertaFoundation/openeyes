<?php
$ageParam = new PatientAgeParameter();
$ageParam->id = 1;
$ageParam->name = 'age';
$ageParam->operation = '>';
$ageParam->value = 50;
    return array(
        'saved_search1' => array(
            'search_criteria' => serialize(array($ageParam)),
            'variables' => 'age',
            'name' => 'Search 1',
        ),
    );
