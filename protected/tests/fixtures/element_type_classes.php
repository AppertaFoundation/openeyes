<?php

class _WrapperCActiveRecordMetaData extends CActiveRecordMetaData
{
	public function __construct($model)
	{
		// potentially parse attributes and set defaults
	}

}

class _TestEventTypeElement extends BaseEventTypeElement
{

	public $attributes;

	public function __construct($attributes = array())
	{
		$this->attributes = $attributes;
	}

	public function getMetaData()
	{
		return new _WrapperCActiveRecordMetaData($this);
	}
}
class HistoryElementType extends _TestEventTypeElement
{
}
class PastHistoryElementType extends _TestEventTypeElement
{
}
class VisualFunctionElementType extends _TestEventTypeElement
{
}
class VisualAcuityElementType extends _TestEventTypeElement
{
}
