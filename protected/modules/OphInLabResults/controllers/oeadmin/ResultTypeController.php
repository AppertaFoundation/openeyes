<?php

/**
 * Class ResultTypeController.
 *
 * Controller to administer result types
 */
class ResultTypeController  extends ModuleAdminController
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
        $this->admin->setListFields(array(
            'id',
            'type',
        ));
        $this->admin->listModel();
    }

    /**
     * Edits or adds a Type.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        if ($id) {
            $this->admin->setModelId($id);
        }

        $eventType = EventType::model()->findByAttributes(array('name' => 'Lab Results'));

        if ($eventType) {
            $options = CHtml::listData(ElementType::model()->findAllByAttributes(array('event_type_id' => $eventType->id)), 'id', 'name');
        } else {
            $options = CHtml::listData(ElementType::model()->findAll(), 'id', 'name');
        }

        $this->admin->setEditFields(array(
            'type' => 'text',
            'result_element_id' => array(
                'widget' => 'DropDownList',
                'options' => $options,
                'htmlOptions' => ['class' => 'cols-full'],
                'hidden' => false,
                'layoutColumns' => null,
            ),
        ));
        $this->admin->editModel();
    }

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
