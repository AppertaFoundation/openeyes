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
		/*		$admin->setEditFields(array(
					'id' => 'label',
					'attribute_id' =>  array(
						'widget' => 'DropDownList',
						'options' => CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_Attribute::model()->findAll(),'id', 'name'),
						'htmlOptions' => null,
						'hidden' => false,
						'layoutColumns' => null
					),
					//'attribute.name' => 'text',
					'element_type_id' => array(
						'widget' => 'DropDownList',
						'options' => CHtml::listData(ElementType::model()->findAll(),'id', 'name'),
						'htmlOptions' => null,
						'hidden' => false,
						'layoutColumns' => null
					)

				));*/

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

//		echo 'Add';die;

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
/*
		$this->request = Yii::app()->getRequest();*/

		$newOCEA = new OEModule\OphCiExamination\models\OphCiExamination_Attribute();

		//$this->modelName = 'OEModule\OphCiExamination\models\OphCiExamination_Attribute';


		//$post = $_POST['OEModule_OphCiExamination_models_OphCiExamination_Attribute'];
		$post = Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExamination_Attribute');

		$attributeId = $post["id"];
		$attributeName = $post["name"];
		$attributeLabel = $post["label"];
		$attributeElements = $post["attribute_elements"];


			$count = $newOCEA::model()->countByAttributes(array(
				'name' => $attributeName,
				'label' => $attributeLabel
			));

			//if($count == 0) {

//			echo 'attributeId->'.$attributeElements;
//die;
			if(!isset($attributeElements))
			{

				$newOCEA->name = $attributeName;
				$newOCEA->label = $attributeLabel;

				if ($newOCEA->save()) {

					//$insert_id = Yii::app()->db->getLastInsertID();

					echo "success";
					Yii::app()->request->redirect('list');

				} else {
					echo "error1";
					print_r($newOCEA->getErrors(), true);
				}
			}
			else
			{
				$attribute = $newOCEA::model()->findByPk($attributeId);
				$attribute->name = $attributeName;
				$attribute->label = $attributeLabel;

				if ($attribute->save()) {

					$newOCEAE = new OEModule\OphCiExamination\models\OphCiExamination_AttributeElement();
					//$element = $newOCEAE::model()->findByAttributes("attribute_id=:attribute_id", array(":attribute_id"=>$attributeId));


					$element = $newOCEAE::model()->findByAttributes(array("attribute_id"=>$attributeId));
					if(is_object($element)) {
						$element->element_type_id = $attributeElements;
						$element->save();
					}else{

						//Add New row in Element Table
					}

					//$attributeElements

					echo "success";
					Yii::app()->request->redirect('list');
				} else {
					echo "error1";
					print_r($newOCEA->getErrors(), true);
				}
			}

	}

	/**
	 * Deletes rows for the model
	 */
	public function actionDelete()
	{
		$admin = new Admin(OEModule\OphCiExamination\models\OphCiExamination_AttributeElement::model(), $this);
		$admin->deleteModel();
	}
}