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
}
