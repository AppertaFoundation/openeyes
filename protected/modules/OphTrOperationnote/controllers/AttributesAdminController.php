<?php


use OEModule\OphCiExamination\models\OphCiExamination_Attribute;

class AttributesAdminController extends BaseAdminController
{
	/**
	 * @var string
	 */
	//public $layout = 'admin';

	/**
	 * @var int
	 */
	public $itemsPerPage = 100;

	public $group = 'Operation note';

	public function actionList()
	{
		$admin = new Admin(OphTrOperationnote_Attribute::model(), $this);

		$admin->setListFields(array(
			'display_order',
			'name',
			'label',
			'procedure.term',
			'is_multiselect',
			'getItemsAdminLink'
		));
		$admin->searchAll();
		$admin->setModelDisplayName('Operation note attributes');
		$admin->div_wrapper_class = 'cols-12';
		//$admin->getSearch()->addActiveFilter();
		$admin->getSearch()->setItemsPerPage($this->itemsPerPage);
		$admin->listModel();
	}

	public function actionEdit($id = false)
	{
		$admin = new Admin(OphTrOperationnote_Attribute::model(), $this);
		//$admin->setCustomSaveURL('/oeadmin/ExaminationElementAttributes/update');
		if ($id) {
			$admin->setModelId($id);
		}
		$admin->setModelDisplayName('Operation note attribute');

		$admin->setEditFields(array(
			//'id' => 'label',
			'name' => 'text',
			'label' => 'text',
			'proc_id' => array(
				'widget' => 'DropDownList',
				'options' => CHtml::listData(Procedure::model()->findAll(array('order'=>'term')), 'id', 'term'),
				'htmlOptions' => null,
				'hidden' => false,
				'layoutColumns' => null,
			),
			'is_multiselect' => 'checkbox'
		));

		$admin->editModel();
	}
}