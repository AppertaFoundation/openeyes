<?php

/**
 * Class PatientMedicationParameter
 */
class PatientMedicationParameter extends CaseSearchParameter implements DBProviderInterface
{
    protected $label_ = 'Medication';

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

    public function rules()
    {
        return array_merge(
            parent::rules(),
            array(
                array('value', 'required'),
                array('value', 'safe'),
            )
        );
    }

    public static function getCommonItemsForTerm($term)
    {
        $drugs = Medication::model()->findAllBySql('
SELECT *
FROM medication d 
WHERE LOWER(d.preferred_term) LIKE LOWER(:term) ORDER BY d.preferred_term LIMIT 30', array('term' => "$term%"));

        $values = array();
        foreach ($drugs as $drug) {
            $values[] = array('id' => $drug->id, 'label' => $drug->preferred_term);
        }
        return $values;
    }

    public function getValueForAttribute($attribute)
    {
        if (in_array($attribute, $this->attributeNames(), true)) {
            switch ($attribute) {
                case 'value':
                    return Medication::model()->findByPk($this->$attribute)->preferred_term;
                    break;
                default:
                    return parent::getValueForAttribute($attribute);
            }
        }
        return parent::getValueForAttribute($attribute);
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @return string The constructed query string.
     */
    public function query()
    {
        if ($this->operation === '=') {
            $op = '=';

            return "
SELECT p.id
FROM patient p
JOIN patient_medication_assignment m
  ON m.patient_id = p.id
LEFT JOIN medication d
  ON d.id = m.medication_drug_id
WHERE d.id $op :p_m_value_$this->id";
        }

        $op = '!=';

        return "
SELECT p.id
FROM patient p
LEFT JOIN patient_medication_assignment m
ON m.patient_id = p.id
LEFT JOIN medication md
ON md.id = m.medication_drug_id
WHERE md.id $op :p_m_value_$this->id
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
        if ($this->operation !== '=') {
            $op = '!=';
        }
        return "$this->name: $op \"$this->value\"";
    }
}
