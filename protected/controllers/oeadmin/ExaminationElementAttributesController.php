<?php
/**
 * Created by PhpStorm.
 * User: himanshu
 * Date: 12/05/15
 * Time: 15:02
 */

class ExaminationElementAttributesController extends BaseAdminController
{
	/**
	 * @var string
	 */
	public $layout = 'admin';

	/**
	 * @var int
	 */
	public $itemsPerPage = 100;

	/**
	 * Lists procedures
	 *
	 * @throws CHttpException
	 */
	public function actionList1()
	{
		error_reporting(E_ALL);
		ini_set('display_errors', 1);


		$admin = new Admin(OEModule\OphCiExamination\models\OphCiExamination_AttributeElement::model(), $this);

		$admin->setListFields(array(
			//'id',
			//'attribute_id',
			'attribute.name',
			'attribute.label',
			'element_type.name'
		));
		$admin->searchAll();
		$admin->setModelDisplayName('Element Attributes');
		$admin->getSearch()->addActiveFilter();
		$admin->getSearch()->setItemsPerPage($this->itemsPerPage);
		$admin->listModel();

	}

	public function actionList()
	{
		$admin = new Admin(OEModule\OphCiExamination\models\OphCiExamination_Attribute::model(), $this);


		$admin->setListFields(array(
			//'id',
			'name',
			'label',
			//'element_type_name',
			//'element_type_id',
			'attribute_elements_id.id',
			'attribute_elements.name'
			//'element_type.name'
		));
		$admin->searchAll();
		$admin->setModelDisplayName('Element Attributes');
		$admin->getSearch()->addActiveFilter();
		$admin->getSearch()->setItemsPerPage($this->itemsPerPage);
		$admin->listModel();

	}
	/**
	 * Edits or adds a Procedure
	 *
	 * @param bool|int $id
	 * @throws CHttpException
	 */
	public function actionEdit($id = false)
	{
		$admin = new Admin(OEModule\OphCiExamination\models\OphCiExamination_Attribute::model(), $this);
		$admin->setCustomSaveURL('/oeadmin/ExaminationElementAttributes/update');
		if ($id) {
			$admin->setModelId($id);
		}
		$admin->setModelDisplayName('Element Attributes');


		//var_dump($admin);
/*
		$criteria = new CDbCriteria();

		$params[':attribute_id'] = $id;

		//$criteria->order = 'name';
		$criteria->select = 'element_type_id';
		$criteria->params = $params;*/

		$criteria = new CDbCriteria();

		$admin->setEditFields(array(
			'id' => 'label',
			'name' => 'text',
			'label' => 'text',
			//'attribute_id' => 'text',
			//'attribute_id' => 'text',
			'attribute_elements' => array(
				'widget' => 'DropDownList',
				'options' => CHtml::listData(ElementType::model()->findAll(),'id', 'name'),
				/*'options' => CHtml::listData(
					ElementType::model()->findAll(),
					'id',
					'name',
					array('options' => array(
						'40'=>array(
							'selected'=>true
							)
						)
					)
				),*/
				'htmlOptions' => null,
				'hidden' => false,
				'layoutColumns' => null
			),
/*			'attribute_elements' => array(
				'widget' => 'MultiSelectList',
				'relation_field_id' => 'id',
				'label' => 'Attribute Elements',
				'options' => CHtml::encodeArray(CHtml::listData(
					ElementType::model()->findAll($criteria->condition = "name != 'Other'"),
					'id',
					'name'
				),array('options' => array(
					'40'=>array(
						'selected'=>true
					)
				)
				)),
			)*/

		));

//'htmlOptions' => array('selected' => OEModule\OphCiExamination\models\OphCiExamination_AttributeElement::model()->find(new CDbCriteria(array('attribute_id'=>$id)))->element_type_id),
		//'htmlOptions' => array('selected' => OEModule\OphCiExamination\models\OphCiExamination_AttributeElement::model()->active()->findAll($criteria)),
		//'htmlOptions' => array('selected' => array('value' => 40)),
		//array('40'=>array('selected'=>true))




		$admin->editModel();
	}


