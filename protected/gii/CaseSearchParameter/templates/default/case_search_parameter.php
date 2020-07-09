<?php
echo '<?php'; ?>

/**
 * Class <?php echo $this->className; ?>Parameter
 */
class <?php echo $this->className; ?>Parameter extends CaseSearchParameter implements <?php echo str_replace(',', 'Interface, ', $this->searchProviders) . 'Interface'; ?>

{
<?php if (!empty($this->attributeList)) :
    foreach (explode(',', $this->attributeList) as $attribute) :?>
    public $<?php echo $attribute; ?>;
    <?php endforeach;
endif; ?>

    /**
    * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
    * @param string $scenario
    */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = '<?php echo str_replace(' ', '_', strtolower($this->name)); ?>';
    }

    public function getLabel()
    {
        // This is a human-readable value, so feel free to change this as required.
        return '<?php echo $this->name; ?>';
    }

    /**
    * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
    * @return array An array of attribute names.
    */
    public function attributeNames()
    {
        return array_merge(parent::attributeNames(), array(
<?php if (!empty($this->attributeList)) :
    foreach (explode(',', $this->attributeList) as $attribute) :?>
                '<?php echo $attribute; ?>',
    <?php endforeach;
endif; ?>
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
        // Initialise any rendering variables here.
        ?>
        <!-- Place screen-rendering code here. -->
        <?php
        echo '<?php'; ?>

    }

<?php foreach (explode(',', $this->searchProviders) as $searchProvider) :?>
    <?php if ($searchProvider === 'DBProvider') :?>
    /**
    * Generate a SQL fragment representing the subquery of a FROM condition.
    * @param $searchProvider <?php echo $searchProvider; ?> The search provider. This is used to determine whether or not the search provider is using SQL syntax.
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
            "<?php echo $this->alias ?>_<?php echo $attribute; ?>_$this->id" => $this-><?php echo $attribute; ?>,
            <?php endforeach;
        endif;?>
        );
    }
    <?php endif;?>
<?php endforeach;?>
}
