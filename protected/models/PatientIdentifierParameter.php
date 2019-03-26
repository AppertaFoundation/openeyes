<?php

/**
 * Class PatientIdentifierParameter
 */
class PatientIdentifierParameter extends CaseSearchParameter implements DBProviderInterface
{
    public $textValue;

    public $code;

    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'patient_identifier';
        $this->operation = '='; // Remove if more operations are added.
    }

    public function getLabel()
    {
        // This is a human-readable value, so feel free to change this as required.
        return 'Patient Identifier Number';
    }

    /**
     * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
     * @return array An array of attribute names.
     */
    public function attributeNames()
    {
        return array_merge(parent::attributeNames(), array(
                'code',
                'textValue',
            )
        );
    }
    /**
     * Attribute labels for display purposes.
     * @return array Attribute key/value pairs.
     */
    public function attributeLabels()
    {
        return array(
            'textValue' => 'Value',
            'code' => 'Code'
        );
    }

    /**
     * Override this function if the parameter subclass has extra validation rules. If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
     * @return array The validation rules for the parameter.
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('textValue', 'required'),
            array('code', 'required'),
            array('code','safe')
        ));
    }

    public function renderParameter($id)
    {
        ?>
        <div class="flex-layout flex-left">
            <?= $this->getDisplayTitle() ?>
            <div class="parameter-option">
                <?php echo CHtml::activeDropDownList(
                    $this,
                    "[$id]code",
                    $this->getAllCodes(),
                    array('onchange' => 'refreshValues(this)','prompt' => 'Select One...', 'class' => 'js-code')
                ); ?>
                <?php echo CHtml::error($this, "[$id]code"); ?>
            </div>

            <div class="parameter-option">
                <?php echo CHtml::activeTextField($this, "[$id]textValue"); ?>
                <?php echo CHtml::error($this, "[$id]textValue"); ?>
            </div>
        </div>
        <?php
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @param $searchProvider DBProvider The database search provider.
     * @return string The constructed query string.
     * @throws CHttpException
     */
    public function query($searchProvider)
    {
        $op = '=';
        return "SELECT DISTINCT p.patient_id 
FROM patient_identifier p
WHERE p.code $op :p_code_$this->id AND p.value $op :p_id_number_$this->id";
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        return array(
            "p_id_number_$this->id" => $this->textValue,
            "p_code_$this->id" => $this->code,
        );
    }

    /**
     * @inherit
     */
    public function getAuditData()
    {
        return "$this->name: $this->operation $this->code $this->textValue";
    }
    /**
     * @return array contains all identifier codes
     */
    public function getAllCodes(){
        $command = Yii::app()->db->createCommand('SELECT DISTINCT code FROM patient_identifier');
        $all_codes = $command->queryAll();
        $codes = array();
        foreach ($all_codes as $code){
            $codes[$code['code']] = $code['code'];
        }
        return $codes;
    }
}
