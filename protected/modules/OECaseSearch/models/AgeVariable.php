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
        if ($this->csv_mode === 'BASIC') {
            return 'SELECT TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) age
        FROM patient p
        JOIN contact c ON c.id = p.contact_id
        WHERE p.id = p_outer.id
            AND (:start_date IS NULL OR p.created_date > :start_date)
        AND (:end_date IS NULL OR p.created_date < :end_date)';
        } elseif ($this->csv_mode === 'ADVANCED') {
            return 'SELECT TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) age
        FROM patient p
        WHERE p.id = p_outer.id
            AND (:start_date IS NULL OR p.created_date > :start_date)
        AND (:end_date IS NULL OR p.created_date < :end_date)
        GROUP BY TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE()))';
        } else {
            return 'SELECT TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) age, COUNT(*) frequency, GROUP_CONCAT(DISTINCT id) patient_id_list
        FROM patient p
        WHERE p.id IN (' . implode(', ', $this->id_list) . ')
            AND (:start_date IS NULL OR p.created_date > :start_date)
        AND (:end_date IS NULL OR p.created_date < :end_date)
        GROUP BY TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE()))';
        }
    }

    public function bindValues()
    {
        return array();
    }
}