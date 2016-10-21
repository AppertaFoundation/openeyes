<?php

/**
 * Class SubjectController
 *
 * Contains the actions pertaining to genetics subjects
 */
class SubjectController extends BaseModuleController
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
                'actions' => array('Edit'),
                'roles' => array('OprnEditGeneticPatient'),
            ),
            array('allow',
                'actions' => array('List'),
                'roles' => array('OprnViewGeneticPatient'),
            ),
        );
    }

    /**
     * @param bool|int $id
     */
    public function actionEdit($id = false)
    {
        $admin = new Crud(GeneticsPatient::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setModelDisplayName('Patient');
        $admin->setEditFields(array(
            'patient_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(Patient::model()->findAll(), 'id', 'fullName'),
                'htmlOptions' => null,
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'gender_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(Gender::model()->findAll(), 'id', 'name'),
                'htmlOptions' => null,
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'is_deceased' => 'checkbox',
            'comments' => 'textarea',
        ));
        $admin->editModel();
    }

    /**
     * List the Genetic Patients
     */
    public function actionList()
    {
        $admin = new Crud(GeneticsPatient::model(), $this);
        $admin->setListFields(array(
            'id',
            'patient.fullName',
        ));
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }
}