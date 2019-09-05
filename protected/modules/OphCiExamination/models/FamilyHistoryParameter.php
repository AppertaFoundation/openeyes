<?php

/**
 * Class FamilyHistoryParameter
 */
class FamilyHistoryParameter extends CaseSearchParameter implements DBProviderInterface
{
    /**
     * @var integer $relative
     */
    public $relative;

    /**
     * @var integer $side
     */
    public $side;

    /**
     * @var integer $condition
     */
    public $condition;

    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'family_history';
        $this->operation = '=';
    }

    public function getLabel()
    {
        return 'Family History';
    }

    /**
     * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
     * @return array An array of attribute names.
     */
    public function attributeNames()
    {
        return array_merge(parent::attributeNames(), array(
                'relative',
                'side',
                'condition',
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
                array('condition', 'required'),
                array('relative, side, condition', 'safe'),
            )
        );
    }

    public function renderParameter($id)
    {
        $ops = array(
            '=' => 'has',
            '!=' => 'does not have',
        );

        $relatives = CHtml::listData(OEModule\OphCiExamination\models\FamilyHistoryRelative::model()->findAll(), 'id',
            'name');
        $sides = CHtml::listData(OEModule\OphCiExamination\models\FamilyHistorySide::model()->findAll(), 'id', 'name');
        $conditions = CHtml::listData(OEModule\OphCiExamination\models\FamilyHistoryCondition::model()->findAll(), 'id',
            'name');

        ?>
      <div class="flex-layout flex-left js-case-search-param">
        <div class="parameter-option">
            <?= $this->getDisplayTitle() ?>
        </div>
            <span class="parameter-option">
                <?php echo CHtml::activeDropDownList($this, "[$id]side", $sides, array('empty' => 'Any side')); ?>
            </span>
            <span class="parameter-option">
                <?php echo CHtml::activeDropDownList($this, "[$id]relative", $relatives,
                    array('empty' => 'Any relative')); ?>
            </span>
            <span class="parameter-option">
                <?php echo CHtml::activeDropDownList($this, "[$id]operation", $ops, array('prompt' => 'Select One...')); ?>
                <?php echo CHtml::error($this, "[$id]operation"); ?>
            </span>
            <span class="parameter-option">
                <?php echo CHtml::activeDropDownList($this, "[$id]condition", $conditions,
                    array('prompt' => 'Select One...')); ?>
                <?php echo CHtml::error($this, "[$id]condition"); ?>
            </span>
        </div>

        <?php
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @param $searchProvider DBProvider The search provider. This is used to determine whether or not the search provider is using SQL syntax.
     * @return string The constructed query string.
     * @throws CHttpException
     */
    public function query($searchProvider)
    {
//        Changed query conditions because family history search in Advanced serch was working correctly for ANY values - CERA-538
        $query_side = "";
        $query_relative = "";
        $query_condition = "";

        if($this->side !=''){
            $query_side = ":f_h_side_$this->id IS NULL OR fh.side_id = :f_h_side_$this->id)";
        }
        if($this->relative != ''){
            if($query_side == ""){
                $query_relative = ":f_h_relative_$this->id IS NULL OR fh.relative_id = :f_h_relative_$this->id)";
            }else{
                $query_relative = " AND (:f_h_relative_$this->id IS NULL OR fh.relative_id = :f_h_relative_$this->id)";
            }
        }
        if( $this->condition != ''){
            if($query_side =="" && $query_relative==""){
                $query_condition = ":f_h_condition_$this->id IS NULL OR fh.condition_id = :f_h_condition_$this->id)";
            }else{
                $query_condition = " AND (:f_h_condition_$this->id IS NULL OR fh.condition_id = :f_h_condition_$this->id)";
            }
        }

        $queryStr = "
SELECT DISTINCT p.id 
FROM patient p 
JOIN patient_family_history fh
  ON fh.patient_id = p.id
WHERE (".$query_side.$query_relative.$query_condition;
          switch ($this->operation) {
            case '=':
                // Do nothing.
                break;
            case '!=':
                $queryStr = "
SELECT id
FROM patient
WHERE id NOT IN (
  $queryStr
)";
                break;
            default:
                throw new CHttpException(400, 'Invalid operator specified.');
                break;
        }

        return $queryStr;
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
//        Matched bind parameter numbers to those on the query - CERA-538
        $binds = array();
        if ($this->relative !='' || $this->relative != null){
            $binds["f_h_relative_$this->id"] = $this->relative;
        }
        if ($this->side !='' || $this->side != null){
            $binds["f_h_side_$this->id"] = $this->side;
        }
        if ($this->condition !='' || $this->condition != null){
            $binds["f_h_condition_$this->id"] = $this->condition;
        }
        return $binds;
    }

    /**
     * @inherit
     */
    public function getAuditData()
    {
        return "$this->name: $this->side $this->relative $this->operation \"$this->condition\"";
    }
}
