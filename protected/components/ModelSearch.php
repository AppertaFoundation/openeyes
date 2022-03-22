<?php
/**
 * Created by PhpStorm.
 * User: petergallagher
 * Date: 30/03/15
 * Time: 13:25.
 */

/**
 * ModelSearch class allows for generic searching of a model.
 *
 * With the ModelSearch class it is possible to specify through paramaters which attributes for a model should be
 * searchable and how. It will then use GenericSearch widget to output a form in a view based on the same
 * configuration. To make a model attribute searchable it needs to be added to the searchItems array.
 *
 * It is possible to assign an array of configuration options to this entry in searchItems which will change how the
 * database is queried. For example an array containing type of compare and compare_to which has an array of other
 * attributes of the model will cause the query to be made against all attributes listed. eg:
 *
 * 	$search->addSearchItem('name', array(
 * 		'type' => 'compare'
 * 		'compare_to' => array(
 * 			'pas_code',
 * 			'consultant.first_name',
 * 			'consultant.last_name',
 * 		)
 *	));
 *
 * If the type is set to boolean the user will be presented with a drop down to include all results or include only one
 * or exclude only those results.
 *
 * As you can see this works with across relationships, however for clarity in output you need to add the labels to
 * the attributeLabels array of the model ModelSearch was instantiated with.
 */
class ModelSearch
{
    /**
     * @var BaseActiveRecord
     */
    protected $model;

    /**
     * @var CDbCriteria
     */
    protected $criteria;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var int
     */
    protected $itemsPerPage = 30;

    /**
     * @var array
     */
    protected $searchItems = array();

    /**
     * @var array
     */
    protected $searchTerms = array();

    /**
     * @var bool
     */
    protected $defaultResults = true;

    /**
     * @var bool
     */
    protected $searching = false;

    /**
     * @return BaseActiveRecord
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param BaseActiveRecord $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return CDbCriteria
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param CDbCriteria $criteria
     */
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * @param int $itemsPerPage
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * @return array
     */
    public function getSearchItems()
    {
        return $this->searchItems;
    }

    /**
     * @param array $searchItems
     */
    public function setSearchItems($searchItems)
    {
        $this->searchItems = $searchItems;
        foreach ($this->searchItems as $searchTerm => $searchItem) {
            if (is_array($searchItem) && array_key_exists('default', $searchItem) && !array_key_exists($searchTerm, $this->searchTerms)) {
                $criteria = $this->getCriteria();
                $criteria->addCondition($searchTerm.' = '.$searchItem['default']);
            }
        }
    }

    /**
     * @return bool
     */
    public function isDefaultResults()
    {
        return $this->defaultResults;
    }

    /**
     * @param bool $defaultResults
     */
    public function setDefaultResults($defaultResults)
    {
        $this->defaultResults = $defaultResults;
    }

    /**
     * @return bool
     */
    public function isSearching()
    {
        return $this->searching;
    }

    /**
     * @param bool $searching
     */
    public function setSearching($searching)
    {
        $this->searching = $searching;
    }



    /**
     * @param BaseActiveRecord $model
     */
    public function __construct(BaseActiveRecord $model)
    {
        $this->model = $model;
        $this->criteria = new CDbCriteria();
        $this->criteria->with = array();
        $this->request = Yii::app()->getRequest();
        $this->generateCriteria();
    }

    /**
     * Generates the required Criteria object for the search.
     *
     * @param string|array $attr
     */
    protected function generateCriteria($attr = 'search')
    {
        if (is_array($attr)) {
            $search = $attr;
            $sensitive = false;
        } else {
            $search = $this->request->getParam($attr);
            $sensitive = $this->request->getParam('case_sensitive', false);
        }

        if (is_array($search)) {
            $this->searching = true;

            foreach ($search as $key => $value) {

                if ($key === 'exact') {
                    continue;
                }

                if ($key === 'precision' ){
                    if (is_array($value)){
                        foreach($value as $_key => $_value){
                            $this->addCompare($this->criteria, $_key, $_value, $sensitive, 'AND', true);
                        }
                    }
                    continue;
                }

                $exactMatch = (isset($search['exact'][$key]) && $search['exact'][$key]);
                if (!is_array($value)) {
                    $this->addCompare($this->criteria, $key, $value, $sensitive, 'AND', $exactMatch);
                } else {
                    if ($key === 'filterid') {
                        foreach ($value as $fieldName => $fieldValue) {
                            if ($fieldValue > 0) {
                                $this->addCompare($this->criteria, $fieldName, $fieldValue, $sensitive, 'AND', true);
                            }
                        }
                    }
                    if (!isset($value['value'])) {
                        //no value provided to search against
                        continue;
                    }
                    $searchTerm = $value['value'];
                    $this->addCompare($this->criteria, $key, $searchTerm, $sensitive, 'AND', $exactMatch);
                    if (array_key_exists('compare_to', $value) && is_array($value['compare_to'])) {
                        foreach ($value['compare_to'] as $compareTo) {
                            $this->addCompare($this->criteria, $compareTo, $searchTerm, $sensitive, 'OR', $exactMatch);
                        }
                    }
                }
            }
        }

        if ($this->model->hasAttribute('display_order')) {
            $this->criteria->order = 't.display_order asc';
        } else {
            $order = $this->request->getParam('d');
            $sortColumn = $this->request->getParam('c');
            if ($sortColumn) {
                $this->relationalAttribute($this->criteria, $sortColumn, $attr);

                if ($order) {
                    $sortColumn .= ' DESC';
                }
                $sortPrefix = strpos($sortColumn, '.');
                $this->criteria->order = ($sortPrefix === false ? 't.' : '') . $sortColumn;
            }
        }
    }

