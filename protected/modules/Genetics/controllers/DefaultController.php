<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class DefaultController extends BaseEventTypeController
{
    public $items_per_page = 100;
    public $page = 1;
    public $total_items = 0;
    public $pages = 1;
    public $renderPatientPanel = false;
    public $layout = 'genetics';

    protected static $action_types = array(
        'index' => self::ACTION_TYPE_FORM,
        'Pedigrees' => self::ACTION_TYPE_FORM,
        'AddPedigree' => self::ACTION_TYPE_FORM,
        'EditPedigree' => self::ACTION_TYPE_FORM,
        'ViewPedigree' => self::ACTION_TYPE_FORM,
        'Genes' => self::ACTION_TYPE_FORM,
        'AddGene' => self::ACTION_TYPE_FORM,
        'EditGene' => self::ACTION_TYPE_FORM,
        'Inheritance' => self::ACTION_TYPE_FORM,
        'AddInheritance' => self::ACTION_TYPE_FORM,
        'EditInheritance' => self::ACTION_TYPE_FORM,
        'AddPatientToPedigree' => self::ACTION_TYPE_FORM,
        'RemovePatient' => self::ACTION_TYPE_FORM,
    );

    /**
     * Index action
     */
    public function actionIndex()
    {
        $this->redirect(Yii::app()->createUrl('/Genetics/subject/list'));
    }

    /**
     * Configure access rules
     *
     * @return array
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('Pedigrees', 'Index', 'Genes', 'ViewPedigree'),
                'roles' => array('OprnSearchPedigree'),
            ),
            array('allow',
                'actions' => array('EditGene', 'AddGene', 'AddInheritance', 'EditInheritance', 'AddPedigree', 'EditPedigree', 'AddPatientToPedigree', 'RemovePatient'),
                'roles' => array('OprnEditPedigree'),
            ),
            array('allow',
                'actions' => array('EditGene', 'AddGene'),
                'roles' => array('OprnEditGene'),
            ),
        );
    }

    /**
     * Pedigrees action
     */
    public function actionPedigrees()
    {
        $errors = array();

        if (Yii::app()->request->getPost('delete')) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', Yii::app()->request->getPost('pedigrees'));

            $pedigrees = Pedigree::model()->findAll($criteria);
            if(is_array($pedigrees)){
                foreach ($pedigrees as $pedigree) {
                    try {
                        $pedigree->delete();
                    } catch (Exception $e) {
                        if (!isset($errors['Error'])) {
                            $errors['Error'] = array();
                        }
                        $errors['Error'][] = "unable to delete pedigree $pedigree->id: in use";
                    }
                }
            }
        }

        $pedigrees = array();
        $pagination = null;

        if (Yii::app()->request->getQuery('search')) {
            $criteria = new CDbCriteria();

            if (Yii::app()->request->getQuery('family-id')) {
                $criteria->addCondition('t.id = :id');
                $criteria->params[':id'] = Yii::app()->request->getQuery('family-id');
            }

            if (Yii::app()->request->getQuery('gene-id')) {
                $criteria->addCondition('gene_id = :gene_id');
                $criteria->params[':gene_id'] = Yii::app()->request->getQuery('gene-id');
            }

            if (strlen(Yii::app()->request->getQuery('consanguineous', '')) > 0) {
                $criteria->addCondition('consanguinity = :consanguineous');
                $criteria->params[':consanguineous'] = Yii::app()->request->getQuery('consanguineous');
            }

            if (Yii::app()->request->getQuery('disorder-id')) {
                $criteria->addCondition('disorder_id = :disorder_id');
                $criteria->params[':disorder_id'] = Yii::app()->request->getQuery('disorder-id');
            }

            if (Yii::app()->request->getQuery('molecular-diagnosis') === 'true') {
                $criteria->addCondition('gene_id is not null');
            }

            $dir = Yii::app()->request->getQuery('order') === 'desc' ? 'desc' : 'asc';

            $order = 't.id desc';

            switch (Yii::app()->request->getQuery('sortby')) {
                case 'inheritance':
                    $order = "inheritance.name $dir";
                    break;
                case 'consanguinity':
                    $order = "consanguinity $dir";
                    break;
                case 'gene':
                    $order = "gene.name $dir";
                    break;
                case 'base-change':
                    $order = "base_change $dir";
                    break;
                case 'amino-acid-change':
                    $order = "amino_acid_change $dir";
                    break;
                case 'disorder':
                    $order = "disorder.fully_specified_name $dir";
                    break;
                case 'id':
                    $order = "t.id $dir";
                    break;
            }

            $criteria->order = $order;
            $pagination = $this->initPagination(Pedigree::model(), $criteria);

            $pedigrees = $this->getItems(array(
                'model' => 'Pedigree',
                'with' => array(
                    'inheritance',
                    'gene',
                    'disorder',
                ),
                'page' => (int)Yii::app()->request->getQuery('page', ''),
                'criteria' => $criteria,
                'order' => $order,
            ));
        }

        $this->render('pedigrees', array(
            'pedigrees' => $pedigrees,
            'pagination' => $pagination,
            'errors' => $errors,
        ));
    }

    /**
     * @param $params
     * @return mixed
     */
    public function getItems($params)
    {
        if (isset($params['criteria'])) {
            $criteria = $params['criteria'];
        } else {
            $criteria = new CDbCriteria();
        }

        $model = $params['model'];
        $with = isset($params['with']) ? $params['with'] : array();

        $this->total_items = $model::model()->count($criteria);
        $this->pages = ceil($this->total_items / $this->items_per_page);
        $this->page = 1;

        if (isset($params['page'])) {
            if ($params['page'] >= 1 and $params['page'] <= $this->pages) {
                $this->page = $params['page'];
            }
        }

        $criteria->order = $params['order'];
        $criteria->offset = ($this->page - 1) * $this->items_per_page;
        $criteria->limit = $this->items_per_page;

        return $model::model()->with($with)->findAll($criteria);
    }

    /**
     * @param      $model
     * @param null $criteria
     * @return CPagination
     */
    private function initPagination($model, $criteria = null)
    {
        $criteria = is_null($criteria) ? new CDbCriteria() : $criteria;
        $itemsCount = $model->count($criteria);
        $pagination = new CPagination($itemsCount);
        $pagination->pageSize = $this->items_per_page;
        $pagination->applyLimit($criteria);

        return $pagination;
    }

    /**
     * Add a patient to pedigree
     */
    public function actionAddPatientToPedigree()
    {
        $patient_pedigree = new PatientPedigree();

        $errors = array();

        $patient_pedigree->pedigree_id = Yii::app()->request->getQuery('pedigree');
        $patient_pedigree->patient_id = Yii::app()->request->getQuery('patient');

        if (Yii::app()->request->isPostRequest) {
            if (Yii::app()->request->getPost('cancel')) {
                $this->redirect(array('/Genetics/default/ViewPedigree/'.$patient_pedigree->pedigree_id));
            }

            $pedigree_post = Yii::app()->request->getPost('PatientPedigree', array());
            $patient_pedigree->patient_id = $pedigree_post['patient_id'];

            if (!Patient::model()->find('id=?', array($pedigree_post['patient_id']))) {
                $errors[] = array('Patient' => 'Patient not found');
            } elseif (!Pedigree::model()->find('id=?', array($pedigree_post['pedigree_id']))) {
                $errors[] = array('Pedigree' => 'Pedigree not found');
            } elseif (PatientPedigree::model()->find('patient_id=?', array($pedigree_post['patient_id']))) {
                $errors[] = array('Patient' => 'Patient already in pedigree');
            } else {
                $patient_pedigree->pedigree_id = $pedigree_post['pedigree_id'];
                $patient_pedigree->status_id = $pedigree_post['status_id'];

                if (!$patient_pedigree->save()) {
                    $errors = $patient_pedigree->getErrors();
                } else {
                    PedigreeDiagnosisAlgorithm::updatePedigreeDiagnosisByPatient($patient_pedigree->patient_id);

                    $this->redirect(array('/Genetics/default/ViewPedigree/'.$patient_pedigree->pedigree_id));
                }
            }
        }

        $this->render('add_patient_to_pedigree', array(
            'patient_pedigree' => $patient_pedigree,
            'errors' => $errors,
        ));
    }

    /**
     * View pedigrees
     *
     * @param $id
     * @throws Exception
     */
    public function actionViewPedigree($id)
    {
        if (!$pedigree = Pedigree::model()->findByPk($id)) {
            throw new Exception("Pedigree not found: $id");
        }

        $errors = array();

        $this->render('view_pedigree', array(
            'pedigree' => $pedigree,
            'errors' => $errors,
        ));
    }

    /**
     * Remove patients
     *
     * @param $id
     * @throws Exception
     */
    public function actionRemovePatient($id)
    {
        if (!$patient_pedigree = PatientPedigree::model()->find('patient_id=?', array($id))) {
            throw new Exception("Patient not found: $id");
        }

        $pedigree_id = $patient_pedigree->pedigree_id;
        $patient_pedigree->delete();

        PedigreeDiagnosisAlgorithm::updatePedigreeDiagnosisByPedigreeID($pedigree_id);

        $this->redirect(array('/Genetics/default/viewPedigree/'.$pedigree_id));
    }

    /**
     * Inheritance action
     */
    public function actionInheritance()
    {
        $errors = array();

        if (Yii::app()->request->getPost('add')) {
            $this->redirect(Yii::app()->createUrl('/Genetics/default/addInheritance'));
        }

        if (Yii::app()->request->getPost('delete')) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', Yii::app()->request->getPost('inheritance'));

            foreach (PedigreeInheritance::model()->findAll($criteria) as $inheritance) {
                try {
                    $inheritance->delete();
                } catch (Exception $e) {
                    if (!isset($errors['Error'])) {
                        $errors['Error'] = array();
                    }
                    $errors['Error'][] = "unable to delete inheritance $inheritance->id: in use";
                }
            }
        }

        $pagination = $this->initPagination(PedigreeInheritance::model());

        $this->render('inheritance', array(
            'inheritance' => $this->getItems(array(
                    'model' => 'PedigreeInheritance',
                    'page' => (int)Yii::app()->request->getQuery('page'),
                )),
            'pagination' => $pagination,
            'errors' => $errors,
        ));
    }

    /**
     * Get the inheritances
     *
     * @return CActiveRecord[]
     */
    public function getInheritance()
    {
        $this->total_items = PedigreeInheritance::model()->count(array('order' => 't.id asc'));

        $this->pages = ceil($this->total_items / $this->items_per_page);

        return PedigreeInheritance::model()->findAll(array(
            'order' => 't.id asc',
            'limit' => $this->items_per_page,
        ));
    }

    /**
     * Add an inheritance
     */
    public function actionAddInheritance()
    {
        $inheritance = new PedigreeInheritance();

        $errors = array();

        if (Yii::app()->request->isPostRequest) {
            if (Yii::app()->request->getPost('cancel')) {
                return $this->redirect(array('/Genetics/default/inheritance'));
            }

            $inheritance->attributes = Yii::app()->request->getPost('PedigreeInheritance');

            if (!$inheritance->save()) {
                $errors = $inheritance->getErrors();
            } else {
                return $this->redirect(array('/Genetics/default/inheritance'));
            }
        }

        $this->render('edit_inheritance', array(
            'inheritance' => $inheritance,
            'errors' => $errors,
        ));
    }

    /**
     * Edit an inheritance
     *
     * @param $id
     * @throws Exception
     */
    public function actionEditInheritance($id)
    {
        if (!$inheritance = PedigreeInheritance::model()->findByPk($id)) {
            throw new Exception("PedigreeInheritance not found: $id");
        }

        $errors = array();

        if (Yii::app()->request->isPostRequest) {
            if (Yii::app()->request->getPost('cancel')) {
                $this->redirect(array('/Genetics/default/inheritance'));
            }

            $inheritance->attributes = Yii::app()->request->getPost('PedigreeInheritance');

            if (!$inheritance->save()) {
                $errors = $inheritance->getErrors();
            } else {
                $this->redirect(array('/Genetics/default/inheritance'));
            }
        }

        $this->render('edit_inheritance', array(
            'inheritance' => $inheritance,
            'errors' => $errors,
        ));
    }

    /**
     * Genes action
     */
    public function actionGenes()
    {
        $errors = array();

        if (Yii::app()->request->getPost('add')) {
            $this->redirect(Yii::app()->createUrl('/Genetics/default/addGene'));
        }

        if (Yii::app()->request->getPost('delete')) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', Yii::app()->request->getPost('genes'));

            foreach (PedigreeGene::model()->findAll($criteria) as $gene) {
                try {
                    $gene->delete();
                } catch (Exception $e) {
                    if (!isset($errors['Error'])) {
                        $errors['Error'] = array();
                    }
                    $errors['Error'][] = "unable to delete gene $gene->id: in use";
                }
            }
        }

        $criteria = new CDbCriteria();

        $dir = Yii::app()->request->getQuery('order') === 'desc' ? 'desc' : 'asc';
        $order = "name $dir";

        switch (Yii::app()->request->getQuery('sortby')) {
            case 'name':
                $order = "name $dir";
                break;
            case 'location':
                $order = "location $dir";
                break;
        }

        $criteria->order = $order;
        $pagination = $this->initPagination(PedigreeGene::model(), $criteria);

        $this->render('genes', array(
            'genes' => $this->getItems(array(
                    'model' => 'PedigreeGene',
                    'page' => (int)Yii::app()->request->getQuery('page'),
                    'order' => $order,
                )),
            'pagination' => $pagination,
            'errors' => $errors,
        ));
    }

    /**
     * Get the genes
     *
     * @return CActiveRecord[]
     */
    public function getGenes()
    {
        $this->total_items = PedigreeGene::model()->count(array('order' => 't.asc'));

        $this->pages = ceil($this->total_items / $this->items_per_page);

        return PedigreeGene::model()->findAll(array(
            'order' => 't.id asc',
            'limit' => $this->items_per_page,
        ));
    }

    /**
     * @todo delete this crazy.
     *
     * @return string
     */
    public function getUriAppend()
    {
        $return = '';
        foreach (array('date_from', 'date_to', 'include_bookings' => 0, 'include_reschedules' => 0, 'include_cancellations' => 0) as $token) {
            if (Yii::app()->request->getQuery($token)) {
                $return .= '&'.$token.'='.Yii::app()->request->getPost($token);
            }
        }

        return $return;
    }

    /**
     * @todo delete this dangerous crazy.
     *
     * @param $elements
     * @return mixed|string
     */
    public function getUri($elements)
    {
        $uri = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);

        $request = $_REQUEST;

        if (isset($elements['sortby']) && $elements['sortby'] == @$request['sortby']) {
            $request['order'] = (@$request['order'] == 'desc') ? 'asc' : 'desc';
        } elseif (isset($request['sortby']) && isset($elements['sortby']) && $request['sortby'] != $elements['sortby']) {
            $request['order'] = 'asc';
        }

        $first = true;
        foreach (array_merge($request, $elements) as $key => $value) {
            $uri .= $first ? '?' : '&';
            $first = false;
            $uri .= "$key=$value";
        }

        return $uri;
    }

    /**
     * Add a gene
     */
    public function actionAddGene()
    {
        $gene = new PedigreeGene();

        $errors = array();

        if (Yii::app()->request->isPostRequest) {
            if (Yii::app()->request->getPost('cancel')) {
                $this->redirect(array('/Genetics/default/genes'));
            }

            $gene->attributes = Yii::app()->request->getPost('PedigreeGene');

            if (!$gene->save()) {
                $errors = $gene->getErrors();
            } else {
                $this->redirect(array('/Genetics/default/genes'));
            }
        }

        $this->render('edit_gene', array(
            'gene' => $gene,
            'errors' => $errors,
        ));
    }

    /**
     * Edit a gene
     *
     * @param $id
     * @throws Exception
     */
    public function actionEditGene($id)
    {
        if (!$gene = PedigreeGene::model()->findByPk($id)) {
            throw new Exception("PedigreeGene not found: $id");
        }

        $errors = array();

        if (Yii::app()->request->isPostRequest) {
            if (Yii::app()->request->getPost('cancel')) {
                $this->redirect(array('/Genetics/default/genes'));
            }

            $gene->attributes = Yii::app()->request->getPost('PedigreeGene');

            if (!$gene->save()) {
                $errors = $gene->getErrors();
            } else {
                $this->redirect(array('/Genetics/default/genes'));
            }
        }

        $this->render('edit_gene', array(
            'gene' => $gene,
            'errors' => $errors,
        ));
    }
}
