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
                'actions' => array('Edit', 'Delete', 'EditStudyStatus'),
                'roles' => array('TaskEditPedigreeData'),
            ),
            array(
                'allow',
                'actions' => array('List', 'View', 'Search'),
                'roles' => array('OprnSearchPedigree'),
            ),
        );
    }

    /**
     * @param CAction $action
     * @return bool
     */
    public function beforeAction($action)
    {
        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.Genetics.assets.js'), true);
        Yii::app()->clientScript->registerScriptFile($assetPath . '/gene_validation.js');


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
        } else {
            //oh, sure, let me just set the defaults this way.
            //more: Admin.php line ~515
            $pedigree_inheritance = PedigreeInheritance::model()->findByAttributes(array('name' => 'Unknown/other'));
            $_GET['default'] = array('inheritance_id' => $pedigree_inheritance ? $pedigree_inheritance->id : null);
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

            'disorder_id' => array(
                'widget' => 'DisorderLookup',
                'relation' => 'disorder',
                'options' => CommonOphthalmicDisorder::getList(Firm::model()->findByPk($this->selectedFirmId)),
                'empty_text' => 'Select a commonly used diagnosis'
            ),

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
            'base_change' => array(
                'widget' => 'text',
                'htmlOptions' => array('class' => 'gene-validation'),
            ),
            'amino_acid_change_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(PedigreeAminoAcidChangeType::model()->findAll(), 'id', 'change'),
                'htmlOptions' => array('empty' => '- Amino Acid Change Type -'),
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'amino_acid_change' => array(
                'widget' => 'text',
                'htmlOptions' => array('class' => 'gene-validation'),
            ),
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

                $this->redirect('/Genetics/pedigree/view/' . $admin->getModel()->id);
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
        if (empty($_GET)) {
            if (($data = YiiSession::get('genetics_pedigree_searchoptions'))) {
                $_GET = $data;
            }
            Audit::add('Genetics pedigree list', 'view');
        } else {
            Audit::add('Genetics pedigree list', 'search');

            YiiSession::set('genetics_pedigree_searchoptions', $_GET);
        }


        $admin = new Crud(Pedigree::model(), $this);
        $admin->setListFieldsAction('view');
        $admin->setModelDisplayName('Families');
        $admin->setListFields(array(
            'id',
            'inheritance.name',
            'gene.name',
            'getSubjectsCount',
            'getAffectedSubjectsCount',
            'disorder.term',
            'getConsanguinityAsBoolean'
        ));

        $admin->setUnsortableColumns(['inheritance.name', 'gene.name', 'getSubjectsCount', 'getAffectedSubjectsCount', 'disorder.term', 'getConsanguinityAsBoolean']);

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
        $admin->getSearch()->setDefaultResults(false);
        $display_buttons = $this->checkAccess('OprnEditPedigree');
        $admin->listModel($display_buttons);
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

    /**
     * Search for pedigree
     * returns JSON for autocomplete
     */
    public function actionSearch()
    {

        $pedigree_id = Yii::app()->request->getQuery('term', null);

        if (strlen($pedigree_id) > 2) {
            $criteria = new CDbCriteria();
            $criteria->addSearchCondition('t.id', $pedigree_id, true);

            $pedigrees = Pedigree::model()->with('gene')->findAll($criteria);
        } else {
            //if pedigree_id is 2 digit or less we return the exact match because of performance reasons

            $pedigrees = Pedigree::model()->with('gene')->findByPk($pedigree_id);
            $pedigrees = $pedigrees ? array($pedigrees) : array();
        }

        $output = array();
        foreach ($pedigrees as $pedigree) {
            $output[] = array(
                'label' => $pedigree->id . ($pedigree->gene ? (" (" . $pedigree->gene->name . ")") : ''),
                'value' => $pedigree->id,
            );
        }

        $this->renderJSON($output);

        Yii::app()->end();
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $response = 1;
        $model = Pedigree::model();
        if (Yii::app()->request->isPostRequest) {
            $post = Yii::app()->request->getPost('Pedigree', array());
            if (array_key_exists('id', $post) && is_array($post['id'])) {
                foreach ($post['id'] as $id) {
                    $model = $model->findByPk($id);
                    if (!count($model->subjects)) {
                        if (isset($model->active)) {
                            $model->active = 0;
                            if ($model && !$model->save()) {
                                $response = 0;
                            }
                        } else {
                            if ($model && !$model->delete()) {
                                $response = 0;
                            }
                        }
                    } else {
                        $response = 0;
                    }
                }
            }
        }

        echo $response;
    }
}
