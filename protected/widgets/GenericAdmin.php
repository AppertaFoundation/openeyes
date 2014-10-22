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

}
