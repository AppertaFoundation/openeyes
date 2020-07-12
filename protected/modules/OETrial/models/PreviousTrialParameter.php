<?php

/**
 * @property int $trialTypeId
 * @property TrialType $trialType
 *
 * @property int $treatmentTypeId
 * @property TreatmentType $treatmentType
 *
 * @inherit
 */
class PreviousTrialParameter extends CaseSearchParameter implements DBProviderInterface
{
    public $trial;
    public $trialTypeId;
    public $status;
    public $treatmentTypeId;

    private $statusList;

    protected $label_ = 'Previous Trial';

    protected $options = array(
        'value_type' => 'multi_select',
    );

    /**
     * @return TrialType
     */
    public function getTrialType()
    {
        return TrialType::model()->findByPk($this->trialTypeId);
    }

    public function getTreatmentType()
    {
        return TreatmentType::model()->findByPk($this->treatmentTypeId);
    }

    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'previous_trial';
        $this->status = TrialPatientStatus::model()->find('code = "ACCEPTED"')->id;

        $trialTypes = TrialType::getOptions();
        $treatmentTypes = TreatmentType::getOptions();

        $this->options['operations'][0]['label'] = 'INCLUDES';
        $this->options['operations'][0]['id'] = 'IN';
        $this->options['operations'][1]['label'] = 'DOES NOT INCLUDE';
        $this->options['operations'][1]['id'] = 'NOT IN';

        $intervention_trials = Trial::getTrialList(TrialType::model()->findByAttributes(array('code' => 'INTERVENTION'))->id);
        $non_intervention_trials = Trial::getTrialList(TrialType::model()->findByAttributes(array('code' => 'NON_INTERVENTION'))->id);

        $this->statusList = array(
            TrialPatientStatus::model()->find('code = "SHORTLISTED"')->id => 'Shortlisted in trial',
            TrialPatientStatus::model()->find('code = "ACCEPTED"')->id => 'Accepted into trial',
            TrialPatientStatus::model()->find('code = "REJECTED"')->id => 'Rejected from trial',
        );

