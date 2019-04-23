<?php

/**
 * Class ResultTypeController.
 *
 * Controller to administer result types
 */
class ResultTypeController extends BaseAdminController
{
    protected $admin;
    public $group = 'Lab Results';


    protected function beforeAction($action)
    {
        $this->admin = new Admin(OphInLabResults_Type::model(), $this);
        $this->admin->setModelDisplayName('Lab Result Type');
        $this->admin->div_wrapper_class = 'cols-5';

        return parent::beforeAction($action);
    }

    /**
     * Lists Result Types.
     *
     * @throws CHttpException
     */

    public function actionList()
    {
        $this->render('/admin/list_OphInLabResults_Type', array(
            'model_list' => OphInLabResults_Type::model()->findAll(),
            'title' => 'Manage Result Types',
            'model_class' => 'OphInLabResults_Type',
        ));
    }

    public function actionEdit()
    {
        $request = Yii::app()->getRequest();
        $model = OphInLabResults_Type::model()->findByPk((int)$request->getParam('id'));
        if (!$model) {
            throw new Exception('OphInLabResults_Type not found with id ' . $request->getParam('id'));
        }
        if ($request->getPost('OphInLabResults_Type')) {
            $model->attributes = $request->getPost('OphInLabResults_Type');
            if($model->fieldType->name != "Numeric Field"){
                $model->min_range = null;
                $model->max_range = null;
                $model->normal_min = null;
                $model->normal_max= null;
            }
            if (!$model->validate()) {
                $errors = $model->getErrors();
            } else {
                if ($model->save()) {
                    Yii::app()->user->setFlash('success', 'OphInLabResults_Type saved');
                    $this->redirect(array('List'));
                } else {
                    $errors = $model->getErrors();
                }
            }
        }
        $this->render('/admin/edit', array(
            'model' => $model,
            'title' => 'Edit Results_Type',
            'errors' => isset($errors) ? $errors : null,
            'cancel_uri' => '/oeadmin/resultType/list',
        ));
    }

    /**
//     * Edits or adds a Type.
//     *
//     * @param bool|int $id
//     *
//     * @throws CHttpException
//     */
//    public function actionEdit($id = false)
//    {
//        if ($id) {
//            $this->admin->setModelId($id);
//        }
//
//        $eventType = EventType::model()->findByAttributes(array('name' => 'Lab Results'));
//
//        if ($eventType) {
//            $options = CHtml::listData(ElementType::model()->findAllByAttributes(array('event_type_id' => $eventType->id)), 'id', 'name');
//        } else {
//            $options = CHtml::listData(ElementType::model()->findAll(), 'id', 'name');
//        }
//
//        $this->admin->setEditFields(array(
//            'type' => 'text',
//            'result_element_id' => array(
//                'widget' => 'DropDownList',
//                'options' => $options,
//                'htmlOptions' => ['class' => 'cols-full'],
//                'hidden' => false,
//                'layoutColumns' => null,
//            ),
//        ));
//        $this->admin->editModel();
//    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $this->admin->deleteModel();
    }

    /**
     * Save ordering of the objects.
     */
    public function actionSort()
    {
        $this->admin->sortModel();
    }
}
