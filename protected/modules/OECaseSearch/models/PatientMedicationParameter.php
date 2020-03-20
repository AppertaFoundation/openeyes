<?php

/**
 * Class PatientMedicationParameter
 */
class PatientMedicationParameter extends CaseSearchParameter implements DBProviderInterface
{
    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'medication';
        $this->operation = 'LIKE';
    }

    public function getLabel()
    {
        return 'Medication';
    }

    public static function getCommonItemsForTerm($term)
    {
        $drugs = Drug::model()->findAllBySql('
SELECT *
FROM drug d 
WHERE LOWER(d.name) LIKE LOWER(:term) ORDER BY d.name LIMIT 30', array('term' => "$term%"));

        $medicationDrugs = MedicationDrug::model()->findAllBySql('
SELECT *
FROM medication_drug md
WHERE LOWER(md.name) LIKE LOWER(:term) ORDER BY md.name LIMIT ' . self::_AUTOCOMPLETE_LIMIT, array('term' => "$term%"));

        $values = array();
        foreach ($drugs as $drug) {
            $values[] = array('id' => $drug->id, 'label' => $drug->name);
        }

        foreach ($medicationDrugs as $medicationDrug) {
            // Filter out any duplicates.
            if (!isset($values[$medicationDrug->name])) {
                $values[] = array('id' => $medicationDrug->id, 'label' => $medicationDrug->name);
            }
        }

        sort($values);
        return $values;
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @param $searchProvider DBProvider The search provider. This is used to determine whether or not the search provider is using SQL syntax.
     * @return string The constructed query string.
     */
    public function query($searchProvider)
    {
        if ($this->operation === '=') {
            $op = '=';

            return "
SELECT p.id
FROM patient p
JOIN patient_medication_assignment m
  ON m.patient_id = p.id
LEFT JOIN drug d
  ON d.id = m.drug_id
LEFT JOIN medication_drug md
  ON md.id = m.medication_drug_id
WHERE d.id $op :p_m_value_$this->id
  OR md.id $op :p_m_value_$this->id";
        }

        $op = '!=';

        return "
SELECT p.id
FROM patient p
LEFT JOIN patient_medication_assignment m
ON m.patient_id = p.id
LEFT JOIN drug d
ON d.id = m.drug_id
LEFT JOIN medication_drug md
ON md.id = m.medication_drug_id
WHERE d.id $op :p_m_value_$this->id
OR md.id $op :p_m_value_$this->id
OR m.id IS NULL";
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        return array(
            "p_m_value_$this->id" => $this->value,
        );
    }

    /**
     * @inherit
     */
    public function getAuditData()
    {
        $op = '=';
        if ($this->operation) {
            $op = '!=';
        }
        return "$this->name: $op \"$this->value\"";
    }
}
