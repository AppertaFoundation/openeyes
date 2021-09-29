<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class AdminController extends ModuleAdminController
{
    public $defaultAction = 'ViewDecisionTrees';

    public $group = 'Therapy Application';

    /**
     * simple method for popup management.
     *
     * @param $redirect URL to redirect from closing popup
     */
    private function popupCloseAndRedirect($redirect)
    {
        $this->render('popupcloseandredirect', array(
                'url' => $redirect,
        ));
    }

    // -- Diagnoses actions --

    /**
     * View the level diagnoses.
     */
    public function actionViewDiagnoses($parent_id = null)
    {
        $this->jsVars['OphCoTherapyapplication_sort_url'] = $this->createUrl('sortDiagnoses');

        $criteria = new CDbCriteria();
        $parent = null;

        if ($parent_id) {
            $parent = OphCoTherapyapplication_TherapyDisorder::model()->findByPk((int) $parent_id);
            $criteria->condition = 'parent_id = :pid';
            $criteria->params = array(':pid' => $parent_id);
        } else {
            $criteria->condition = 'parent_id is NULL';
        }
        $criteria->order = 'display_order asc';

        $diagnoses = OphCoTherapyapplication_TherapyDisorder::model()->findAll($criteria);

        Audit::add('admin', 'list', $parent_id, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_TherapyDisorder'));

        $this->render('list_therapy_disorder', array(
                'model_class' => 'OphCoTherapyapplication_TherapyDisorder',
                'model_list' => $diagnoses,
                'title' => $parent_id ? 'Level 2 Disorders for '.$parent->disorder->term : 'Level 1 Disorders',
                'parent_id' => $parent_id,
        ));
    }

    /**
     * add a diagnosis to the specified parent if it doesn't already exist there.
     *
     * @throws Exception
     */
    public function actionAddDiagnosis()
    {
        $parent = null;
        if (@$_POST['parent_id'] && !$parent = OphCoTherapyapplication_TherapyDisorder::model()->findByPk((int) $_POST['parent_id'])) {
            throw new Exception('Cannot find parent with id '.$parent->id);
        }

        if (!$disorder = Disorder::model()->findByPk((int) @$_POST['disorder_id'])) {
            throw new Exception('Unknown disorder id '.@$_POST['disorder_id']);
        }

        // check not already been added
        $criteria = new CDbCriteria();
        $criteria->condition = 'disorder_id = :did';
        $criteria->params = array(':did' => $disorder->id);

        if ($parent) {
            $criteria->condition .= ' AND parent_id = :pid';
            $criteria->params[':pid'] = $parent->id;
        } else {
            $criteria->condition .= ' AND parent_id is NULL';
        }

        if (OphCoTherapyapplication_TherapyDisorder::model()->find($criteria)) {
            Yii::app()->user->setFlash('failure', 'Disorder already set at this level');
        } else {
            $therapy_disorder = new OphCoTherapyapplication_TherapyDisorder();
            $therapy_disorder->disorder_id = $disorder->id;

            $query = 'SELECT MAX(display_order) AS maxdisplay FROM '.OphCoTherapyapplication_TherapyDisorder::model()->tableName();

            if ($parent) {
                $therapy_disorder->parent_id = $parent->id;
                $query .= ' WHERE parent_id = '.$parent->id;
            }

            $val = Yii::app()->db->createCommand($query)->queryRow();
            $therapy_disorder->display_order = $val['maxdisplay'] + 1;
            if (!$therapy_disorder->save()) {
                throw new Exception('Unable to save new therapy disorder '.print_r($therapy_disorder->getErrors(), true));
            }
            Yii::app()->user->setFlash('success', 'Disorder added');
            Audit::add('admin', 'create', $therapy_disorder->id, null, array(
                'module' => 'OphCoTherapyapplication',
                'model' => 'OphCoTherapyapplication_TherapyDisorder',
            ));
        }

        $this->redirect(array('viewdiagnoses', 'parent_id' => @$_POST['parent_id']));
    }

    public function actionDeleteDiagnoses()
    {
        foreach (OphCoTherapyapplication_TherapyDisorder::model()->findAllByPK($_POST['diagnoses']) as $diagnosis) {
            $parent_id = $diagnosis->parent_id;
            $disorder_id = $diagnosis->disorder_id;

            // check for and delete any children first
            $criteria = new CDbCriteria();
            $criteria->condition = 'parent_id = :pid';
            $criteria->params = array(':pid' => $diagnosis->id);
            $transaction = Yii::app()->db->beginTransaction();
            try {
                if ($children = OphCoTherapyapplication_TherapyDisorder::model()->findAll($criteria)) {
                    foreach ($children as $child) {
                        if (!$child->delete()) {
                            throw new Exception('unable to delete child diagnosis '.$child->disorder->term.':'.print_r($child->getErrors(), true));
                        }
                    }
                }

                if (!$diagnosis->delete()) {
                    throw new Exception('unable to delete diagnosis'.print_r($diagnosis->getErrors(), true));
                }
                $transaction->commit();
                Audit::add('admin', 'delete', $disorder_id, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_TherapyDisorder'));
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }
        }

        echo '1';
    }

    /**
     * sort the diagnoses (note that the diagnoses submitted may be a subset of all diagnoses, as the display_order
     * is set on the subsets for diagnoses that have parents.
     *
     * @param string $parent_id
     */
    public function actionSortDiagnoses()
    {
        if (!empty($_POST['order'])) {
            foreach ($_POST['order'] as $i => $id) {
                if ($disorder = OphCoTherapyapplication_TherapyDisorder::model()->findByPk($id)) {
                    $disorder->display_order = $i + 1;
                    if (!$disorder->save()) {
                        throw new Exception('Unable to save drug: '.print_r($disorder->getErrors(), true));
                    }
                }
            }
        }
    }

    // -- Treatment actions --
    /**
     * View all the treatments that are defined.
     */
    public function actionViewTreatments()
    {
        Audit::add('admin', 'list', null, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_Treatment'));

        $this->render('list_treatment', array(
                'model_class' => 'OphCoTherapyapplication_Treatment',
                'model_list' => OphCoTherapyapplication_Treatment::model()->findAll(),
                'title' => 'Treatments',
        ));
    }

    /**
     * update the specified treatment.
     *
     * @param int $id
     */
    public function actionEditTreatment($id)
    {
        $model = OphCoTherapyapplication_Treatment::model()->findByPk((int) $id);

        if (isset($_POST['OphCoTherapyapplication_Treatment'])) {
            $model->attributes = $_POST['OphCoTherapyapplication_Treatment'];

            if ($model->save()) {
                Audit::add('admin', 'update', $id, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_Treatment'));
                Yii::app()->user->setFlash('success', 'Treatment updated');

                $this->redirect(array('viewtreaTments'));
            }
        }

        $this->render('update', array(
                'model' => $model,
                'title' => 'Treatment',
                'cancel_uri' => '/OphCoTherapyapplication/admin/viewTreatments',
        ));
    }

    /**
     * create a treatment.
     */
    public function actionAddTreatment()
    {
        $model = new OphCoTherapyapplication_Treatment();

        if (isset($_POST['OphCoTherapyapplication_Treatment'])) {
            // do the actual create
            $model->attributes = $_POST['OphCoTherapyapplication_Treatment'];

            if ($model->save()) {
                Audit::add('admin', 'create', $model->id, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_Treatment'));
                Yii::app()->user->setFlash('success', 'Treatment created');

                $this->redirect(array('viewtreaTments'));
            }
        }

        $this->render('create', array(
                'model' => $model,
                'title' => 'Treatment',
                'cancel_uri' => '/OphCoTherapyapplication/admin/viewTreatments',
        ));
    }

    public function actionDeleteTreatments()
    {
        $result = 1;

        foreach (OphCoTherapyapplication_Treatment::model()->findAllByPK($_POST['treatments']) as $treatment) {
            if (!$treatment->delete()) {
                $result = 0;
            }
        }

        echo $result;
    }

    // -- decision tree actions --

    public function actionViewDecisionTrees()
    {
        $data_provider = new CActiveDataProvider('OphCoTherapyapplication_DecisionTree', ['criteria' => [
            'condition' => 'institution_id is null OR institution_id = '. Yii::app()->session['selected_institution_id'],
        ]]);

        Audit::add('admin', 'list', null, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_DecisionTree'));

        $this->render('list', array(
            'dataProvider' => $data_provider,
            'title' => 'Decision Trees',
        ));
    }

    public function actionViewDecisionTree($id)
    {
        $model = OphCoTherapyapplication_DecisionTree::model()->findByPk((int) $id);

        if (@$_GET['node_id']) {
            $node = OphCoTherapyapplication_DecisionTreeNode::model()->findByPk((int) $_GET['node_id']);
            if ($node->decisiontree_id != $model->id) {
                throw Exception('mismatched node and decision tree!');
            }
        } else {
            $node = $model->getRootNode();
        }

        Audit::add('admin', 'view', $id, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_DecisionTree'));

        $this->render('view_decision_tree', array(
                'model' => $model,
                'node' => $node,
        ));
    }

    public function actionCreateOphCoTherapyapplication_DecisionTree()
    {
        $model = new OphCoTherapyapplication_DecisionTree();

        if (isset($_POST['OphCoTherapyapplication_DecisionTree'])) {
            // do the actual create
            $model->attributes = $_POST['OphCoTherapyapplication_DecisionTree'];

            if ($model->save()) {
                Audit::add('admin', 'create', $model->id, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_DecisionTree'));
                Yii::app()->user->setFlash('success', 'Decision Tree created');

                $this->redirect(array('viewdecisiontree', 'id' => $model->id));
            }
        }

        $this->render('create', array(
                'model' => $model,
                'title' => 'Decision Tree',
                'cancel_uri' => $this->createUrl('admin/viewDecisionTrees'),
        ));
    }

    // decision tree node actions

    public function actionCreateDecisionTreeNode($id)
    {
        $tree = OphCoTherapyapplication_DecisionTree::model()->findByPk((int) $id);

        $parent = null;
        if (isset($_GET['parent_id'])) {
            $parent = OphCoTherapyapplication_DecisionTreeNode::model()->findByPk((int) $_GET['parent_id']);
        }

        $model = new OphCoTherapyapplication_DecisionTreeNode();

        if (isset($_POST['OphCoTherapyapplication_DecisionTreeNode'])) {
            $model->attributes = $_POST['OphCoTherapyapplication_DecisionTreeNode'];
            $model->decisiontree_id = $id;

            if ($parent) {
                $model->parent_id = $parent->id;
            }

            if ($model->save()) {
                Audit::add('admin', 'create', $model->id, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_DecisionTreeNode'));
                Yii::app()->user->setFlash('success', 'Decision Tree node created');

                $this->popupCloseAndRedirect(Yii::app()->createUrl('OphCoTherapyapplication/admin/viewdecisiontree', array('id' => $model->decisiontree_id)).'/?node_id='.$model->id);
            }
        }

        $this->renderPartial('create', array(
                'model' => $model,
                'decisiontree' => $tree,
                'title' => 'Node for '.$tree->name,
        ));
    }

    public function actionUpdateDecisionTreeNode($id)
    {
        $this->layout = '//layouts/admin_popup';

        $model = OphCoTherapyapplication_DecisionTreeNode::model()->findByPk((int) $id);

        if (isset($_POST['OphCoTherapyapplication_DecisionTreeNode'])) {
            $model->attributes = $_POST['OphCoTherapyapplication_DecisionTreeNode'];

            if ($model->save()) {
                Audit::add('admin', 'update', $model->id, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_DecisionTreeNode'));
                Yii::app()->user->setFlash('success', 'Decision Tree node updated');

                $this->popupCloseAndRedirect(Yii::app()->createUrl('OphCoTherapyapplication/admin/viewdecisiontree', array('id' => $model->decisiontree_id)).'/?node_id='.$model->id);
            }
        }

        $this->render('update', array(
                'model' => $model,
                'cancel_uri' => '/OphCoTherapyapplication/admin/viewDecisionTrees',
                'title' => 'Node Edit',
        ));
    }

    public function actionCreateDecisionTreeNodeRule($id)
    {
        $node = OphCoTherapyapplication_DecisionTreeNode::model()->findByPk((int) $id);

        $model = new OphCoTherapyapplication_DecisionTreeNodeRule();
        $model->node = $node;

        if (isset($_POST['OphCoTherapyapplication_DecisionTreeNodeRule'])) {
            $model->attributes = $_POST['OphCoTherapyapplication_DecisionTreeNodeRule'];
            $model->node_id = $node->id;

            if ($model->save()) {
                Audit::add('admin', 'create', $model->id, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_DecisionTreeNodeRule'));
                Yii::app()->user->setFlash('success', 'Decision Tree Node rule created');

                $this->redirect(array('viewdecisiontree', 'id' => $node->decisiontree_id, 'node_id' => $node->id));
            }
        }

        $this->renderPartial('create', array(
            'model' => $model,
            'node' => $node,
            'title' => 'Rule for '.($node->outcome ? $node->outcome->name.' Outcome' : $node->question),
        ));
    }

    public function actionUpdateDecisionTreeNodeRule($id)
    {
        $model = OphCoTherapyapplication_DecisionTreeNodeRule::model()->findByPk((int) $id);

        if (isset($_POST['OphCoTherapyapplication_DecisionTreeNodeRule'])) {
            $model->attributes = $_POST['OphCoTherapyapplication_DecisionTreeNodeRule'];

            if ($model->save()) {
                Audit::add('admin', 'update', $model->id, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_DecisionTreeNodeRule'));
                Yii::app()->user->setFlash('success', 'Decision Tree Node Rule updated');

                $this->redirect(array('viewdecisiontree', 'id' => $model->node->decisiontree_id, 'node_id' => $model->node->id));
            }
        }

        $this->renderPartial('update', array(
                'model' => $model,
                'node' => $model->node,
                'title' => 'Rule for '.($model->node->outcome ? $model->node->outcome->name.' Outcome' : $model->node->question),
        ));
    }

    // -- File Collection actions --

    public function actionViewFileCollections()
    {
        Audit::add('admin', 'list', null, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_FileCollection'));

        $this->render('list_OphCoTherapyapplication_FileCollection', array(
                'model_class' => 'OphCoTherapyapplication_FileCollection',
                'model_list' => OphCoTherapyapplication_FileCollection::model()->findAll(['condition' => 'institution_id is null OR institution_id = '. Yii::app()->session['selected_institution_id']]),
                'title' => 'File Collections',
        ));
    }

    public function actionAddFileCollection()
    {
        $model = new OphCoTherapyapplication_FileCollection();

        if (isset($_POST['OphCoTherapyapplication_FileCollection'])) {
            $this->processFileCollectionForm($model);
        }

        $this->render('create', array(
                'model' => $model,
                'title' => 'File Collection',
                'cancel_uri' => '/OphCoTherapyapplication/admin/viewFileCollections',
        ));
    }

    public function actionViewOphCoTherapyapplication_FileCollection($id)
    {
        $model = OphCoTherapyapplication_FileCollection::model()->findByPk((int) $id);

        Audit::add('admin', 'view', $id, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_FileCollection'));

        $this->render('view_'.get_class($model), array(
                'model' => $model, ));
    }

    /**
     * processes uploaded files for a collection - will add errors to the collection if there are problems
     * with any of the files. Otherwise returns array of protected file ids that have been created.
     *
     * @param $collection
     * @param $uploaded_files
     *
     * @throws Exception
     *
     * @return array $protectedfile_ids
     */
    protected function processFileCollectionFileUpload($collection, $uploaded_files)
    {
        $files = array();
        foreach ($uploaded_files['tmp_name'] as $i => $f) {
            if (!empty($uploaded_files['error'][$i])) {
                $collection->addError('files', "file $i had an error");
            } elseif (!empty($f) && is_uploaded_file($f)) {
                $name = $uploaded_files['name'][$i];
                // check the file mimetype
                if (OphCoTherapyapplication_FileCollection::checkMimeType($f)) {
                    $files[] = array('tmpfile' => $f, 'name' => $name);
                } else {
                    $collection->addError('files', "File $name is not a valid filetype");
                }
            }
        }

        $pfs = array();
        $pf_ids = array();

        if (!count($collection->getErrors())) {
            foreach ($files as $fdet) {
                $pf = ProtectedFile::createFromFile($fdet['tmpfile']);
                $pf->name = $fdet['name'];
                if ($pf->save()) {
                    $pfs[] = $pf;
                    $pf_ids[] = $pf->id;
                } else {
                    $collection->addError('files', 'There was a problem storing file '.$pf->name);
                    Yii::log("couldn't save file object".print_r($pf->getErrors(), true), 'error');

                    // need to remove any protected files that have been created so far (note that because
                    // ProtectedFile affects the filesystem, we are relying on the delete clean up method)
                    foreach ($pfs as $pf) {
                        $pf->delete();
                    }
                    // return an empty array - no protected files successfully created.
                    return array();
                }
            }
        }

        return $pf_ids;
    }

    /**
     * abstraction to process FileCollection form.
     *
     * @param OphCoTherapyapplication_FileCollection $model
     * @param string                                 $audit_type
     */
    protected function processFileCollectionForm($model, $audit_type = 'create')
    {
        $model->attributes = $_POST['OphCoTherapyapplication_FileCollection'];

        // validate the model
        $model->validate();

        $transaction = Yii::app()->getDb()->beginTransaction();
        // slightly complex rollback process because of files being copied into the protected file store
        // we want to be able to roll this back as well as the db process.
        try {
            $pf_ids = $this->processFileCollectionFileUpload($model, $_FILES['OphCoTherapyapplication_FileCollection_files']);
            if (!count($model->getErrors())) {
                if ($model->save()) {
                    // because this might be an update, we get the current files on the model so that we don't remove files
                    // from it
                    $curr_pf_ids = array();
                    foreach ($model->files as $file) {
                        $curr_pf_ids[] = $file->id;
                    }
                    $model->updateFiles(array_merge($curr_pf_ids, $pf_ids));

                    Audit::add('admin', $audit_type, $model->id, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_FileCollection'));
                    Yii::app()->user->setFlash('success', 'File Collection created');

                    $transaction->commit();
                    $this->redirect(array('viewfilecollections'));
                }
            }

            // clear out any protected files that might have been created.
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $pf_ids);
            foreach (ProtectedFile::model()->findAll($criteria) as $pf) {
                $pf->delete();
            }
            // if we've got this far, something is amiss
            $transaction->rollback();
        } catch (Exception $e) {
            Yii::log('OphCoTherapyapplication_FileCollection creation error: '.$e->getMessage(), 'error');
            Yii::app()->user->setFlash('error', 'An unexpected error has occurred');

            // clear out any protected files that might have been created.
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $pf_ids);
            foreach (ProtectedFile::model()->findAll($criteria) as $pf) {
                $pf->delete();
            }

            $transaction->rollback();
        }
    }

    /**
     * action for updating file collection identified by $id.
     *
     * @param int $id
     */
    public function actionEditFileCollection($id)
    {
        $model = OphCoTherapyapplication_FileCollection::model()->findByPk((int) $id);
        $this->jsVars['filecollection_id'] = $model->id;

        if (isset($_POST['OphCoTherapyapplication_FileCollection'])) {
            $this->processFileCollectionForm($model, 'update');
        }
        Audit::add('admin', 'view', $id, null, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_FileCollection'));

        $this->render('create', array(
                'model' => $model,
                'title' => 'File Collection',
                'cancel_uri' => '/OphCoTherapyapplication/admin/viewFileCollections',
            ));
    }

    public function actionRemoveFileCollection_File()
    {
        try {
            if ($collection = OphCoTherapyapplication_FileCollection::model()->findByPk(@$_GET['filecollection_id'])) {
                if ($collection->removeFileById(@$_GET['file_id'])) {
                    $this->renderJSON(array('success' => true));
                }
            }
        } catch (Exception $e) {
            Yii::log("couldn't remove file (".@$_GET['file_id'].') from collection ('.@$_GET['filecollection_id'].')'.$e->getMessage(), 'error');
            $this->renderJSON(array('success' => false));
        }
    }

    public function actionDeleteFileCollections()
    {
        $result = 1;

        foreach ($_POST['file_collections'] as $file_collection_id) {
            try {
                if ($collection = OphCoTherapyapplication_FileCollection::model()->findByPk($file_collection_id)) {
                    foreach ($collection->file_assignments as $file_assignment) {
                        if (!$collection->removeFileById($file_assignment->file_id)) {
                            $result = 0;
                        }
                    }
                    if (!$collection->delete()) {
                        $result = 0;
                    }
                }
            } catch (Exception $e) {
                Yii::log("couldn't remove file collection $file_collection_id: ".$e->getMessage(), 'error');
                $result = 0;
            }
        }

        echo $result;
    }

    public function actionViewEmailRecipients()
    {
        Audit::add('admin', 'list', null, false, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_Email_Recipient'));

        $this->render('list_OphCoTherapyapplication_Email_Recipient', array(
                'model_class' => 'OphCoTherapyapplication_Email_Recipient',
                'model_list' => OphCoTherapyapplication_Email_Recipient::model()->findAll([
                    'with' => 'site',
                    'condition' => 't.institution_id = :institution_id OR t.site_id is null OR site.institution_id = :institution_id',
                    'params' => [':institution_id' => Yii::app()->session['selected_institution_id']],
                    'order' => 'display_order asc',
                ]),
                'title' => 'Email recipients',
        ));
    }

    public function actionAddEmailRecipient()
    {
        $model = new OphCoTherapyapplication_Email_Recipient();

        if (isset($_POST['OphCoTherapyapplication_Email_Recipient'])) {
            // do the actual create
            $model->attributes = $_POST['OphCoTherapyapplication_Email_Recipient'];

            if ($model->save()) {
                Audit::add('admin', 'create', serialize($model->attributes), false, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_Email_Recipient'));
                Yii::app()->user->setFlash('success', 'Email recipient created');

                $this->redirect(array('viewEmailRecipients'));
            }
        }

        $this->render('create', array(
                'model' => $model,
                'title' => 'Email recipient',
                'cancel_uri' => '/OphCoTherapyapplication/admin/viewEmailRecipients',
        ));
    }

    public function actionEditEmailRecipient($id)
    {
        $model = OphCoTherapyapplication_Email_Recipient::model()->findByPk((int) $id);

        if (isset($_POST['OphCoTherapyapplication_Email_Recipient'])) {
            $model->attributes = $_POST['OphCoTherapyapplication_Email_Recipient'];

            if ($model->save()) {
                Audit::add('admin', 'update', serialize($model->attributes), false, array('module' => 'OphCoTherapyapplication', 'model' => 'OphCoTherapyapplication_Email_Recipient'));
                Yii::app()->user->setFlash('success', 'Email recipient updated');

                $this->redirect(array('viewEmailRecipients'));
            }
        }

        $this->render('update', array(
                'model' => $model,
                'title' => 'Email Recipient',
                'cancel_uri' => '/OphCoTherapyapplication/admin/viewEmailRecipients',
        ));
    }

    public function actionDeleteEmailRecipients()
    {
        $result = 1;

        foreach (OphCoTherapyapplication_Email_Recipient::model()->findAllByPK($_POST['email_recipients']) as $email_recipient) {
            if (!$email_recipient->delete()) {
                $result = 0;
            }
        }

        echo $result;
    }

    /**
     * utility function that should probably sit somewhere else, but is only for this template at the moment
     * calculates the byte size of the passed in value.
     *
     * @param $size_str
     *
     * @return int
     */
    public function returnBytes($size_str)
    {
        switch (substr($size_str, -1)) {
            case 'M':
            case 'm':
                return (int)$size_str * 1048576;
            case 'K':
            case 'k':
                return (int)$size_str * 1024;
            case 'G':
            case 'g':
                return (int)$size_str * 1073741824;
            default:
                return $size_str;
        }
    }
}
