<?= '<?php' ?>

class <?= $this->className ?>Variable extends CaseSearchVariable implements <?= str_replace(',', 'Interface, ', $this->searchProviders) . 'Interface' ?>

{
    public function __construct($id_list)
    {
        parent::__construct($id_list);
        $this->field_name = '<?= $this->name ?>';
        $this->label = '<?= $this->label ?>';
        $this->unit = '<?= $this->unit ?>';
<?php if ($this->eyeCardinality) : ?>
        $this->eye_cardinality = true;
<?php endif; ?>
    }

<?php foreach (explode(',', $this->searchProviders) as $searchProvider) :?>
    <?php if ($searchProvider === 'DBProvider') :?>
    public function query($searchProvider)
    {
        // TODO: Return a query string here.
        return null;
    }

    public function bindValues()
    {
        // TODO: Return an array of  bind mappings here (if applicable)
        return array();
    }
    <?php endif; ?>
<?php endforeach; ?>
}
