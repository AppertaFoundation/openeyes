<?php

class GenericAdmin extends BaseCWidget
{
	public $model;
	public $items;
	public $errors;
	public $label_field;
	public $label_relation;
	public $label_field_type;
	public $label_field_model;
	public $new_row_url;
	public $extra_fields = array();
	public $filter_fields;
	public $filter_values;
	public $filters_ready;
	public $get_row = false;
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
