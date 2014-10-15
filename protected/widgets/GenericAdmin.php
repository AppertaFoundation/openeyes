<?php

class GenericAdmin extends BaseCWidget
{
	public $model;
	public $items;
	public $errors;
	public $label_field;
	public $extra_fields = array();
	public $filter_fields;
	public $filter_values;
	public $filters_ready;
	public $has_default = false;

	public function init()
	{
		$model = $this->model;

		if (!$this->extra_fields) {
			$this->extra_fields = array();
		}

		if ($model::model()->hasAttribute('default')) {
			foreach ($this->items as $item) {
				if ($item->default) {
					$this->has_default = true;
				}
			}
		}

		return parent::init();
	}
}
