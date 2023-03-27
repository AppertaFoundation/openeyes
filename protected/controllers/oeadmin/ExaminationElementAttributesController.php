<?php

use OEModule\OphCiExamination\models\OphCiExamination_Attribute;
use OEModule\OphCiExamination\models\OphCiExamination_AttributeElement;
use OEModule\OphCiExamination\models\OphCiExamination_AttributeOption;

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

    public $group = 'Examination';


    public function accessRules()
    {
        return array(array('allow', 'roles' => array('admin', 'OprnInstitutionAdmin')));
    }

    /**
     * @throws CHttpException
     */
    public function actionList()
    {
        $admin = new Admin(OphCiExamination_Attribute::model(), $this);

        $admin->setListFields(array(
            'display_order',
            'name',
            'label',
            'attribute_elements.id',
            'attribute_element_types.name',
            'is_multiselect',
        ));

        $institution_id = !empty($_GET['institution_id']) ? $_GET['institution_id'] : \Yii::app()->session['selected_institution_id'];
        $institution = Institution::model()->findByPk($institution_id);

        $criteria = new CDbCriteria();
        $criteria->order = 't.display_order asc';

        $admin->getSearch()->setCriteria(
            OphCiExamination_Attribute::model()->getCriteriaForLevels(
                ReferenceData::LEVEL_ALL,
                $criteria,
                $institution
            )
        );
        $admin->setModelDisplayName('Element Attributes');
        $admin->div_wrapper_class = 'cols-8';
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);

        $admin->setListTemplate('//admin/generic/listInstitution');
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
        $admin = new Admin(OphCiExamination_Attribute::model(), $this);
        $admin->setCustomSaveURL('/oeadmin/ExaminationElementAttributes/update');
        if ($id) {
            $admin->setModelId($id);
        } else {
            $institution_id = !empty($_GET['institution_id']) ? $_GET['institution_id'] : \Yii::app()->session['selected_institution_id'];
            $model = $admin->getModel();

            $model->institution_id = $institution_id;

            $admin->setModel($model);
        }
        $admin->setModelDisplayName('Element Attributes');

        $admin->setEditFields(array(

            'name' => 'text',
            'label' => 'text',
            'attribute_elements' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(ElementType::model()->findAll(), 'id', 'name'),
                'htmlOptions' => ['class' => 'cols-8'],
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'institution' => array(
                'widget' => 'DropDownList',
                'options' => Institution::model()->getTenantedList(false, true),
                'htmlOptions' => ['class' => 'cols-8', 'empty' => 'None'],
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'is_multiselect' => 'checkbox',
        ));

        $admin->editModel();
    }

    /**
     * @throws Exception
     */
    public function actionUpdate()
    {
        $newOCEA = new OphCiExamination_Attribute();
        $newOCEAE = new OphCiExamination_AttributeElement();

        $post = Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExamination_Attribute');

        $attributeId = $post['id'];
        $attributeName = $post['name'];
        $attributeLabel = $post['label'];
        $attributeElements = $post['attribute_elements'];
        $attributeInstitution = $post['institution'] === '' ? null : $post['institution'];
        $attributeIsMultiSelect = $post['is_multiselect'];

        if (!isset($attributeElements)) {
            $newOCEA->name = $attributeName;
            $newOCEA->label = $attributeLabel;
            $newOCEA->institution_id = $attributeInstitution;
            $newOCEA->is_multiselect = $attributeIsMultiSelect;

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
                $newOCEA->institution_id = $attributeInstitution;
                $newOCEA->is_multiselect = $attributeIsMultiSelect;

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
                $attribute = OphCiExamination_Attribute::model()->findByPk($attributeId);
                $attribute->name = $attributeName;
                $attribute->label = $attributeLabel;
                $attribute->institution_id = $attributeInstitution;
                $attribute->is_multiselect = $attributeIsMultiSelect;


                if ($attribute->save()) {
                    $element = OphCiExamination_AttributeElement::model()->findByAttributes(array('attribute_id' => $attributeId));
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

    protected function isAttributeElementDeletable(OphCiExamination_AttributeElement $element)
    {
        return !OphCiExamination_AttributeOption::model()->exists(
            'attribute_element_id = :id',
            [':id' => $element->id]
        );
    }

    protected function isAttributeDeletable(OphCiExamination_Attribute $attribute)
    {
        return !OphCiExamination_AttributeElement::model()->exists(
            'attribute_id = :id',
            [':id' => $attribute->id]
        );
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $post = Yii::app()->request->getPost(OphCiExamination_Attribute::class);

        $attributeIdsArray = $post['id'];

        foreach ($attributeIdsArray as $key => $attributeId) {
            $element = OphCiExamination_AttributeElement::model()->findByAttributes(array('attribute_id' => $attributeId));

            if (Yii::app()->request->getPost('DELETE_SUBS_ALSO')) {
                $this->deleteAttributeElements($element);
            }

            if ($element && $this->isAttributeElementDeletable($element)) {
                if (!$element->delete()) {
                    echo 'error';
                    print_r($element->getErrors(), true);
                }
            } else {
                echo "Cannot delete; Attribute Element is in use";
            }

            $attribute = OphCiExamination_Attribute::model()->findByAttributes(array('id' => $attributeId));
            if ($attribute && $this->isAttributeDeletable($attribute)) {
                if ($attribute->delete()) {
                    echo true;
                } else {
                    echo 'error';
                    print_r($attribute->getErrors(), true);
                }
            } else {
                echo "Cannot delete; Attribute is in use";
            }
        }
    }

    public function actionSearch()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $criteria = new CDbCriteria();
            if (isset($_GET['term']) && strlen($_GET['term']) > 0) {
                $criteria->addCondition(
                    array('LOWER(name) LIKE :term'),
                    'OR'
                );
                $params[':term'] = '%' . strtolower(strtr($_GET['term'], array('%' => '\%'))) . '%';
            }

            $criteria->order = 'name';
            $criteria->select = 'id, name';
            $criteria->params = $params;

            $results = OphCiExamination_Attribute::model()->active()->findAll($criteria);

            $return = array();
            foreach ($results as $resultRow) {
                $return[] = array(
                    'label' => $resultRow->name,
                    'value' => $resultRow->name,
                    'id' => $resultRow->id,
                );
            }
            $this->renderJSON($return);
        }
    }

    /**
     * Save ordering of the objects.
     */
    public function actionSort()
    {
        $admin = new Admin(OphCiExamination_Attribute::model(), $this);
        $admin->sortModel();
    }

    public function deleteAttributeElements(OphCiExamination_AttributeElement $element)
    {
        OphCiExamination_AttributeOption::model()->deleteAll('attribute_element_id = :id', [':id' => $element->id]);
    }
}
