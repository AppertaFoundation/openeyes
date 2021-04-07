<?php

/**
 * Class PatientDiagnosisParameter
 */
class PatientDiagnosisParameter extends CaseSearchParameter implements DBProviderInterface
{
    /**
     * @var int|null $firm_id
     */
    public $firm_id = null;

    /**
     * @var bool $only_last_event
     */
    public $only_latest_event = false;

    protected array $options = array(
        'value_type' => 'string_search',
    );

    protected ?string $label_ = 'Diagnosis';

    /**
     * PatientAgeParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'diagnosis';
        $this->only_latest_event = false;
        $this->options['operations'][0] = array('label' => 'INCLUDES', 'id' => 'IN');
        $this->options['operations'][1] = array('label' => 'DOES NOT INCLUDE', 'id' => 'NOT IN');

        $firms = Firm::model()->getListWithSpecialties();

        $this->options['option_data'] = array(
            array(
                'id' => 'firm',
                'field' => 'firm_id',
                'options' => array_map(
                    static function ($item, $key) {
                        return array('id' => $key, 'label' => $item);
                    },
                    $firms,
                    array_keys($firms)
                ),
            ),
            array(
                'id' => 'latest-event',
                'field' => 'only_latest_event',
                'options' => array(
                    array('id' => true, 'label' => 'Only latest event')
                ),
            ),
        );
    }

    public function getValueForAttribute(string $attribute)
    {
        if (in_array($attribute, $this->attributeNames(), true)) {
            switch ($attribute) {
                case 'value':
                    return Disorder::model()->findByPk($this->$attribute)->term;
                case 'firm_id':
                    if ($this->$attribute !== '' && $this->$attribute !== null) {
                        return Firm::model()->findByPk($this->$attribute)->name;
                    }
                    return 'Any context';
                case 'only_latest_event':
                    return $this->$attribute ? 'Only patient\'s latest event' : '';
                default:
                    return parent::getValueForAttribute($attribute);
            }
        }
        return parent::getValueForAttribute($attribute);
    }

    /**
     * @return array The validation rules for the parameter.
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            array(
                array('value', 'required'),
                array('firm_id, only_latest_event', 'safe'),
            )
        );
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @return string The constructed query string.
     */
    public function query()
    {
        $query = "
SELECT episode.patient_id
FROM ophciexamination_diagnosis diagnosis
JOIN et_ophciexamination_diagnoses diagnoses ON diagnoses.id = diagnosis.element_diagnoses_id
JOIN event ON event.id = diagnoses.event_id
JOIN episode ON episode.id = event.episode_id
JOIN disorder ON diagnosis.disorder_id = disorder.id
WHERE disorder.id = :p_d_value_$this->id
AND (:p_d_firm_$this->id IS NULL OR event.firm_id = :p_d_firm_$this->id)
AND (:p_d_only_latest_event_$this->id = 0 OR
  NOT EXISTS (
    SELECT true
    FROM et_ophciexamination_diagnoses later_diagnoses
    JOIN event later_event ON later_event.id = later_diagnoses.event_id
    JOIN episode later_episode ON later_episode.id = later_event.episode_id
    WHERE later_episode.patient_id = episode.patient_id
       AND later_event.event_date > event.event_date
       OR (later_event.event_date = event.event_date AND later_event.created_date > event.created_date)
  )
)

UNION

SELECT episode.patient_id
FROM ophciexamination_systemic_diagnoses_diagnosis diagnosis
JOIN et_ophciexamination_systemic_diagnoses diagnoses ON diagnoses.id = diagnosis.element_id
JOIN event ON event.id = diagnoses.event_id
JOIN episode ON episode.id = event.episode_id
JOIN disorder ON diagnosis.disorder_id = disorder.id
WHERE disorder.id = :p_d_value_$this->id
AND (:p_d_firm_$this->id IS NULL OR event.firm_id = :p_d_firm_$this->id)
AND (:p_d_only_latest_event_$this->id = 0 OR
  NOT EXISTS (
    SELECT true
    FROM et_ophciexamination_systemic_diagnoses later_diagnoses
    JOIN event later_event ON later_event.id = later_diagnoses.event_id
    JOIN episode later_episode ON later_episode.id = later_event.episode_id
    WHERE later_episode.patient_id = episode.patient_id
       AND later_event.event_date > event.event_date
       OR (later_event.event_date = event.event_date AND later_event.created_date > event.created_date)
  )
)";
        if (($this->firm_id === '' || $this->firm_id === null) && $this->only_latest_event === 0) {
            $query .= ' UNION ';
            $query .= "SELECT p3.id
FROM patient p3 
JOIN secondary_diagnosis sd
  ON sd.patient_id = p3.id
JOIN disorder d3
  ON d3.id = sd.disorder_id
WHERE d3.id = :p_d_value_$this->id
AND :p_d_firm_$this->id IS NULL
AND :p_d_only_latest_event_$this->id = 0";
        }

        if ($this->operation === 'NOT IN') {
                $query = "
SELECT DISTINCT p1.id
FROM patient p1
WHERE p1.id NOT IN (
  $query
)";
        }

        return $query;
    }

    public static function getCommonItemsForTerm(string $term)
    {
        $disorders = Disorder::model()->findAllBySql(
            'SELECT * FROM disorder
WHERE LOWER(term) LIKE LOWER(:term)
   OR LOWER(aliases) LIKE LOWER(:term)
   OR LOWER(fully_specified_name) LIKE LOWER(:term)
ORDER BY term LIMIT  ' . self::_AUTOCOMPLETE_LIMIT,
            array('term' => "$term%")
        );
        return array_map(
            static function ($disorder) {
                return array('id' => $disorder->id, 'label' => $disorder->term);
            },
            $disorders
        );
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        return array(
            "p_d_value_$this->id" => $this->value,
            "p_d_only_latest_event_$this->id" => $this->only_latest_event,
            "p_d_firm_$this->id" => $this->firm_id ?: null,
        );
    }

    /**
     * @inherit
     */
    public function getAuditData()
    {
        $op = '=';
        $result = null;
        if ($this->operation !== 'IN') {
            $op = '!=';
        }
        $result = "$this->name: $op \"$this->value\"";

        if ($this->firm_id !== '' && $this->firm_id !== null) {
            $firm = Firm::model()->findByPk($this->firm_id);
            $result .= " diagnosed by {$firm->getNameAndSubspecialty()}";
        }

        if ($this->only_latest_event) {
            $result .= ' with only the latest event';
        }

        return $result;
    }

    public function saveSearch()
    {
        return array_merge(
            parent::saveSearch(),
            array(
                'firm_id' => $this->firm_id,
                'only_latest_event' => $this->only_latest_event,
            )
        );
    }
}
