<?php
/**
 * Created by PhpStorm.
 * User: petergallagher
 * Date: 30/03/15
 * Time: 13:25
 */

class ModelSearch
{
	protected $model;
	protected $criteria;
	protected $request;
	protected $itemsPerPage = 30;
	protected $searchItems = array();
	protected $searchTerms = array();

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
	}


	public function __construct(BaseActiveRecord $model)
	{
		$this->model = $model;
		$this->criteria = new CDbCriteria();
		$this->request = $request = Yii::app()->getRequest();
		$this->generateCriteria();
	}

	protected function generateCriteria($attr = 'search')
	{
		$search = $this->request->getParam($attr);
		$sensitive = $this->request->getParam('case_sensitive', false);

		if(is_array($search)){
			foreach($search as $key => $value){
				if(!is_array($value)){
					$this->addCompare($this->criteria, $key, $value, $sensitive);
				} else {
					if(!isset($value['value'])){
						//no value provided to search against
						continue;
					}
					$searchTerm = $value['value'];
					$this->addCompare($this->criteria, $key, $searchTerm, $sensitive);
					if(array_key_exists('compare_to', $value) && is_array($value['compare_to'])){
						foreach($value['compare_to'] as $compareTo)
						{
							$this->addCompare($this->criteria, $compareTo, $searchTerm, $sensitive, 'OR');
						}
					}
				}
			}
		}
	}

	protected function addCompare(CDbCriteria $criteria, $attribute, $value, $sensitive = false, $operator = 'AND')
	{
		if(strpos($attribute, '.')){
			$relationship = explode('.', $attribute);
			$attribute = $relationship[0];
			$tableName = $this->model->$attribute->getTableName();
		}

		if($value !== '' && $this->model->hasAttribute($attribute)  ){
			if ($sensitive) {
				$criteria->compare('LOWER(' . $attribute . ')', strtolower($value), true, $operator);
			} else {
				$criteria->compare($attribute, $value, true, $operator);
			}
			$this->searchTerms[$attribute] = $value;
		}
	}

	public function initPagination()
	{
		$itemsCount = $this->model->count($this->criteria);
		$pagination = new CPagination($itemsCount);
		$pagination->pageSize = $this->itemsPerPage;
		$pagination->applyLimit($this->criteria);
		return $pagination;
	}

	public function retrieveResults()
	{
		return $this->model->findAll($this->criteria);
	}

	public function addSearchItem($key, $search = '')
	{
		$this->searchItems[$key] = $search;
	}

	public function getSearchTermForAttribute($attribute, $default = '')
	{
		if(array_key_exists($attribute, $this->searchTerms)){
			return $this->searchTerms[$attribute];
		}

		return $default;
	}
}