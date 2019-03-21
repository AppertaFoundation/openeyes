<?php

/**
 * Class PatientVisionParameter
 */
class PatientVisionParameter extends CaseSearchParameter implements DBProviderInterface
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
     * PatientVisionParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario Model scenario.
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'vision';
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
     * @return string "Patient Vision".
     */
    public function getLabel()
    {
        return 'Patient Vision';
    }

    /**
     * @param $id integer ID of the parameter for rendering purposes.
     */
    public function renderParameter($id)
    {
        $ops = array(
            '<' => 'Worse than',
            '>' => 'Better than',
            'BETWEEN' => 'Between',
        );
        $va_values = \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity::model()->getUnitValuesForForm(OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::model()->findByAttributes(array('name'=>'ETDRS Letters'))->id, false);
        $va_values = $va_values[0];
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
                    array('onchange' => 'refreshValues(this)', 'prompt' => 'Select One...', 'class' => 'js-vision-operation')
                ); ?>
                <?php echo CHtml::error($this, "[$id]operation"); ?>
            </div>
            <div class="dual-value parameter-option"
                 style="<?php echo $this->operation === 'BETWEEN' ? 'display: inline-block;' : 'display: none;' ?>"
            >
                <?php echo CHtml::activeDropDownList(
                        $this,
                        "[$id]minValue",
                        $va_values,
                        array( 'class' => 'js-vision-min')
                ); ?>
                <?php echo CHtml::error($this, "[$id]minValue"); ?>
                <?php echo CHtml::activeDropDownList(
                    $this,
                    "[$id]maxValue",
                    $va_values,
                    array( 'class' => 'js-vision-max')
                ); ?>
                <?php echo CHtml::error($this, "[$id]maxValue"); ?>
            </div>
            <div class="single-value parameter-option"
                 style="<?php echo $this->operation !== 'BETWEEN' ? 'display: inline-block;' : 'display: none;' ?>"
            >
                <?php echo CHtml::activeDropDownList(
                    $this,
                    "[$id]textValue",
                    $va_values,
                    array('class' => 'js-vision-value')
                ); ?>
                <?php echo CHtml::error($this, "[$id]textValue"); ?>
            </div>
            <div class="parameter-option">
                <p>ETDRS Letters</p>
            </div>
        </div>
        <?php
    }

    /**
     * Generate the SQL query for patient vision.
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
            case '<':
                $op = '<';
                break;
            default:
                throw new CHttpException(400, 'Invalid operator specified.');
        }

        $queryStr = 'SELECT distinct t2.patient_id FROM
(
SELECT t1.patient_id, t1.va_value
FROM
(
SELECT patient.id AS patient_id, ovr.value AS va_value
FROM patient
LEFT JOIN episode e ON patient.id = e.patient_id
LEFT JOIN event ON event.episode_id = e.id
LEFT JOIN et_ophciexamination_visualacuity eov ON event.id = eov.event_id
LEFT JOIN ophciexamination_visualacuity_reading ovr ON eov.id = ovr.element_id
ORDER BY ovr.last_modified_date DESC
) t1
GROUP BY patient_id
) t2 WHERE t2.va_value';

        if ($op === 'BETWEEN') {
            return "$queryStr BETWEEN :p_v_min_$this->id AND :p_v_max_$this->id";
        }

        return "$queryStr $op :p_v_value_$this->id";
    }

    /**
     * @return array The list of bind values being used by the current parameter instance.
     */
    public function bindValues()
    {
        $bindValues = array();

        if ($this->operation === 'BETWEEN') {
            $bindValues["p_v_min_$this->id"] = (int)$this->minValue;
            $bindValues["p_v_max_$this->id"] = (int)$this->maxValue;
        } else {
            $bindValues["p_v_value_$this->id"] = (int)$this->textValue;
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