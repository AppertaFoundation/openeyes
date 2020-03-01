<?php

/**
 * Class PatientDiagnosisParameter
 */
class PatientDiagnosisParameter extends CaseSearchParameter implements DBProviderInterface
{
    /**
     * @var string $term
     */
    public $term;

    /**
     * @var integer $firm_id
     */
    public $firm_id;

    /**
     * @var boolean $only_last_event
     */
    public $only_latest_event;

    /**
     * PatientAgeParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'diagnosis';
        $this->only_latest_event = false;
    }

    public function getLabel()
    {
        return 'Diagnosis';
    }

    /**
     * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
     * @return array An array of attribute names.
     */
    public function attributeNames()
    {
        return array_merge(parent::attributeNames(), array('term', 'firm_id', 'only_latest_event'));
    }

    /**
     * Override this function if the parameter subclass has extra validation rules. If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
     * @return array The validation rules for the parameter.
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            array(
                array('term', 'required'),
                array('term, firm_id, only_latest_event', 'safe'),
            )
        );
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @param $searchProvider DBProvider The search provider. This is used to determine whether or not the search provider is using SQL syntax.
     * @return string The constructed query string.
     * @throws CHttpException
     */
    public function query($searchProvider)
    {
        $query = "
SELECT episode.patient_id
FROM ophciexamination_diagnosis diagnosis
JOIN et_ophciexamination_diagnoses diagnoses ON diagnoses.id = diagnosis.element_diagnoses_id
JOIN event ON event.id = diagnoses.event_id
JOIN episode ON episode.id = event.episode_id
JOIN disorder ON diagnosis.disorder_id = disorder.id
WHERE LOWER(disorder.term) LIKE LOWER(:p_d_value_$this->id)
AND (:p_d_firm_$this->id IS NULL OR event.firm_id = :p_d_firm_$this->id)
AND (:p_d_only_latest_event_$this->id = 0 OR
  NOT EXISTS (
    SELECT true
    FROM et_ophciexamination_diagnoses later_diagnoses
    JOIN event later_event ON later_event.id = later_diagnoses.event_id
    JOIN episode later_episode ON later_episode.id = later_event.episode_id
    WHERE later_episode.patient_id = episode.patient_id
    AND later_event.event_date > event.event_date OR (later_event.event_date = event.event_date AND later_event.created_date > event.created_date)
  )
)

UNION

SELECT episode.patient_id
FROM ophciexamination_systemic_diagnoses_diagnosis diagnosis
JOIN et_ophciexamination_systemic_diagnoses diagnoses ON diagnoses.id = diagnosis.element_id
JOIN event ON event.id = diagnoses.event_id
JOIN episode ON episode.id = event.episode_id
JOIN disorder ON diagnosis.disorder_id = disorder.id
WHERE LOWER(disorder.term) LIKE LOWER(:p_d_value_$this->id)
AND (:p_d_firm_$this->id IS NULL OR event.firm_id = :p_d_firm_$this->id)
AND (:p_d_only_latest_event_$this->id = 0 OR
  NOT EXISTS (
    SELECT true
    FROM et_ophciexamination_systemic_diagnoses later_diagnoses
    JOIN event later_event ON later_event.id = later_diagnoses.event_id
    JOIN episode later_episode ON later_episode.id = later_event.episode_id
    WHERE later_episode.patient_id = episode.patient_id
    AND later_event.event_date > event.event_date OR (later_event.event_date = event.event_date AND later_event.created_date > event.created_date)
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
WHERE LOWER(d3.term) LIKE LOWER(:p_d_value_$this->id)
AND :p_d_firm_$this->id IS NULL
AND :p_d_only_latest_event_$this->id = 0";
        }

        if ($this->operation) {
                $query = "
SELECT DISTINCT p1.id
FROM patient p1
WHERE p1.id NOT IN (
  $query
)";
        }

        return $query;
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        $result = array(
            "p_d_value_$this->id" => '%' . $this->term . '%',
            "p_d_only_latest_event_$this->id" => $this->only_latest_event,
            "p_d_firm_$this->id" => $this->firm_id ?: null,
        );

        return $result;
    }

    /**
     * @inherit
     */
    public function getAuditData()
    {
        $op = 'LIKE';
        $result = null;
        if ($this->operation) {
            $op = 'NOT LIKE';
        }
        $result = "$this->name: $op \"$this->term\"";

        if ($this->firm_id !== '' && $this->firm_id !== null) {
            $firm = Firm::model()->findByPk($this->firm_id);
            $result .= "$this->name: $this->operation \"$this->term\" diagnosed by {$firm->getNameAndSubspecialty()}";
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
                'term' => $this->term,
                'firm_id' => $this->firm_id,
                'only_latest_event' => $this->only_latest_event,
            )
        );
    }
}
