<?php

/**
 * Class PatientAllergyParameter
 */
class PatientAllergyParameter extends CaseSearchParameter implements DBProviderInterface
{
    protected string $label_ = 'Allergy';

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
        $this->name = 'allergy';
        $this->operation = '=';
    }

    public function getValueForAttribute(string $attribute)
    {
        if (in_array($attribute, $this->attributeNames(), true)) {
            if ($attribute === 'value') {
                return Allergy::model()->findByPk($this->$attribute)->name;
            }
            return parent::getValueForAttribute($attribute);
        }
        return parent::getValueForAttribute($attribute);
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
        $allergies = Allergy::model()->findAllBySql('
SELECT a.*
FROM allergy a 
WHERE LOWER(a.name) LIKE LOWER(:term) ORDER BY a.name LIMIT  ' . self::_AUTOCOMPLETE_LIMIT, array('term' => "$term%"));
        return array_map(
            static function ($allergy) {
                return array('id' => $allergy->id, 'label' => $allergy->name);
            },
            $allergies
        );
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @return string The constructed query string.
     */
    public function query(): string
    {
        $query = "SELECT DISTINCT p.id 
FROM patient p 
LEFT JOIN patient_allergy_assignment paa
  ON paa.patient_id = p.id
LEFT JOIN allergy a
  ON a.id = paa.allergy_id
WHERE a.id = :p_al_textValue_$this->id";
        if ($this->operation === '=') {
            return $query;
        }

        return "SELECT DISTINCT p1.id
FROM patient p1
WHERE p1.id NOT IN (
$query
)";
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues(): array
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        return array(
            "p_al_textValue_$this->id" => $this->value,
        );
    }

    /**
     * @inherit
     */
    public function getAuditData() : string
    {
        $allergy = Allergy::model()->findByPk($this->value);
        if ($this->operation === '=') {
            $op = '=';
        } else {
            $op = '!=';
        }
        $name = $allergy->name ?? 'Unknown';
        return "$this->name: $op \"{$name}\"";
    }
}