        $this->options['option_data'] = array(
            array(
                'id' => 'trial-status',
                'field' => 'status',
                'options' => array_merge(
                    array(
                        array('id' => '', 'label' => 'Any status', 'selected' => true)
                    ),
                    array_map(
                        static function ($item, $key) {
                            return array('id' => $key, 'label' => $item);
                        },
                        $this->statusList,
                        array_keys($this->statusList)
                    )
                )
            ),
            array(
                'id' => 'trial-type',
                'field' => 'trialTypeId',
                'options' => array_merge(
                    array(
                        array('id' => '', 'label' => 'Any trial type', 'conditional_id' => '', 'selected' => true)
                    ),
                    array_map(
                        static function ($item, $key) {
                            $conditional_id = '';
                            if ($key == TrialType::model()->findByAttributes(array('code' => 'INTERVENTION'))->id) {
                                $conditional_id = 'trial-type-intervention-trial,trial-type-treatment-type';
                            } elseif ($key == TrialType::model()->findByAttributes(array('code' => 'NON_INTERVENTION'))->id) {
                                $conditional_id = 'trial-type-non-intervention-trial';
                            }
                            return array('id' => $key, 'label' => $item, 'conditional_id' => $conditional_id);
                        },
                        $trialTypes,
                        array_keys($trialTypes)
                    )
                )
            ),
            array(
                'id' => 'trial-type-intervention-trial',
                'field' => 'trial',
                'hidden' => true,
                'options' => array_merge(
                    array(
                        array('id' => '', 'label' => 'Any trial', 'selected' => true)
                    ),
                    array_map(
                        static function ($item, $key) {
                            return array('id' => $key, 'label' => $item);
                        },
                        $intervention_trials,
                        array_keys($intervention_trials)
                    )
                )
            ),
            array(
                'id' => 'trial-type-non-intervention-trial',
                'field' => 'trial',
                'hidden' => true,
                'options' => array_merge(
                    array(
                        array('id' => '', 'label' => 'Any trial', 'selected' => true)
                    ),
                    array_map(
                        static function ($item, $key) {
                            return array('id' => $key, 'label' => $item);
                        },
                        $non_intervention_trials,
                        array_keys($non_intervention_trials)
                    )
                )
            ),
            array(
                'id' => 'trial-type-treatment-type',
                'field' => 'treatmentTypeId',
                'hidden' => true,
                'options' => array_merge(
                    array(
                        array('id' => '', 'label' => 'Any treatment', 'selected' => true)
                    ),
                    array_map(
                        static function ($item, $key) {
                            return array('id' => $key, 'label' => $item);
                        },
                        $treatmentTypes,
                        array_keys($treatmentTypes)
                    )
                )
            ),
        );
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
                array('trialType, trialTypeId,  trial, status, treatmentTypeId', 'safe'),
            )
        );
    }

    public function getValueForAttribute($attribute)
    {
        if (in_array($attribute, $this->attributeNames(), true)) {
            switch ($attribute) {
                case 'trial':
                    return $this->trialTypeId ? ($this->$attribute ? Trial::model()->findByPk($this->$attribute)->name : 'Any trial') : '';
                    break;
                case 'trialTypeId':
                    return 'Participating in ' . ($this->$attribute ? $this->getTrialType()->name : 'any') . ' trial';
                    break;
                case 'treatmentTypeId':
                    return 'Received ' . ($this->$attribute ? $this->getTreatmentType()->name : 'any') . ' treatment';
                    break;
                case 'status':
                    return $this->$attribute ? $this->statusList[$this->$attribute] : 'Any trial status';
                    break;
                default:
                    return parent::getValueForAttribute($attribute);
            }
        }
        return parent::getValueForAttribute($attribute);
    }

    /**
     * Generate a SQL fragment representing the sub-query of a FROM condition.
     * @return mixed The constructed query string.
     */
    public function query()
    {
        $condition = null;
        $joinCondition = 'JOIN';
        if ($this->trialType) {
            if ($this->trial === '') {
                // Any intervention/non-intervention trial
                $condition = "t.trial_type_id = :p_t_trial_type_$this->id";
            } else {
                // specific trial
                $condition = "t_p.trial_id = :p_t_trial_$this->id";
            }
        } else {
            // Any trial
            $condition = 't_p.trial_id IS NOT NULL';
        }

        if ($this->status !== '' && $this->status !== null) {
            //in a trial with a specific status
            $condition .= " AND t_p.status_id = :p_t_status_$this->id";
        } else {
            // in any trial
            $condition .= ' AND t_p.status_id IN (
                      SELECT id FROM trial_patient_status WHERE code IN ("ACCEPTED", "SHORTLISTED", "REJECTED"))';
        }

        if ((!$this->trialType || $this->trialType->code !== TrialType::NON_INTERVENTION_CODE)
            && $this->treatmentTypeId !== '' && $this->treatmentTypeId !== null
        ) {
            $condition .= " AND t_p.treatment_type_id = :p_t_treatment_type_id_$this->id";
        }
        if ($this->operation === 'IN') {
            $query = "SELECT p.id 
                        FROM patient p 
                        $joinCondition trial_patient t_p 
                          ON t_p.patient_id = p.id 
                        $joinCondition trial t
                          ON t.id = t_p.trial_id
                        WHERE $condition";
        } else {
                $query = "SELECT p.id from patient p WHERE p.id NOT IN (SELECT p.id 
                            FROM patient p 
                            $joinCondition trial_patient t_p 
                              ON t_p.patient_id = p.id 
                            $joinCondition trial t
                              ON t.id = t_p.trial_id
                            WHERE $condition)";
        }

        return $query;
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        $binds = array();
        if ($this->trialType) {
            if ($this->trial === '') {
                $binds[":p_t_trial_type_$this->id"] = $this->trialTypeId;
            } else {
                $binds[":p_t_trial_$this->id"] = $this->trial;
            }
        }

        if ($this->status !== '' && $this->status !== null) {
            $binds[":p_t_status_$this->id"] = $this->status;
        }
        if ((!$this->trialType || $this->trialType->code !== TrialType::NON_INTERVENTION_CODE)
            && $this->treatmentTypeId !== '' && $this->treatmentTypeId !== null
        ) {
            $binds[":p_t_treatment_type_id_$this->id"] = $this->treatmentTypeId;
        }

        return $binds;
    }

    /**
     * @inherit
     */
    public function getAuditData()
    {
        $trialTypes = TrialType::getOptions();

        $statusList = array(
            TrialPatientStatus::model()->find('code = "SHORTLISTED"')->id => 'Shortlisted in',
            TrialPatientStatus::model()->find('code = "ACCEPTED"')->id => 'Accepted in',
            TrialPatientStatus::model()->find('code = "REJECTED"')->id => 'Rejected from',
        );
        $trials = Trial::getTrialList(isset($this->trialType) ? $this->trialType->id : '');
        $treatmentTypeList = TreatmentType::getOptions();

        $status = $this->status === null || $this->status === '' ? 'Included in' : $statusList[$this->status];
        $type = !$this->trialType ? 'Any Trial Type with' : $trialTypes[$this->trialTypeId];
        $trial = $this->trial === null || $this->trial === '' ? 'Any trial with' : $trials[$this->trial] . ' with ';
        $treatment = $this->treatmentTypeId === null || $this->treatmentTypeId === '' ? 'Any Treatment' : $treatmentTypeList[$this->treatmentTypeId];

        return "$this->name: $this->operation $status $type $trial $treatment";
    }

    public function saveSearch()
    {
        return array_merge(
            parent::saveSearch(),
            array(
                'trial' => $this->trial,
                'trialTypeId' => $this->trialTypeId,
                'status' => $this->status,
                'treatmentTypeId' => $this->treatmentTypeId,
            )
        );
    }
}
