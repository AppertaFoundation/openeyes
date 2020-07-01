<?php

/**
 * Class PatientNameParameter
 */
class PatientNameParameter extends CaseSearchParameter implements DBProviderInterface
{
    protected $options = array(
        'value_type' => 'string_search',
    );

    protected $label_ = 'Name';

    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'patient_name';
        $this->operation = '=';
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

    public function getValueForAttribute($attribute)
    {
        if (in_array($attribute, $this->attributeNames(), true)) {
            switch ($attribute) {
                case 'value':
                    return Patient::model()->findByPk($this->$attribute)->getFullName();
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
        return "SELECT DISTINCT p.id 
FROM patient p 
WHERE p.id = :p_n_name_{$this->id}
";
    }

    public static function getCommonItemsForTerm($term)
    {
        $patients = Patient::model()->findAllBySql(
            "SELECT p.* FROM patient p
JOIN contact c ON c.id = p.contact_id
WHERE (LOWER(CONCAT(c.first_name, ' ', c.last_name)) LIKE LOWER(:term)) OR (LOWER(CONCAT(c.last_name, ' ', c.first_name)) LIKE LOWER(:term)) OR
     SOUNDEX(c.first_name) = SOUNDEX(:term)
      OR SOUNDEX(c.last_name) = SOUNDEX(:term)
ORDER BY c.first_name, c.last_name LIMIT " . self::_AUTOCOMPLETE_LIMIT,
            array('term' => "%$term%")
        );
        $values = array();
        foreach ($patients as $patient) {
            $values[] = array('id' => $patient->id, 'label' => $patient->getFullName());
        }
        return $values;
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        return array(
            "p_n_name_$this->id" => $this->value,
        );
    }

    /**
     * @inherit
     */
    public function getAuditData()
    {
        $patient = Patient::model()->findByPk($this->value);
        return "$this->name: = \"{$patient->getFullName()}\"";
    }
}
