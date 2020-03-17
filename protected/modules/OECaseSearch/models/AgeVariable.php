<?php

/**
 * Class AgeVariable
 */
class AgeVariable extends CaseSearchVariable implements DBProviderInterface
{
    public function __construct($id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'age';
        $this->label = 'Age';
        $this->unit = 'y';
    }

    public function query($searchProvider)
    {
        return 'SELECT TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) age, COUNT(*) frequency
        FROM patient p
        WHERE id IN (' . implode(', ', $this->id_list) . ')
        GROUP BY TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE()))';
    }

    public function bindValues()
    {
        return array();
    }
}