	public function actionAdd1($id = false)
	{

		$admin = new Admin(OEModule\OphCiExamination\models\OphCiExamination_AttributeElement::model(), $this);
		if ($id) {
			$admin->setModelId($id);

		}
		$admin->setModelDisplayName('Element Attributes');

		$admin->setEditFields(array(
			'id' => 'label',
			'name' => 'text',
			'element_type_id' => array(
				'widget' => 'DropDownList',
				'options' => CHtml::listData(ElementType::model()->findAll(),'id', 'name'),
				'htmlOptions' => null,
				'hidden' => false,
				'layoutColumns' => null
			)

		));

		$admin->editModel();
	}


	public function actionEdit1($id = false)
	{


		$admin = new Admin(OEModule\OphCiExamination\models\OphCiExamination_Attribute::model(), $this);
		$admin->setCustomSaveURL('/oeadmin/ExaminationElementAttributes/update');


		$returnUri = $admin->generateReturnUrl(Yii::app()->request->requestUri);

		//$admin->returnUriEdit('ggg');

		if ($id) {
			$admin->setModelId($id);

		}
		$admin->setModelDisplayName('Element Attributes');
		$criteria = new CDbCriteria();
		$admin->setEditFields(array(
			'id' => 'label',
			'name' => 'text',
			'label' => 'text',
		));
		$admin->editModel();
	}

	public function actionUpdate()
	{
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		$newOCEA = new OEModule\OphCiExamination\models\OphCiExamination_Attribute();
		$newOCEAE = new OEModule\OphCiExamination\models\OphCiExamination_AttributeElement();



		$post = Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExamination_Attribute');

		$attributeId = $post["id"];
		$attributeName = $post["name"];
		$attributeLabel = $post["label"];
		$attributeElements = $post["attribute_elements"];


		$count = $newOCEA::model()->countByAttributes(array(
			'name' => $attributeName,
			'label' => $attributeLabel
		));

		if (!isset($attributeElements)) {
			$newOCEA->name = $attributeName;
			$newOCEA->label = $attributeLabel;

			if ($newOCEA->save()) {

				echo "success";
				Yii::app()->request->redirect('list');

			} else {
				echo "error1";
				print_r($newOCEA->getErrors(), true);
			}
		} else {
			//Add New
			if ($attributeId ==  "")
			{
				$newOCEA->name = $attributeName;
				$newOCEA->label = $attributeLabel;

				if ($newOCEA->save()) {

					$newAttributeId = Yii::app()->db->getLastInsertID();

					$newOCEAE->attribute_id = $newAttributeId;
					$newOCEAE->element_type_id = $attributeElements;

					if($newOCEAE->save())
					{
						echo "success";
					}
					else
					{
						echo "error";
						print_r($newOCEA->getErrors(), true);
					}

					Yii::app()->request->redirect('list');

				} else {
					echo "error";
					print_r($newOCEA->getErrors(), true);
				}

			} else {
				//Edit
				$attribute = $newOCEA::model()->findByPk($attributeId);
				$attribute->name = $attributeName;
				$attribute->label = $attributeLabel;

				if ($attribute->save()) {


					$element = $newOCEAE::model()->findByAttributes(array("attribute_id" => $attributeId));
					if (is_object($element)) {
						$element->element_type_id = $attributeElements;
						$element->save();
					}

					echo "success";
					Yii::app()->request->redirect('list');
				} else {
					echo "error1";
					print_r($newOCEA->getErrors(), true);
				}
			}
		}

	}

	/**
	 * Deletes rows for the model
	 */
	public function actionDelete()
	{
		$post = Yii::app()->request->getPost('OEModule\OphCiExamination\models\OphCiExamination_Attribute');

		$attributeIdsArray = $post["id"];

		$newOCEA = new OEModule\OphCiExamination\models\OphCiExamination_Attribute();
		$newOCEAE = new OEModule\OphCiExamination\models\OphCiExamination_AttributeElement();

		foreach($attributeIdsArray as $key=>$attributeId) {

			$element = $newOCEAE::model()->findByAttributes(array("attribute_id" => $attributeId));
			if (is_object($element)) {
				if ($element->delete()) {
					//echo success;
				} else {
					echo "error";
					print_r($element->getErrors(), true);
				}
			}

			$attribute = $newOCEA::model()->findByAttributes(array("id" => $attributeId));
			if (is_object($attribute)) {
				if ($attribute->delete()) {
					echo true;
				} else {
					echo "error";
					print_r($attribute->getErrors(), true);
				}
			}
		}
	}
}