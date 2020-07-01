<?php

/**
 * Class PatientDeceasedParameter
 */
class PatientDeceasedParameter extends CaseSearchParameter implements DBProviderInterface
{
    protected $options = array(
        'value_type' => 'boolean',
    );

    protected $label_ = 'Patient Deceased';

    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'patient_deceased';
        $this->operation = false;

        // Override the existing operation IDs to use boolean values.
        $this->options['operations'][0]['id'] = '1';
        $this->options['operations'][1]['id'] = '0';
    }

    /**
     * Override this function if the parameter subclass has extra validation rules. If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
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
    public function query()
    {
        switch ($this->operation) {
            case '0':
                return 'SELECT id FROM patient WHERE NOT(is_deceased)';
                break;
            case '1':
                return 'SELECT id FROM patient WHERE is_deceased';
                break;
            default:
                throw new CHttpException(400, "Invalid value specified: $this->operation");
                break;
        }
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        // No binds are used in this query, so return an empty array.
        return array();
    }

    /**
     * @inherit
     */
    public function getAuditData()
    {
        $value = $this->operation === false ? 'False' : 'True';
        return "$this->name: $value";
    }
}
