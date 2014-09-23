<?php

class GenericAdmin extends BaseCWidget
{
	public $model;
	public $data;
	public $errors;
	public $extra_fields = array();
	public $has_default = false;

	public function init()
	{
		$model = $this->model;

		if (!$this->extra_fields) {
			$this->extra_fields = array();
		}

		if (empty($_POST['id'])) {
			$this->data = $model::model()->findAll(array('order'=>'display_order asc'));
		} else {
			$this->data = array();

			foreach ($_POST['id'] as $i => $id) {
				$item = new $model;
				$item->id = $id;
				$item->name = $_POST['name'][$i];
				$attributes = $item->getAttributes();
				if (array_key_exists('active',$attributes)) {
					$item->active = (isset($_POST['active'][$i]) || intval($id) == 0)? 1 : 0;
				}

				foreach ($this->extra_fields as $field) {
					$item->{$field['field']} = $_POST[$field['field']][$i];
				}

				$this->data[] = $item;
			}
		}

		if ($model::model()->hasAttribute('default')) {
			foreach ($this->data as $item) {
				if ($item->default) {
					$this->has_default = true;
				}
			}
		}

		return parent::init();
	}
}
