<?php

/**
 * Class PatientMedicationParameter
 */
class PatientMedicationParameter extends CaseSearchParameter implements DBProviderInterface
{
    protected string $label_ = 'Medication';

    protected array $options = array(
        'value_type' => 'string_search',
        'operations' => array(
            array('label' => 'IS', 'id' => '='),
            array('label' => 'IS NOT', 'id' => '!='),
        )
    );

    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'medication';
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

    public static function getCommonItemsForTerm(string $term) : array
    {
        $institution_id = Institution::model()->getCurrent()->id;

        $drugs = Medication::model()->findAllBySql("
SELECT *
FROM (SELECT m.* FROM medication m LEFT OUTER JOIN medication_institution mi ON mi.medication_id = m.id WHERE m.source_type != 'LOCAL' OR mi.institution_id = :institution_id) d
WHERE LOWER(d.preferred_term) LIKE LOWER(:term) ORDER BY d.preferred_term LIMIT 30", array('term' => "$term%", 'institution_id' => $institution_id));

        return array_map(
            static function ($drug) {
                return array('id' => $drug->id, 'label' => $drug->preferred_term);
            },
            $drugs
        );
    }

    /**
     * @param string $attribute
     * @return mixed|void
     * @throws CException
     */
    public function getValueForAttribute(string $attribute)
    {
        if (in_array($attribute, $this->attributeNames(), true)) {
            if ($attribute === 'value') {
                return Medication::model()->findByPk($this->$attribute)->preferred_term;
            }
            return parent::getValueForAttribute($attribute);
        }
        return parent::getValueForAttribute($attribute);
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @return string The constructed query string.
     */
    public function query(): string
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
LEFT JOIN medication d
ON d.id = m.medication_drug_id
WHERE NOT EXISTS (
    SELECT md.id
    FROM patient_medication_assignment pm
    JOIN medication md ON md.id = pm.medication_drug_id
    WHERE pm.patient_id = p.id AND md.id = :p_m_value_$this->id
) OR m.id IS NULL";
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues(): array
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        return array(
            "p_m_value_$this->id" => $this->value,
        );
    }

    /**
     * @inherit
     */
    public function getAuditData() : string
    {
        $op = '=';
        if ($this->operation !== '=') {
            $op = '!=';
        }
        return "$this->name: $op \"$this->value\"";
    }
}
