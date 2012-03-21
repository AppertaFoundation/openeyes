<?php
class EventTypeModuleCode extends ModuleCode // CCodeModel
{
	public $moduleID;
	public $moduleSuffix;
	public $template = "default";

	public function rules() {
		return array(
			array('moduleSuffix', 'required'),
			array('moduleSuffix', 'safe'),
		);
	}

	public function prepare() {
		$this->moduleID = ucfirst(strtolower(Specialty::model()->findByPk($_REQUEST['Specialty']['id'])->code)) . ucfirst(strtolower(EventGroup::model()->findByPk($_REQUEST['EventGroup']['id'])->code)) . Yii::app()->request->getQuery('Specialty[id]') . preg_replace("/ /", "", ucfirst($this->moduleSuffix));
		parent::prepare();
	}
	public function init() {
		parent::init();
	}
}
