<?php

/**
 * Class PatientNumberParameter
 */
class PatientNumberParameter extends CaseSearchParameter implements DBProviderInterface
{
    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'patient_number';
        $this->operation = '='; // Remove if more operations are added.
    }

    public function getLabel()
    {
        // This is a human-readable value, so feel free to change this as required.
        return Yii::app()->params['hos_num_label'];
    }

    /**
     * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
     * @return array An array of attribute names.
     */
    public function attributeNames()
    {
        return array_merge(
            parent::attributeNames(),
            array(
                'number',
            )
        );
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'number' => 'Value',
        ));
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @param $searchProvider DBProvider The database search provider.
     * @return string The constructed query string.
     */
    public function query($searchProvider)
    {
        $op = '=';

        return "SELECT DISTINCT p.id 
FROM patient p
WHERE p.hos_num $op :p_num_number_$this->id";
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
        return "$this->name: = $this->value";
    }

    public function getDisplayString()
    {
        $op = 'IS';
        if ($this->operation) {
            $op = 'IS NOT';
        }

        return "Number $op = $this->value";
    }
}
