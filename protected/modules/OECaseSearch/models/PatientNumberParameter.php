<?php

/**
 * Class PatientNumberParameter
 */
class PatientNumberParameter extends CaseSearchParameter implements DBProviderInterface
{
    protected $options = array(
        'value_type' => 'string_search',
    );

    protected $label_ = null;

    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'patient_number';
        $this->label_ = Yii::app()->params['hos_num_label'] ?? 'Patient Number';
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
                    return Patient::model()->findByPk($this->$attribute)->hos_num;
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
        if ($this->operation !== '=' && $this->operation !== '!=') {
            throw new CHttpException(400, "Invalid value specified: $this->operation");
        }

        $op = $this->operation;

        return "SELECT DISTINCT p.id 
FROM patient p
WHERE p.id $op :p_num_number_{$this->id}";
    }

    public static function getCommonItemsForTerm($term)
    {
        $patients = Patient::model()->findAllBySql(
            "SELECT p.* FROM patient p
WHERE p.hos_num LIKE :term
ORDER BY p.hos_num LIMIT " . self::_AUTOCOMPLETE_LIMIT,
            array('term' => "$term%")
        );
        $values = array();
        foreach ($patients as $patient) {
            $values[] = array('id' => $patient->id, 'label' => $patient->hos_num);
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
            "p_num_number_$this->id" => $this->value,
        );
    }

    /**
     * @inherit
     */
    public function getAuditData()
    {
        $patient = Patient::model()->findByPk($this->value);
        return "$this->name: = $patient->hos_num";
    }
}
