<?php

/**
 * Class GeneController
 *
 * Contains the actions pertaining to genes
 */
class StudyController extends BaseAdminController
{
    public $layout = 'genetics';

    protected $itemsPerPage = 20;

    /**
     * Configure access rules
     *
     * @return array
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('List', 'View'),
                'roles' => array('TaskViewGeneticStudy'),
            ),
            array('allow',
                'actions' => array('Edit', 'Delete'),
                'roles' => array('TaskEditGeneticStudy'),
            ),
        );
    }

    public function actionView($id)
    {
        $genetics_study = $this->loadModel($id);
        $this->render('view', array('model' => $genetics_study));
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
            'referer' => 'referer',
            'name' => 'text',
            'criteria' => 'textarea',
            'end_date' => 'date',
            'proposers' => array(
                'widget' => 'MultiSelectList',
                'relation_field_id' => 'id',
                'label' => 'Investigator',
                'options' => CHtml::encodeArray(CHtml::listData(
                    // because of performance issues we need to list all the required roles even if they are in parent-child
                    User::model()->findAllByRoles(['Genetics User', 'Genetics Clinical', 'Genetics Laboratory Technician', 'Genetics Admin'], true),
                    'id',
                    function ($model) {
                        return $model->fullName;
                    }
                )),
            ),
        ));

        $admin->setCustomCancelURL(Yii::app()->request->getUrlReferrer());

        $valid = $admin->editModel(false);

        if (Yii::app()->request->isPostRequest) {
            if ($valid) {
                Yii::app()->user->setFlash('success', "Study Saved");
                $url = str_replace('/edit', '/view', (Yii::app()->request->requestUri)) . '/' . $admin->getModel()->id;
                $this->redirect($url);
            } else {
                $admin->render($admin->getEditTemplate(), array('admin' => $admin, 'errors' => $admin->getModel()->getErrors()));
            }
        }
    }

    /**
     * List the Genetic Patients
     */
    public function actionList()
    {
        $admin = new Crud(GeneticsStudy::model(), $this);
        $admin->setListFieldsAction('view');
        $admin->setModelDisplayName('Genetic Studies');
        $admin->setListFields(array(
            'id',
            'name',
            'end_date',
            'criteria',
            'getProposerNames'
        ));

        $searchArray = array(
            'type' => 'compare',
            'compare_to' => array('name', 'end_date', 'criteria', 'proposers.first_name', 'proposers.last_name'));
        $admin->getSearch()->addSearchItem('id', $searchArray);

        $admin->setUnsortableColumns(array('getProposerNames'));

        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->getSearch()->setDefaultResults(false);
        $display_buttons = $this->checkAccess('TaskEditGeneticStudy');
        $admin->listModel($display_buttons);
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $admin = new Admin(GeneticsStudy::model(), $this);
        $admin->deleteModel();
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @param int $id the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = GeneticsStudy::model()->findByPk((int) $id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }
}
