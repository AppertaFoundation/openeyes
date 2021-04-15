<?php

/**
 * Class PatientDeceasedParameter
 */

class PatientDeceasedParameter extends CaseSearchParameter implements DBProviderInterface
{
    protected array $options = array(
        'value_type' => 'boolean',
        'operations' => array(
            array('label' => 'IS', 'id' => '1'),
            array('label' => 'IS NOT', 'id' => '0'),
        ),
    );

    protected string $label_ = 'Patient Deceased';

    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'patient_deceased';
        $this->operation = false;
    }

    /**
     * @return array The validation rules for the parameter.
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('operation', 'boolean'),
        ));
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @return string The constructed query string.
     * @throws CHttpException
     */
    public function query() : string
    {
        switch ($this->operation) {
            case '0':
                return 'SELECT id FROM patient WHERE NOT(is_deceased)';
            case '1':
                return 'SELECT id FROM patient WHERE is_deceased';
            default:
                throw new CHttpException(400, "Invalid value specified: $this->operation");
        }
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues() : array
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        // No binds are used in this query, so return an empty array.
        return array();
    }

    /**
     * @inherit
     */
    public function getAuditData() : string
    {
        $value = $this->operation === false ? 'False' : 'True';
        return "$this->name: $value";
    }
}
