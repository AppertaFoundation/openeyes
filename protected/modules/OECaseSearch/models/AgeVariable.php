<?php

/**
 * Class AgeVariable
 */
class AgeVariable extends CaseSearchVariable implements DBProviderInterface
{
    public function __construct(?array $id_list)
    {
        parent::__construct($id_list);
        $this->field_name = 'age';
        $this->label = 'Age';
        $this->x_label = 'Age (y)';
    }

    public function query(): string
    {
        if ($this->csv_mode === 'ADVANCED') {
            return 'SELECT (
            SELECT pi.value
            FROM patient_identifier pi
                JOIN patient_identifier_type pit ON pit.id = pi.patient_identifier_type_id
            WHERE pi.patient_id = p.id
            AND pit.usage_type = \'GLOBAL\'
            ) nhs_num, TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) age, p.created_date, null
        FROM patient p
        JOIN contact c ON c.id = p.contact_id
        WHERE p.id IN (' . implode(', ', $this->id_list) . ')
        AND (:start_date IS NULL OR p.created_date > :start_date)
        AND (:end_date IS NULL OR p.created_date < :end_date)
        ORDER BY 1, 2, 3';
        }

        if ($this->csv_mode === 'BASIC') {
            return 'SELECT (10*FLOOR(TIMESTAMPDIFF(YEAR, p.dob, IFNULL(p.date_of_death, CURDATE()))/10)) age, COUNT(*) frequency
        FROM patient p
        WHERE p.id IN (' . implode(', ', $this->id_list) . ')
        AND (:start_date IS NULL OR p.created_date > :start_date)
        AND (:end_date IS NULL OR p.created_date < :end_date)
        GROUP BY FLOOR(TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE()))/10)
        ORDER BY 1';
        }

        return 'SELECT (10*FLOOR(TIMESTAMPDIFF(YEAR, p.dob, IFNULL(p.date_of_death, CURDATE()))/10)) age, COUNT(*) frequency, GROUP_CONCAT(DISTINCT id) patient_id_list
    FROM patient p
    WHERE p.id IN (' . implode(', ', $this->id_list) . ')
    AND (:start_date IS NULL OR p.created_date > :start_date)
    AND (:end_date IS NULL OR p.created_date < :end_date)
    GROUP BY FLOOR(TIMESTAMPDIFF(YEAR, p.dob, IFNULL(p.date_of_death, CURDATE()))/10)
    ORDER BY 1';
    }

    public function bindValues(): array
    {
        return array();
    }
}
