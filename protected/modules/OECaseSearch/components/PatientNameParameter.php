<?php

/**
 * Class PatientNameParameter
 */
class PatientNameParameter extends CaseSearchParameter implements DBProviderInterface
{
    public $patient_name;

    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'patient_name';
        $this->operation = 'LIKE'; // Remove if more operations are added.
    }

    public function getLabel()
    {
        // This is a human-readable value, so feel free to change this as required.
        return 'Patient Name';
    }

    /**
     * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
     * @return array An array of attribute names.
     */
    public function attributeNames()
    {
        return array_merge(parent::attributeNames(), array(
                'patient_name',
            )
        );
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'patient_name' => 'Patient Name',
        ));
    }

    /**
     * Override this function if the parameter subclass has extra validation rules. If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
     * @return array The validation rules for the parameter.
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('patient_name', 'required'),
        ));
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @param $searchProvider DBProvider The search provider. This is used to determine whether or not the search provider is using SQL syntax.
     * @return string The constructed query string.
     * @throws CHttpException
     */
    public function query($searchProvider)
    {
        $op = 'LIKE';

        return "SELECT DISTINCT p.id 
FROM patient p 
JOIN contact c 
  ON c.id = p.contact_id
WHERE (LOWER(CONCAT(c.first_name, ' ', c.last_name)) $op LOWER(:p_n_name_like_$this->id)) OR (LOWER(CONCAT(c.last_name, ' ', c.first_name)) $op LOWER(:p_n_name_like_$this->id)) OR
     SOUNDEX(c.first_name) = SOUNDEX(:p_n_name_$this->id)
      OR SOUNDEX(c.last_name) = SOUNDEX(:p_n_name_$this->id)
";
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        return array(
            "p_n_name_like_$this->id" => '%' . $this->patient_name . '%',
            "p_n_name_$this->id" => $this->patient_name,
        );
    }

    /**
     * @inherit
     */
    public function getAuditData()
    {
        return "$this->name: $this->operation \"$this->patient_name\"";
    }
}
