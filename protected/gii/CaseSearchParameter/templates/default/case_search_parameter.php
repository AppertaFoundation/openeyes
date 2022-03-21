<?= '<?php' ?>

/**
 * Class <?= $this->className ?>Parameter
 */
class <?= $this->className ?>Parameter extends CaseSearchParameter implements <?= str_replace(',', 'Interface, ', $this->searchProviders) . 'Interface' ?>

{
    protected string $label_ = '<?= $this->name ?>';
    protected array $options = array(
        'value_type' => '<?= $this->type ?>',
        'operations' => array(
            array('label' => 'IS', 'id' => '='),
            array('label' => 'IS NOT', 'id' => '!='),
<?php if ($this->type === 'number') : ?>
            array('label' => 'IS LESS THAN', 'id' => '<'),
            array('label' => 'IS MORE THAN', 'id' => '>')
<?php endif; ?>
        )
    );
<?php if (!empty($this->attributeList)) {
    foreach (explode(',', $this->attributeList) as $attribute) {?>
    public $<?= $attribute ?>;
    <?php }
} ?>

    /**
    * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
    * @param string $scenario
    */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = '<?= str_replace(' ', '_', strtolower($this->name)) ?>';
    }
<?php if (!empty($this->attributeList)) : ?>
    /**
    * Override this function if the parameter subclass has extra validation rules. If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
    * @return array The validation rules for the parameter.
    */
    public function rules()
    {
        return parent::rules();
    }
<?php endif; ?>

<?php if ($this->type === 'string_search' || $this->type === 'multi_select') : ?>
    /**
    * @param string $attribute
    * @return mixed|void
    * @throws CException
    */
    public function getValueForAttribute(string $attribute)
    {
        // Add customisation of display value here.
        return parent::getValueForAttribute($attribute);
    }
<?php endif; ?>

<?php if ($this->type === 'string_search') : ?>
    public static function getCommonItemsForTerm(string $term) : array
    {
        // Add customisation here
        return array();
    }
<?php endif; ?>

<?php foreach (explode(',', $this->searchProviders) as $searchProvider) :?>
    <?php if ($searchProvider === 'DBProvider') :
        ?>/**
    * Generate a SQL fragment representing the subquery of a FROM condition.
    * @param $searchProvider <?= $searchProvider ?> The search provider. This is used to determine whether or not the search provider is using SQL syntax.
    * @return string The constructed query string.
    */
    public function query($searchProvider) : string
    {
        // Construct your SQL query here.
        return '';
    }

    /**
    * Get the list of bind values for use in the SQL query.
    * @return array An array of bind values. The keys correspond to the named binds in the query string.
    */
    public function bindValues() : array
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        return array(
        <?php if (!empty($this->attributeList)) :
            foreach (explode(',', $this->attributeList) as $attribute) :?>
    "<?= $this->alias ?>_<?= $attribute ?>_$this->id" => $this-><?= $attribute ?>,
            <?php endforeach;
        endif;?>);
    }
    <?php endif;?>

    public function getAuditData() : string
    {
        return "insert audit string here";
    }

    <?php if (!empty($this->attributeList)) : ?>
    public function saveSearch() : array
    {
        return array_merge(
            parent::saveSearch(),
            array(
                // Add all additional properties here.
        <?php foreach (explode(',', $this->attributeList) as $attribute) :?>
            '<?= $attribute ?>' => $this-><?= $attribute ?>,
        <?php endforeach;?>
            )
        );
    }
    <?php endif; ?>

<?php endforeach;?>
}
