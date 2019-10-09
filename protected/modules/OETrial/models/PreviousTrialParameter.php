<?php

/**
 * @property integer $trialTypeId
 * @property TrialType $trialType
 *
 * @property integer $treatmentTypeId
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
    }

    public function getLabel()
    {
        // This is a human-readable value, so feel free to change this as required.
        return 'Previous Trial';
    }

    /**
     * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
     * @return array An array of attribute names.
     */
    public function attributeNames()
    {
        return array_merge(parent::attributeNames(), array(
                'trial',
                'trialType',
                'status',
                'treatmentTypeId',
            )
        );
    }

    /**
     * Override this function if the parameter subclass has extra validation rules. If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
     * @return array The validation rules for the parameter.
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
                array('trialType, trialTypeId,  trial, status, treatmentTypeId', 'safe'),
            )
        );
    }

    public function renderParameter($id)
    {
        $ops = array(
            '=' => 'Is',
            '!=' => 'Is not',
        );

        $trials = Trial::getTrialList(isset($this->trialType) ? $this->trialType->id : '');

        $statusList = array(
            TrialPatientStatus::model()->find('code = "SHORTLISTED"')->id => 'Shortlisted in',
            TrialPatientStatus::model()->find('code = "ACCEPTED"')->id => 'Accepted in',
            TrialPatientStatus::model()->find('code = "REJECTED"')->id => 'Rejected from',
        );

        ?>
        <div class="flex-layout flex-left js-case-search-param">
            <div class="parameter-option">
                <?= $this->getDisplayTitle()?>
            </div>
            <div class="parameter-option">
                <?php echo CHtml::activeDropDownList($this, "[$id]operation", $ops,
                    array('prompt' => 'Select One...')); ?>
                <?php echo CHtml::error($this, "[$id]operation"); ?>
            </div>
            <div class="parameter-option">
                <?php echo CHtml::activeDropDownList(
                    $this,
                    "[$id]status",
                    $statusList,
                    array('empty' => 'Involved with'));
                ?>
            </div>
            <div class="js-trial-type parameter-option">
            <?php echo CHtml::activeDropDownList($this, "[$id]trialTypeId", TrialType::getOptions(),
                    array('empty' => 'Any Trial', 'onchange' => "getTrialList(this)")); ?>
            </div>
            <div class="js-trial-list parameter-option">
                <?php echo CHtml::activeDropDownList($this, "[$id]trial", $trials,
                    array('empty' => 'Any')); ?>
            </div>
            <span class="js-treatment-type-container flex-layout flex-left"
                style="<?= $this->trialType && $this->trialType->code = TrialType::NON_INTERVENTION_CODE ? 'display:none;':''?>"
            >
                <p class="parameter-option" style="margin-bottom: 0px;">with</p>
                <div class="parameter-option">
                    <?php echo CHtml::activeDropDownList($this, "[$id]treatmentTypeId", TreatmentType::getOptions(),
                        array('empty' => 'Any')); ?>
                </div>
                <p class="parameter-option">treatment</p>
            </span>
        </div>

        <script type="text/javascript">

            let DOMStrings = {
                parameterClass: '.parameter',
                trialType: '.js-trial-type',
                trialList: '.js-trial-list select',
                treatmentType: '.js-treatment-type-container'
            };

            function getDOM() {
                return DOMStrings;
            }

            // populateTrialList argument receives a boolean to specify whether we need to get the trial list or not.
            function init(target, populateTrialList = true) {
                let DOM = getDOM();
                var parameterNode = $(DOM.parameterClass + '#' + <?= $this->id ?>);

                var trialType = $(target).val();
                var trialList = parameterNode.find(DOM.trialList);
                var treatmentTypeContainer = parameterNode.find(DOM.treatmentType);

                // If user has selected Any Trial as Trial type then hide the trial list
                treatmentTypeContainer.toggle(
                    !trialType ||
                    trialType === '<?= TrialType::model()->find('code = "INTERVENTION"')->id ?>'
                );

                if (!trialType) {
                    trialList.empty();
                    trialList.hide();
                } else {
                    if(populateTrialList) {
                        $.ajax({
                            url: '<?php echo Yii::app()->createUrl('/OETrial/trial/getTrialList'); ?>',
                            type: 'GET',
                            data: {type: trialType},
                            success: function (response) {
                                trialList.empty();
                                trialList.append(response);
                                trialList.show();
                            }
                        });
                    }
                }
            }

            // Execute the function on loading the page.
            jQuery(document).ready(function(){
                var DOM = getDOM();
                init($(DOM.trialType).children(), false);
            });

            function getTrialList(target) {
                init(target);
            }

        </script>

        <?php
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @param $searchProvider SearchProvider The search provider. This is used to determine whether or not the search provider is using SQL syntax.
     * @return mixed The constructed query string.
     * @throws CHttpException
     */
    public function query($searchProvider)
    {
        $condition = ' ';
        switch ($this->operation) {
            case '=':
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

                break;
            case '!=':
                $joinCondition = 'LEFT JOIN';
                $condition = 't_p.trial_id IS NULL OR ';

                if ($this->status !== '' && $this->status !== null) {
                    if ($this->trial == '' ){
                        $condition .=
                            "  p.id NOT IN(
                          SELECT patient_id
                          FROM trial_patient
                          WHERE status_id = :p_t_status_$this->id
                      ) ";
                    }else {
                        $condition .=
                            "  p.id NOT IN(
                          SELECT patient_id
                          FROM trial_patient
                          WHERE status_id = :p_t_status_$this->id
                          AND trial_id = :p_t_trial_$this->id
                      ) ";
                    }

                } else {
                    // not accepted/rejected in any trial
                    $condition .=
                        ' p.id NOT IN(
                          SELECT patient_id
                          FROM trial_patient
                          WHERE status_id IN (SELECT id FROM trial_patient_status WHERE code IN ("ACCEPTED", "SHORTLISTED", "REJECTED")))';
                }
                if ($this->trialType) {

                    if ($this->trial === '') {
                        // Not in any intervention/non-intervention trial
                        $condition .= " OR t.trial_type_id  != :p_t_trial_type_$this->id ";
                    } else {
                        // Not in a specific trial
                        $condition .= " AND t_p.trial_id != :p_t_trial_$this->id ";
                    }
                }

                if ((!$this->trialType || $this->trialType->code !== TrialType::INTERVENTION_CODE)
                    && $this->treatmentTypeId !== '' && $this->treatmentTypeId !== null
                ) {
                    $condition .= " OR t_p.treatment_type_id IS NULL OR t_p.treatment_type_id != :p_t_treatment_type_id_$this->id";
                }

                break;
            default:
                throw new CHttpException(400, 'Invalid operator specified.');
                break;
        }

        return "
