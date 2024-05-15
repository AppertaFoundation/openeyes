<?php

/**
 * Class PatientNumberParameter
 */
class PatientNumberParameter extends CaseSearchParameter implements DBProviderInterface
{
    public $number;

    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'patient_number';
        $this->operation = '='; // Remove if more operations are added.
    }

    public function getLabel()
    {
        // This is a human-readable value, so feel free to change this as required.
        return \SettingMetadata::model()->getSetting('hos_num_label') . ' Number';
    }

    /**
     * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
     * @return array An array of attribute names.
     */
    public function attributeNames()
    {
        return array_merge(parent::attributeNames(), array(
                'number',
            ));
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'number' => 'Value',
        ));
    }

    /**
     * Override this function if the parameter subclass has extra validation rules. If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
     * @return array The validation rules for the parameter.
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('number', 'required'),
            array('number', 'numerical'),
        ));
    }

    public function renderParameter($id)
    {
        ?>
      <div class="flex-layout flex-left js-case-search-param">
        <div class="parameter-option">
            <?= $this->getDisplayTitle() ?>
        </div>
        <div class="parameter-option">
          <?php echo CHtml::activeTextField($this, "[$id]number"); ?>
          <?php echo CHtml::error($this, "[$id]number"); ?>
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
        /*
        // Reimplement this code if more operation choices are added to this parameter type.
         if ($this->operation === '=') {
            $op = '=';
        } else {
            throw new CHttpException(400, 'Invalid operator specified.');
        }*/

        return "SELECT DISTINCT p.id 
FROM patient p
WHERE p.hos_num $op :p_num_number_$this->id";
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        return array(
            "p_num_number_$this->id" => $this->number,
        );
    }

    /**
     * @inherit
     */
    public function getAuditData()
    {
        return "$this->name: $this->operation $this->number";
    }
}