    /**
     * Adds a comparison between a search term and an attribute.
     *
     * @param CDbCriteria $criteria
     * @param $attribute
     * @param $value
     * @param bool   $sensitive
     * @param string $operator
     */
    protected function addCompare(
        CDbCriteria $criteria,
        $attribute,
        $value,
        $sensitive = false,
        $operator = 'AND',
        $exactmatch = false
    ) {
        if (method_exists($this->model, 'get_'.$attribute)) {
            //It's a magic method attribute, doesn't exist in the db has to be dealt with elsewhere
            return;
        }

        if (method_exists($this->model, $attribute) ){
            $compareArguments = $this->model->{$attribute}();
            $criteria->compare($compareArguments['field'], $value, $compareArguments['exactmatch'], $compareArguments['operator']);
            return;
        }

        $search = $attribute;
        $search = $this->relationalAttribute($criteria, $attribute, $search);

        if ($value !== '') {
            if (!$sensitive && !$exactmatch) {
                $criteria->compare('LOWER('.$search.')', strtolower($value), true, $operator);
            } elseif ($exactmatch) {
                $criteria->compare($search, $value, false, $operator);
            } else {
                $criteria->compare($search, $value, true, $operator);
            }
            $this->searchTerms[$attribute] = $value;
        }
    }

    /**
     * Inits pagination for the results and returns it.
     *
     * @return CPagination
     */
    public function initPagination()
    {
        $itemsCount = $this->model->count($this->criteria);
        $pagination = new CPagination($itemsCount);
        $pagination->pageSize = $this->itemsPerPage;
        $pagination->applyLimit($this->criteria);

        return $pagination;
    }

    /**
     * Performs the query that has been generated.
     *
     * @return CActiveRecord[]
     */
    public function retrieveResults()
    {
        if (!$this->defaultResults && !$this->searching){
            return array();
        }

        return $this->model->findAll($this->criteria);
    }

    /**
     * Add a filter for active
     */
    public function addActiveFilter()
    {
        $this->addSearchItem('active', array('type' => 'boolean'));
    }

    /**
     * Add a search item.
     *
     * @param $key
     * @param string|array $search
     */
    public function addSearchItem($key, $search = '')
    {
        $this->searchItems[$key] = $search;
        if (is_array($search) && array_key_exists('default', $search) && !array_key_exists($key, $this->searchTerms)) {
            $criteria = $this->getCriteria();
            $criteria->addCondition($key . ' = ' . $search['default']);
        }
    }

    /**
     * @param $searchInput
     */
    public function initSearch($searchInput)
    {
        $this->generateCriteria($searchInput);
    }

    /**
     * Retrieves the search term supplied by the user for a given attribute if there was one.
     *
     * @param $attribute
     * @param string $default
     *
     * @return string
     */
    public function getSearchTermForAttribute($attribute, $default = '')
    {
        if (array_key_exists($attribute, $this->searchTerms)) {
            return $this->searchTerms[$attribute];
        }

        return $default;
    }

    /**
     * Takes an attribute name and makes sure appropriate relationships are included.
     *
     * This will take an attribute name many layers of relationship deep, make sure that all appropriate tables are
     * included with the result and return a string that is then acceptable to be used in a where clause.
     *
     * @param CDbCriteria $criteria
     * @param string      $attribute
     * @param string      $search
     *
     * @return string
     */
    protected function relationalAttribute(CDbCriteria $criteria, $attribute, $search)
    {
        $search = $this->model->getTableAlias().'.'.$search;

        if (strpos($attribute, '.')) {
            $relationship = explode('.', $attribute);
            $relationshipArray = array();
            while (count($relationship) > 1) {
                $relationshipString = array_shift($relationship);
                $search = $relationshipString;
                if (count($relationshipArray)) {
                    $relationshipString = implode('.', $relationshipArray).'.'.$relationshipString;
                }
                $relationshipArray[] = $relationshipString;
            }

            $search .= '.'.array_shift($relationship);

            $criteria->together = true;
            $criteria->with = array_merge(
                $criteria->with,
                $relationshipArray
            );
        }

        return $search;
    }
}
