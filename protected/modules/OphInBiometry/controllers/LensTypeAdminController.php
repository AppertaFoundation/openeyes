<?php
/**
 * Created by PhpStorm.
 * User: veta
 * Date: 06/05/15
 * Time: 11:30
 */

class LensTypeAdminController  extends BaseAdminController
{

	/**
	 * @var int
	 */
	public $itemsPerPage = 100;

	/**
	 * Lists lens types
	 *
	 * @throws CHttpException
	 */
	public function actionList()
	{
		$admin = new Admin(OphInBiometry_LensType_Lens::model(), $this);
		$admin->setListFields(array(
			'id',
			'name',
			'description',
			'acon',
			'active'
		));
		$admin->setModelDisplayName('Lens types');
		$admin->searchAll();
		$admin->getSearch()->addActiveFilter();
		$admin->getSearch()->setItemsPerPage($this->itemsPerPage);
		$admin->listModel();
	}

	/**
	 * Edits or adds a lens type
	 *
	 * @param bool|int $id
	 * @throws CHttpException
	 */
	public function actionEdit($id = false)
	{
		$admin = new Admin(OphInBiometry_LensType_Lens::model(), $this);
		if($id){
			$admin->setModelId($id);
		}
		$admin->setModelDisplayName('Lens types');
		$admin->setEditFields(array(
			'name' => 'text',
			'description' => 'text',
			'acon' => 'text',
			'active' => 'checkbox'
		));
		$admin->editModel();
	}

	/**
	 * Deletes rows for the model
	 */
	public function actionDelete()
	{
		$admin = new Admin(OphInBiometry_LensType_Lens::model(), $this);
		$admin->deleteModel();
	}

}