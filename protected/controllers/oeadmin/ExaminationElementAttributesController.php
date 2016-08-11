<?php
/**
 * Created by PhpStorm.
 * User: himanshu
 * Date: 12/05/15
 * Time: 15:02.
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
     * @throws CHttpException
     */
    public function actionList()
    {
        $admin = new Admin(OEModule\OphCiExamination\models\OphCiExamination_Attribute::model(), $this);

        $admin->setListFields(array(
            'display_order',
            'name',
            'label',
            'attribute_elements_id.id',
            'attribute_elements.name',
        ));
        $admin->searchAll();
        $admin->setModelDisplayName('Element Attributes');
        //$admin->getSearch()->addActiveFilter();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }
    /**
     * Edits or adds a Procedure.
     *
     * @param bool|int $id
     *
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

        $admin->setEditFields(array(
            'id' => 'label',
            'name' => 'text',
            'label' => 'text',
            'attribute_elements' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(ElementType::model()->findAll(), 'id', 'name'),
                'htmlOptions' => null,
                'hidden' => false,
                'layoutColumns' => null,
            ),
        ));

        $admin->editModel();
    }

    /**
     * @throws Exception
     */
    public function actionUpdate()
    {
        $newOCEA = new OEModule\OphCiExamination\models\OphCiExamination_Attribute();
        $newOCEAE = new OEModule\OphCiExamination\models\OphCiExamination_AttributeElement();

        $post = Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExamination_Attribute');

        $attributeId = $post['id'];
        $attributeName = $post['name'];
        $attributeLabel = $post['label'];
        $attributeElements = $post['attribute_elements'];

        $count = $newOCEA::model()->countByAttributes(array(
            'name' => $attributeName,
            'label' => $attributeLabel,
        ));

        if (!isset($attributeElements)) {
            $newOCEA->name = $attributeName;
            $newOCEA->label = $attributeLabel;

            if ($newOCEA->save()) {
                echo 'success';
                Yii::app()->request->redirect('list');
            } else {
                echo 'error1';
                print_r($newOCEA->getErrors(), true);
            }
        } else {
            //Add New
            if ($attributeId ==  '') {
                $newOCEA->name = $attributeName;
                $newOCEA->label = $attributeLabel;

                if ($newOCEA->save()) {
                    $newAttributeId = Yii::app()->db->getLastInsertID();

                    $newOCEAE->attribute_id = $newAttributeId;
                    $newOCEAE->element_type_id = $attributeElements;

                    if ($newOCEAE->save()) {
                        echo 'success';
                    } else {
                        echo 'error';
                        print_r($newOCEA->getErrors(), true);
                    }

                    Yii::app()->request->redirect('list');
                } else {
                    echo 'error';
                    print_r($newOCEA->getErrors(), true);
                }
            } else {
                //Edit
                $attribute = $newOCEA::model()->findByPk($attributeId);
                $attribute->name = $attributeName;
                $attribute->label = $attributeLabel;

                if ($attribute->save()) {
                    $element = $newOCEAE::model()->findByAttributes(array('attribute_id' => $attributeId));
                    if (is_object($element)) {
                        $element->element_type_id = $attributeElements;
                        $element->save();
                    }

                    echo 'success';
                    Yii::app()->request->redirect('list');
                } else {
                    echo 'error1';
                    print_r($newOCEA->getErrors(), true);
                }
            }
        }
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $post = Yii::app()->request->getPost('OEModule\OphCiExamination\models\OphCiExamination_Attribute');

        $attributeIdsArray = $post['id'];

        $newOCEA = new OEModule\OphCiExamination\models\OphCiExamination_Attribute();
        $newOCEAE = new OEModule\OphCiExamination\models\OphCiExamination_AttributeElement();

        foreach ($attributeIdsArray as $key => $attributeId) {
            $element = $newOCEAE::model()->findByAttributes(array('attribute_id' => $attributeId));
            if (is_object($element)) {
                if ($element->delete()) {
                    //echo success;
                } else {
                    echo 'error';
                    print_r($element->getErrors(), true);
                }
            }

            $attribute = $newOCEA::model()->findByAttributes(array('id' => $attributeId));
            if (is_object($attribute)) {
                if ($attribute->delete()) {
                    echo true;
                } else {
                    echo 'error';
                    print_r($attribute->getErrors(), true);
                }
            }
        }
    }

    public function actionSearch()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $criteria = new CDbCriteria();
            if (isset($_GET['term']) && strlen($term = $_GET['term']) > 0) {
                $criteria->addCondition(array('LOWER(name) LIKE :term'),
                    'OR');
                $params[':term'] = '%'.strtolower(strtr($term, array('%' => '\%'))).'%';
            }

            $criteria->order = 'name';
            $criteria->select = 'id, name';
            $criteria->params = $params;

            $newOCEA = new OEModule\OphCiExamination\models\OphCiExamination_Attribute();

            $results = $newOCEA::model()->active()->findAll($criteria);

            $return = array();
            foreach ($results as $resultRow) {
                $return[] = array(
                    'label' => $resultRow->name,
                    'value' => $resultRow->name,
                    'id' => $resultRow->id,
                );
            }
            echo CJSON::encode($return);
        }
    }

    /**
     * Save ordering of the objects.
     */
    public function actionSort()
    {
        $admin = new Admin(OEModule\OphCiExamination\models\OphCiExamination_Attribute::model(), $this);
        $admin->sortModel();
    }
}
