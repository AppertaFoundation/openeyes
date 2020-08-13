<?= '<?php' ?>

/**
 * Class <?= $this->className ?>Parameter
 */
class <?= $this->className ?>Parameter extends CaseSearchParameter implements <?= str_replace(',', 'Interface, ', $this->searchProviders) . 'Interface' ?>

{
<?php if (!empty($this->attributeList)) :
    foreach (explode(',', $this->attributeList) as $attribute) :?>
    public $<?= $attribute ?>;
    <?php endforeach;
endif; ?>

    /**
    * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
    * @param string $scenario
    */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = '<?= str_replace(' ', '_', strtolower($this->name)) ?>';
    }

    public function getLabel()
    {
        // This is a human-readable value, so feel free to change this as required.
        return '<?= $this->name ?>';
    }

    /**
    * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
    * @return array An array of attribute names.
    */
    public function attributeNames()
    {
        return array_merge(
            parent::attributeNames(),
            array(
<?php if (!empty($this->attributeList)) :
    foreach (explode(',', $this->attributeList) as $attribute) :?>
                '<?= $attribute ?>',
    <?php endforeach;
endif; ?>        )
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

<?php foreach (explode(',', $this->searchProviders) as $searchProvider) :?>
    <?php if ($searchProvider === 'DBProvider') :?>
    /**
    * Generate a SQL fragment representing the subquery of a FROM condition.
    * @param $searchProvider <?= $searchProvider ?> The search provider. This is used to determine whether or not the search provider is using SQL syntax.
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
        <?php if (!empty($this->attributeList)) :
            foreach (explode(',', $this->attributeList) as $attribute) :?>
    "<?= $this->alias ?>_<?= $attribute ?>_$this->id" => $this-><?= $attribute ?>,
            <?php endforeach;
        endif;?>
);
    }
    <?php endif;?>

    public function getAuditData()
    {
        return "insert audit string here";
    }

    public function saveSearch()
    {
        return array_merge(
            parent::saveSearch(),
            array(
                // Add all additional properties here.
    <?php if (!empty($this->attributeList)) :
        foreach (explode(',', $this->attributeList) as $attribute) :?>
            '<?= $attribute ?>' => $this-><?= $attribute ?>,
        <?php endforeach;
    endif; ?>    )
        );
    }

    public function getDisplayString()
    {
        return 'insert display string content here'
    }
<?php endforeach;?>
}
