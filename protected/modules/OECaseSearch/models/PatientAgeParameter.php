<?php

/**
 * Class PatientAgeParameter
 */
class PatientAgeParameter extends CaseSearchParameter implements DBProviderInterface
{
    /**
     * @var integer $minValue Represents a minimum value.
     */
    public $minValue;

    /**
     * @var integer $maxValue Represents a maximum value.
     */
    public $maxValue;

    protected $options = array(
        'value_type' => 'number',
    );

    /**
     * PatientAgeParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario Model scenario.
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'age';

        $this->options['operations'][] = array('label' => 'IS LESS THAN', 'id' => '<');
        $this->options['operations'][] = array('label' => 'IS MORE THAN', 'id' => '>');
    }

    /**
     * This has been overridden to allow additional rules surrounding the operator and value fields.
     * @return array Complete array of validation rules.
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('minValue, maxValue', 'numerical', 'min' => 0),
        ));
    }

    /**
     * This has been overridden to add additional attributes.
     * @return array Complete array of attribute names.
     */
    public function attributeNames()
    {
        return array_merge(parent::attributeNames(), array(
            'minValue',
            'maxValue',
        ));
    }

    /**
     * Attribute labels for display purposes.
     * @return array Attribute key/value pairs.
     */
    public function attributeLabels()
    {
        return array(
            'minValue' => 'Minimum Value',
            'maxValue' => 'Maximum Value',
            'id' => 'ID',
        );
    }

    /**
     * @return string "Patient age".
     */
    public function getLabel()
    {
        return 'Patient Age';
    }

    /**
     * Generate the SQL query for patient age.
     * @param $searchProvider DBProvider The search provider building the query.
     * @return null|string The query string for use by the search provider, or null if not implemented for the specified search provider.
     * @throws CHttpException
     */
    public function query($searchProvider)
    {
        if ($this->minValue && !$this->maxValue) {
            $this->operation = '>=';
        } elseif ($this->maxValue && !$this->minValue) {
            $this->operation = '<=';
        } elseif ($this->maxValue && $this->minValue) {
            $this->operation = 'BETWEEN';
        } else {
            throw new CHttpException(400, 'Please specify either a minimum or maximum value');
        }

        $op = $this->operation;

        $queryStr = 'SELECT id FROM patient WHERE TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE()))';
        if ($op === 'BETWEEN') {
            return "$queryStr BETWEEN :p_a_min_$this->id AND :p_a_max_$this->id";
        }

        return "$queryStr $op :p_a_value_$this->id";
    }

    /**
     * @return array The list of bind values being used by the current parameter instance.
     */
    public function bindValues()
    {
        $bindValues = array();

        if ($this->operation === 'BETWEEN') {
            $bindValues["p_a_min_$this->id"] = (int)$this->minValue;
            $bindValues["p_a_max_$this->id"] = (int)$this->maxValue;
        } elseif ($this->operation === '<=') {
            $bindValues["p_a_value_$this->id"] = (int)$this->maxValue;
        } elseif ($this->operation === '>=') {
            $bindValues["p_a_value_$this->id"] = (int)$this->minValue;
        }

        return $bindValues;
    }

    /**
     * @inherit
     */
    public function getAuditData()
    {
        if ($this->operation === 'BETWEEN') {
            return "$this->name: BETWEEN $this->minValue and $this->maxValue";
        }

        if ($this->operation === '<=') {
            return "$this->name: $this->operation $this->maxValue";
        }

        return "$this->name: $this->operation $this->minValue";
    }

    public function saveSearch()
    {
        return array_merge(
            parent::saveSearch(),
            array(
                'minValue' => $this->minValue,
                'maxValue' => $this->maxValue,
            )
        );
    }

    public function getDisplayString()
    {
        if ($this->minValue && !$this->maxValue) {
            $this->operation = '>=';
        } elseif ($this->maxValue && !$this->minValue) {
            $this->operation = '<=';
        } elseif ($this->maxValue && $this->minValue) {
            $this->operation = 'BETWEEN';
        }

        if ($this->operation === 'BETWEEN') {
            return "Age IS between $this->minValue and $this->maxValue";
        }

        if ($this->operation === '<=') {
            return "Age IS $this->operation $this->maxValue";
        }

        return "Age IS $this->operation $this->minValue";
    }
}
