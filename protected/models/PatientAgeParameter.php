<?php

/**
 * Class PatientAgeParameter
 */
class PatientAgeParameter extends CaseSearchParameter implements DBProviderInterface
{
    /**
     * @var integer $textValue Represents a single value
     */
    public $textValue;

    /**
     * @var integer $minValue Represents a minimum value.
     */
    public $minValue;

    /**
     * @var integer $maxValue Represents a maximum value.
     */
    public $maxValue;

    /**
     * PatientAgeParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario Model scenario.
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'age';
    }

    /**
     * This has been overridden to allow additional rules surrounding the operator and value fields.
     * @return array Complete array of validation rules.
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('textValue, minValue, maxValue', 'numerical', 'min' => 0),
            array('textValue, minValue, maxValue', 'values'),
        ));
    }

    /**
     * This has been overridden to add additional attributes.
     * @return array Complete array of attribute names.
     */
    public function attributeNames()
    {
        return array_merge(parent::attributeNames(), array(
            'textValue',
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
            'textValue' => 'Value',
            'minValue' => 'Minimum Value',
            'maxValue' => 'Maximum Value',
            'id' => 'ID',
        );
    }

    /**
     * Validator to validate parameter values for specific operators.
     * @param $attribute string Attribute being validated.
     */
    public function values($attribute)
    {
        $label = $this->attributeLabels()[$attribute];
        if ($attribute === 'minValue' || $attribute === 'maxValue') {
            if ($this->operation === 'BETWEEN' && $this->$attribute === '') {
                $this->addError($attribute, "$label must be specified.");
            }
        } else {
            if ($this->operation !== 'BETWEEN' && $this->$attribute === '') {
                $this->addError($attribute, "$label must be specified.");
            }
        }
    }

    /**
     * @return string "Patient age".
     */
    public function getLabel()
    {
        return 'Patient Age';
    }

    /**
     * @param $id integer ID of the parameter for rendering purposes.
     */
    public function renderParameter($id)
    {
        $ops = array(
            '<=' => 'Younger than',
            '>' => 'Older than',
            'BETWEEN' => 'Between',
        );
        ?>
      <div class="flex-layout flex-left js-case-search-param">
        <div class="parameter-option">
            <p><?= $this->getLabel() ?></p>
        </div>
        <div class="parameter-option">
            <?php echo CHtml::activeDropDownList(
                    $this,
                    "[$id]operation",
                    $ops,
                    array('onchange' => 'refreshValues(this)', 'prompt' => 'Select One...', 'class' => 'js-age-operation')
            ); ?>
            <?php echo CHtml::error($this, "[$id]operation"); ?>
        </div>
        <div class="dual-value parameter-option"
             style="<?php echo $this->operation === 'BETWEEN' ? 'display: inline-block;' : 'display: none;' ?>"
        >
              <?php echo CHtml::activeTextField($this, "[$id]minValue", array('placeholder' => 'min', 'class' => 'js-age-min')); ?>
              <?php echo CHtml::error($this, "[$id]minValue"); ?>
              <?php echo CHtml::activeTextField($this, "[$id]maxValue", array('placeholder' => 'max', 'class' => 'js-age-max')); ?>
              <?php echo CHtml::error($this, "[$id]maxValue"); ?>
        </div>
        <div class="single-value parameter-option"
             style="<?php echo $this->operation !== 'BETWEEN' ? 'display: inline-block;' : 'display: none;' ?>"
        >
            <?php echo CHtml::activeTextField($this, "[$id]textValue", array('class' => 'js-age-value')); ?>
            <?php echo CHtml::error($this, "[$id]textValue"); ?>
        </div>
        <div class="parameter-option">
          <p>years old</p>
        </div>
      </div>
        <?php
    }

    /**
     * Generate the SQL query for patient age.
     * @param $searchProvider DBProvider The search provider building the query.
     * @return null|string The query string for use by the search provider, or null if not implemented for the specified search provider.
     * @throws CHttpException
     */
    public function query($searchProvider)
    {
        switch ($this->operation) {
            case 'BETWEEN':
                $op = 'BETWEEN';
                break;
            case '>':
                $op = '>';
                break;
            case '<=':
                $op = '<=';
                break;
            default:
                throw new CHttpException(400, 'Invalid operator specified.');
                break;
        }

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
        } else {
            $bindValues["p_a_value_$this->id"] = (int)$this->textValue;
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

        return "$this->name: $this->operation $this->textValue";
    }
}