SELECT p.id 
FROM patient p 
$joinCondition trial_patient t_p 
  ON t_p.patient_id = p.id 
$joinCondition trial t
  ON t.id = t_p.trial_id
WHERE $condition";
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        $binds = array();

        if ($this->trial !== '' && $this->trial !== null) {
            $binds[":p_t_trial_$this->id"] = $this->trial;
        } elseif ($this->trialTypeId) {
            $binds[":p_t_trial_type_$this->id"] = $this->trialTypeId;
        }

        if ($this->status !== '' && $this->status !== null && $this->operation === '!=') {
            if($this->trial ==''){
                $binds[":p_t_status_$this->id"] = $this->status;
            }else {
                $binds[":p_t_status_$this->id"] = $this->status;
                $binds[":p_t_trial_$this->id"] = $this->trial;
            }
        }

        if ($this->status !== '' && $this->status !== null && $this->operation === '=') {
            $binds[":p_t_status_$this->id"] = $this->status;
        }

        if ($this->operation === '=') {
            if ((!$this->trialType || $this->trialType->code !== TrialType::NON_INTERVENTION_CODE)
                && $this->treatmentTypeId !== '' && $this->treatmentTypeId !== null
            ) {
                $binds[":p_t_treatment_type_id_$this->id"] = $this->treatmentTypeId;
            }
        } else {
            if ((!$this->trialType || $this->trialType->code !== TrialType::INTERVENTION_CODE)
                && $this->treatmentTypeId !== '' && $this->treatmentTypeId !== null
            ) {
                $binds[":p_t_treatment_type_id_$this->id"] = $this->treatmentTypeId;
            }
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
}
