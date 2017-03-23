<?php

/**
 * Class PedigreeController
 *
 * Controller for the Pedigree actions
 */
class PedigreeController extends BaseModuleController
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
            array(
                'allow',
                'actions' => array('Edit', 'EditStudyStatus'),
                'roles' => array('TaskEditPedigreeData','OprnEditPedigree'),
            ),
            array(
                'allow',
                'actions' => array('List', 'View'),
                'roles' => array('OprnSearchPedigree'),
            ),
            array(
                'allow',
                'actions' => array('PedigreeDisorder'),
                'roles' => array('OprnSearchPedigree', 'OprnEditGeneticPatient'),
            ),
        );
    }

    /**
     * @param CAction $action
     * @return bool
     */
    public function beforeAction($action)
    {
        Yii::app()->assetManager->registerCssFile('/components/font-awesome/css/font-awesome.css', null, 10);

        return parent::beforeAction($action);
    }

    public function actionView($id)
    {
        $pedigree = $this->loadModel($id);
        $this->render('view', array('model' => $pedigree));
    }

    /**
     * @param bool $id
     *
     * @throws CHttpException
     * @throws Exception
     */
    public function actionEdit($id = false)
    {
        $admin = new Crud(Pedigree::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }

        $admin->setEditFields(array(
            'referer' => 'referer',
            'id' => 'label',
            'inheritance_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(PedigreeInheritance::model()->findAll(), 'id', 'name'),
                'htmlOptions' => null,
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'comments' => 'textarea',
            'disorder' => 'label',
            'consanguinity' => 'checkbox',
            'gene_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(PedigreeGene::model()->findAll(), 'id', 'name'),
                'htmlOptions' => array('empty' => '- Gene -'),
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'base_change_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(PedigreeBaseChangeType::model()->findAll(), 'id', 'change'),
                'htmlOptions' => array('empty' => '- Base Change Type -'),
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'base_change' => 'text',
            'amino_acid_change_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(PedigreeAminoAcidChangeType::model()->findAll(), 'id', 'change'),
                'htmlOptions' => array('empty' => '- Amino Acid Change Type -'),
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'amino_acid_change' => 'text',
            'genomic_coordinate' => 'text',
            'genome_version' => array(
                'widget' => 'DropDownList',
                'options' => array_combine($admin->getModel()->genomeVersions(), $admin->getModel()->genomeVersions()), //get the versions as key and value for the dropdown
                'htmlOptions' => null,
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'gene_transcript' => 'text',
            'subjects' => array(
                'widget' => 'CustomView',
                'viewName' => '/default/subjectList',
                'viewArguments' => array(
                    'subjects' => $admin->getModel()->subjects,
                    'pedigree_id' => $id,
                ),
            ),
        ));

        $admin->setCustomCancelURL(Yii::app()->request->getUrlReferrer());    

        $valid = $admin->editModel(false);

        if (Yii::app()->request->isPostRequest) {        
            if ($valid) {
                Yii::app()->user->setFlash('success', "Family Saved");
                     $url = '/Genetics/pedigree/view/'.$admin->getModel()->id;
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
        $admin = new Crud(Pedigree::model(), $this);
        $admin->setListFieldsAction('view');
        $admin->setModelDisplayName('Families');
        $admin->setListFields(array(
            'id',
            'inheritance.name',
            'gene.name',
            'getSubjectsCount',
            'getAffectedSubjectsCount'
        ));
        $admin->getSearch()->addSearchItem('id', array( 'type' => 'id' ));
        $admin->getSearch()->addSearchItem('inheritance_id', array(
            'type' => 'dropdown',
            'options' => CHtml::listData(PedigreeInheritance::model()->findAll(), 'id', 'name'),
            'empty' => '- Inheritance -',
        ));
        $admin->getSearch()->addSearchItem('gene_id', array(
            'type' => 'dropdown',
            'options' => CHtml::listData(PedigreeGene::model()->findAll(), 'id', 'name'),
            'empty' => '- Gene -',
        ));
        $admin->getSearch()->addSearchItem('consanguinity', array('type' => 'boolean'));
        $admin->getSearch()->addSearchItem('disorder_id', array('type' => 'disorder'));
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $display_buttons = $this->checkAccess('OprnEditPedigree');
        $admin->listModel($display_buttons);
    }

    /**
     * @param $id
     * @throws CHttpException
     */
    public function actionPedigreeDisorder($id)
    {
        $pedigree = Pedigree::model()->findByPk($id);

        if (!$pedigree) {
            throw new CHttpException(404);
        }

        if (!$pedigree->disorder_id) {
            throw new CHttpException(400);
        }

        $this->renderJSON(
            array(
                'id' => $pedigree->disorder_id,
                'disorder' => $pedigree->disorder->term,
            )
        );
    }
    
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @param int $id the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = Pedigree::model()->findByPk((int) $id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }
}