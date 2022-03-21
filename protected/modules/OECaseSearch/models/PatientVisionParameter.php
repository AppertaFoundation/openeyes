<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit;

/**
 * Class PatientVisionParameter
 */
class PatientVisionParameter extends CaseSearchParameter implements DBProviderInterface
{
    /**
     * @var bool $bothEyesIndicator Indicates whether value must be identical for both eyes when searching.
     */
    public bool $bothEyesIndicator = false;

    /**
     * @var array $va_values List of VA values ($base_value => $etdrs_value)
     */
    private $va_values;

    /**
     * @var string[] $options List of options for the Adder Dialog.
     */
    protected array $options = array(
        'value_type' => 'multi_select',
        'operations' => array(
            array('label' => 'IS', 'id' => '='),
            array('label' => 'IS NOT', 'id' => '!='),
            array('label' => 'IS LESS THAN', 'id' => '<'),
            array('label' => 'IS MORE THAN', 'id' => '>')
        )
    );

    /**
     * @var string|null $label_ Label to display in adder dialog for the parameter.
     */
    protected string $label_ = 'Vision';

    /**
     * PatientVisionParameter constructor.
     * This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario Model scenario.
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'vision';
        $this->va_values = Element_OphCiExamination_VisualAcuity::model()->getUnitValuesForForm(
            OphCiExamination_VisualAcuityUnit::model()->findByAttributes(array('name' => 'ETDRS Letters'))->id,
            false
        )[0];
        $this->options['option_data'] = array(
            array(
                'id' => 'va-values',
                'field' => 'value',
                'options' => array_map(
                    static function ($item, $key) {
                        return array('id' => $key, 'label' => $item);
                    },
                    $this->va_values,
                    array_keys($this->va_values)
                )
            ),
            array(
                'id' => 'both-eyes',
                'field' => 'bothEyesIndicator',
                'options' => array(
                    array('id' => 1, 'label' => 'Both Eyes')
                )
            ),
        );
    }

    /**
     * @param string $attribute
     * @return mixed|void
     * @throws CException
     */
    public function getValueForAttribute(string $attribute)
    {
        if (in_array($attribute, $this->attributeNames(), true)) {
            switch ($attribute) {
                case 'value':
                    return $this->va_values[$this->$attribute];
                case 'bothEyesIndicator':
                    return $this->$attribute ? 'Both eyes' : '';
                default:
                    return parent::getValueForAttribute($attribute);
            }
        }
        return parent::getValueForAttribute($attribute);
    }

    /**
     * This has been overridden to allow additional rules surrounding the operator and value fields.
     * @return array Complete array of validation rules.
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('value', 'required'),
            array('value', 'safe'),
            array('bothEyesIndicator', 'safe'),
        ));
    }

    /**
     * Generate the SQL query for patient vision.
     * @return string The query string for use by the search provider.
     */
    public function query(): string
    {
        $second_operation = 'OR';
        $op = $this->operation;

        if ($this->bothEyesIndicator) {
            $second_operation = 'AND';
        }

        $queryStr = 'SELECT DISTINCT t5.patient_id
FROM (
       SELECT DISTINCT patient_id, MAX(left_va_value) AS left_va_value, MAX(right_va_value) AS right_va_value
       FROM (
              SELECT patient_id                      AS patient_id,
                     IF(va_side = 0, va_value, NULL) AS left_va_value,
                     IF(va_side = 1, va_value, NULL) AS right_va_value
              FROM (
                     SELECT t1.patient_id, t1.va_value, t1.va_side
                     FROM (SELECT patient.id             AS patient_id,
                                  ovr.value              AS va_value,
                                  ovr.side               AS va_side,
                                  ovr.last_modified_date AS date
                           FROM patient
                                  LEFT JOIN episode e ON patient.id = e.patient_id
                                  LEFT JOIN event ON event.episode_id = e.id
                                  LEFT JOIN et_ophciexamination_visualacuity eov ON event.id = eov.event_id
                                  LEFT JOIN ophciexamination_visualacuity_reading ovr ON eov.id = ovr.element_id
                           WHERE ovr.value IS NOT NULL
                             AND ovr.side IS NOT NULL
                             AND ovr.last_modified_date IS NOT NULL) t1
                     WHERE t1.date = (SELECT MAX(t2.date)
                                      FROM (SELECT patient.id             AS patient_id,
                                                   ovr.side               AS va_side,
                                                   ovr.last_modified_date AS date
                                            FROM patient
                                                   LEFT JOIN episode e ON patient.id = e.patient_id
                                                   LEFT JOIN event ON event.episode_id = e.id
                                                   LEFT JOIN et_ophciexamination_visualacuity eov
                                                       ON event.id = eov.event_id
                                                   LEFT JOIN ophciexamination_visualacuity_reading ovr
                                                       ON eov.id = ovr.element_id
                                            WHERE ovr.value IS NOT NULL
                                              AND ovr.side IS NOT NULL
                                              AND ovr.last_modified_date IS NOT NULL) t2
                                      WHERE t2.patient_id = t1.patient_id
                                        AND t1.va_side = t2.va_side)
                   ) t3) t4
       GROUP BY patient_id) t5
';

        $subQueryStr = "WHERE (t5.left_va_value $op :p_v_value_$this->id)
$second_operation (t5.right_va_value $op :p_v_value_$this->id)";

        return $queryStr . $subQueryStr;
    }

    /**
     * @return array The list of bind values being used by the current parameter instance.
     */
    public function bindValues(): array
    {
        return array(
            "p_v_value_$this->id" => (int)$this->value
        );
    }

    /**
     * @inherit
     */
    public function getAuditData() : string
    {
        $bothEyes = $this->bothEyesIndicator ? ' searching both eyes' : '';

        return "$this->name: $this->operation $this->value" . $bothEyes;
    }

    public function saveSearch() : array
    {
        return array_merge(
            parent::saveSearch(),
            array(
                'bothEyesIndicator' => $this->bothEyesIndicator
            )
        );
    }
}
