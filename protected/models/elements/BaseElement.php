<?php

/**
 * A class that all clinical elements should extend from.
 */
class BaseElement extends CActiveRecord
{
	// Some elements need access to the firm specialty
	public $userFirm;

	public function construct($userFirm)
	{
		$this->userFirm = $userFirm;

		parent::construct();
	}
}
