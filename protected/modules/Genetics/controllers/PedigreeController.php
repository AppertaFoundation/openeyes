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
                'roles' => array('OprnEditPedigree'),
            ),
            array(
                'allow',
                'actions' => array('List'),
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
                'viewName' => 'subjectList',
                'viewArguments' => array(
                    'subjects' => $admin->getModel()->subjects,
                    'pedigree_id' => $id,
                ),
            ),
        ));

        $admin->editModel();
    }

    /**
     * List the Genetic Patients
     */
    public function actionList()
    {
        $admin = new Crud(Pedigree::model(), $this);
        $admin->setListFields(array(
            'inheritance.name',
            'gene.name',
            'genomic_coordinate',
            'genome_version',
            'gene_transcript',
        ));
        $admin->getSearch()->addSearchItem('id');
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
        $admin->listModel();
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
}