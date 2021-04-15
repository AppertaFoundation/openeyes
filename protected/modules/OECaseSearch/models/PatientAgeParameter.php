<?php

/**
 * Class PatientAgeParameter
 */
class PatientAgeParameter extends CaseSearchParameter implements DBProviderInterface
{
    protected array $options = array(
        'value_type' => 'number',
        'operations' => array(
            array('label' => 'IS', 'id' => '='),
            array('label' => 'IS NOT', 'id' => '!='),
            array('label' => 'IS LESS THAN', 'id' => '<'),
            array('label' => 'IS MORE THAN', 'id' => '>')
        )
    );

    protected string $label_ = 'Age';

    /**
     * PatientAgeParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario Model scenario.
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'age';
    }

    public function rules()
    {
        return array_merge(
            parent::rules(),
            array(
                array('value', 'numerical', 'allowEmpty' => false, 'min' => 0),
                array('value', 'safe'),
            )
        );
    }

    /**
     * Generate the SQL query for patient age.
     * @return null|string The query string for use by the search provider,
     * or null if not implemented for the specified search provider.
     */
    public function query(): string
    {
        $op = $this->operation;

        $queryStr = 'SELECT id FROM patient WHERE TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE()))';

        return "$queryStr $op :p_a_value_$this->id";
    }

    /**
     * @return array The list of bind values being used by the current parameter instance.
     */
    public function bindValues(): array
    {
        return array(
            "p_a_value_$this->id" => (int)$this->value
        );
    }

    /**
     * @inherit
     */
    public function getAuditData() : string
    {
        return "$this->name: $this->operation $this->value";
    }
}
