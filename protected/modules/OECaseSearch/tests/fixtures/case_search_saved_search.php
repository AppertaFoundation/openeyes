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
            'institution_id' => 1,
            'name' => 'Search 1',
        ),
        'saved_search2' => array(
            'search_criteria' => serialize(array($ageParam)),
            'variables' => 'age',
            'institution_id' => 1,
            'name' => 'Search 1',
            'created_user_id' => 2,
        ),
        'saved_search3' => array(
            'search_criteria' => serialize(array($ageParam)),
            'variables' => 'age',
            'institution_id' => 2,
            'name' => 'Search 1',
            'created_user_id' => 1,
        ),
        'saved_search4' => array(
            'search_criteria' => serialize(array($ageParam)),
            'variables' => 'age',
            'institution_id' => 2,
            'name' => 'Search 1',
            'created_user_id' => 2,
        ),
    );
