<?php

/**
 * Class PreviousProceduresParameter
 */
class PreviousProceduresParameter extends CaseSearchParameter implements DBProviderInterface
{
    public $has_had;
    public $textValue;

    /**
    * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
    * @param string $scenario
    */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'previous_procedures';
    }

    public function getLabel()
    {
        // This is a human-readable value, so feel free to change this as required.
        return 'Previous Procedures';
    }

    /**
    * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
    * @return array An array of attribute names.
    */
    public function attributeNames()
    {
        return array_merge(parent::attributeNames(), array(
                'has_had',
                'procedure',
            )
        );
    }

    /**
    * Override this function if the parameter subclass has extra validation rules. If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
    * @return array The validation rules for the parameter.
    */
    public function rules()
    {
        return parent::rules();
    }

    public function renderParameter($id)
    {
        $ops = array(
            'LIKE' => 'Has had a',
            'NOT LIKE' => 'Has not had a',
        );
        ?>

        <div class="flex-layout flex-left">
            <?= $this->getDisplayTitle()?>
            <div style="padding-right: 15px;">
                <?php echo CHtml::activeDropDownList($this, "[$id]operation", $ops, array('prompt' => 'Select One...')); ?>
                <?php echo CHtml::error($this, "[$id]operation"); ?>
            </div>

            <div>
                <?php
                $html = Yii::app()->controller->widget('zii.widgets.jui.CJuiAutoComplete', array(
                    'name' => $this->name . $this->id,
                    'model' => $this,
                    'attribute' => "[$id]textValue",
                    'source' => Yii::app()->controller->createUrl('AutoComplete/commonProcedures'),
                    'options' => array(
                        'minLength' => 2,
                    ),
                ), true);
                Yii::app()->clientScript->render($html);
                echo $html;
                ?>
                <?php echo CHtml::error($this, "[$id]textValue"); ?>
            </div>
        </div>
        <?php
    }

    /**
    * Generate a SQL fragment representing the subquery of a FROM condition.
    * @param $searchProvider DBProvider The search provider. This is used to determine whether or not the search provider is using SQL syntax.
    * @return string The constructed query string.
    */
    public function query($searchProvider)
    {
        // Construct your SQL query here.
        return null;
    }

    /**
    * Get the list of bind values for use in the SQL query.
    * @return array An array of bind values. The keys correspond to the named binds in the query string.
    */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        return array(
            "p_p_has_had_$this->id" => $this->has_had,
            "p_p_procedure_$this->id" => $this->textValue,
        );
    }
}
