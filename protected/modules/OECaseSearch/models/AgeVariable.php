<?php


class AgeVariable extends CaseSearchVariable implements DBProviderInterface
{
    public $field_name = 'age';
    public $label = 'Age';

    public function query($searchProvider)
    {
        return 'SELECT TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) age, COUNT(*) frequency
        FROM patient p
        WHERE id IN (' . implode(', ', $this->id_list) . ')
        GROUP BY TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE()))';
    }

    public function bindValues()
    {
    }
}