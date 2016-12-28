<?php

/**
 * Class StudyAdminController
 *
 * Admin contorller for the Genetics Studies
 */
class StudyAdminController extends BaseAdminController
{
    /**
     * @var string
     */
    public $layout = '//layouts/admin';

    /**
     * @var int
     */
    public $itemsPerPage = 100;

    /**
     * Lists studies.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $admin = new Admin(GeneticsStudy::model(), $this);
        $admin->setModelDisplayName('Genetics Study');
        $admin->setListFields(array(
            'id',
            'name',
        ));
        $admin->searchAll();
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
        $admin = new Admin(GeneticsStudy::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setModelDisplayName('Genetics Study');
        $admin->setEditFields(array(
            'name' => 'text',
            'criteria' => 'textarea',
            'end_date' => 'date',
            'proposers' => array(
                'widget' => 'MultiSelectList',
                'relation_field_id' => 'id',
                'label' => 'Investigator',
                'options' => CHtml::encodeArray(CHtml::listData(
                    User::model()->findAll(),
                    'id',
                    function ($model) {
                        return $model->fullName;
                    }
                )),
            ),
            /*'patients' => array(
                'widget' => 'MultiSelectList',
                'relation_field_id' => 'id',
                'label' => 'Patient',
                'options' => CHtml::encodeArray(CHtml::listData(
                    Patient::model()->findAll(),
                    'id',
                    function ($model) {
                        return $model->fullName;
                    }
                )),
            ),*/
        ));
        $admin->editModel();
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $admin = new Admin(GeneticsStudy::model(), $this);
        $admin->deleteModel();
    }
}