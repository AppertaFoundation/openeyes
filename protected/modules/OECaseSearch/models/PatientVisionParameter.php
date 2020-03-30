<?php

use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;

/**
 * Class PatientVisionParameter
 *
 * @property int $minValue Represents a minimum value.
 * @property int $maxValue Represents a maximum value.
 * @property bool $bothEyesIndicator
 */
class PatientVisionParameter extends CaseSearchParameter implements DBProviderInterface
{
    public $bothEyesIndicator = false;
    private $va_values;

    protected $options = array(
        'value_type' => 'multi_select',
    );

    protected $_label = 'Vision';

    /**
     * PatientVisionParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario Model scenario.
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'vision';
        $this->options['operations'][0]['label'] = 'INCLUDES';
        $this->options['operations'][0]['id'] = 'IN';
        $this->options['operations'][1]['label'] = 'DOES NOT INCLUDE';
        $this->options['operations'][1]['id'] = 'NOT IN';
        $this->va_values = Element_OphCiExamination_VisualAcuity::model()->getUnitValuesForForm(
            OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::model()->findByAttributes(array('name' => 'ETDRS Letters'))->id,
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

    public function getValueForAttribute($attribute)
    {
        if (in_array($attribute, $this->attributeNames(), true)) {
            switch ($attribute) {
                case 'value':
                    return $this->va_values[$this->$attribute];
                    break;
                case 'bothEyesIndicator':
                    return 'Both eyes';
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
     * Attribute labels for display purposes.
     * @return array Attribute key/value pairs.
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            array(
                'bothEyesIndicator' => 'Both Eyes',
            )
        );
    }

    /**
     * Generate the SQL query for patient vision.
     * @return null|string The query string for use by the search provider, or null if not implemented for the specified search provider.
     */
    public function query()
    {
        $second_operation = 'OR';
        $op = '=';

        if ($this->operation === 'NOT IN') {
            $op = '!=';
        }

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
                                                   LEFT JOIN et_ophciexamination_visualacuity eov ON event.id = eov.event_id
                                                   LEFT JOIN ophciexamination_visualacuity_reading ovr ON eov.id = ovr.element_id
                                            WHERE ovr.value IS NOT NULL
                                              AND ovr.side IS NOT NULL
                                              AND ovr.last_modified_date IS NOT NULL) t2
                                      WHERE t2.patient_id = t1.patient_id
                                        AND t1.va_side = t2.va_side)
                   ) t3) t4
       GROUP BY patient_id) t5';

        $subQueryStr = " WHERE (t5.left_va_value $op :p_v_value_$this->id) $second_operation (t5.right_va_value $op :p_v_value_$this->id)";

        return $queryStr . $subQueryStr;
    }

    /**
     * @return array The list of bind values being used by the current parameter instance.
     */
    public function bindValues()
    {
        return array(
            "p_v_value_$this->id" => (int)$this->value
        );
    }

    /**
     * @inherit
     */
    public function getAuditData()
    {
        $bothEyes = $this->bothEyesIndicator ? ' searching both eyes' : '';

        return "$this->name: $this->operation $this->value" . $bothEyes;
    }

    public function saveSearch()
    {
        return array_merge(
            parent::saveSearch(),
            array(
                'bothEyesIndicator' => $this->bothEyesIndicator
            )
        );
    }
}
