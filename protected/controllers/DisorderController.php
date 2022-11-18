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
class DisorderController extends BaseController
{

    public $layout = 'userDisorder';

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('index', 'view', 'autocomplete','getcommonlyuseddiagnoses'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions'=>array('create','update','index','view', 'delete', 'autocomplete', 'getcommonlyuseddiagnoses'),
                'users'=>array('TaskCreateDisorder', 'admin'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    protected function setOptionsForGenericAdmin($options, $model)
    {
        $options = array_merge(array(
            'label_field' => $model::SELECTION_LABEL_FIELD,
            'extra_fields' => array(),
            'filter_fields' => array(),
            'filters_ready' => true,
            'label_extra_field' => false,
            'description' => '',
            'div_wrapper_class' => 'cols-full',
        ), $options);

        $columns = $model::model()->metadata->columns;

        $options = $this->addExtraFieldsToOptions($options, $columns);
        $options['display_order'] = false;
        return $options;
    }

    protected function renderPartialForGenericAdmin($key, $model, $options, $title, $errors)
    {
        $items = array($key => new $model());
        $options['get_row'] = true;
        if ($model::model()->hasAttribute('display_order')) {
            $options['display_order'] = true;
        }
        $this->renderPartial('//admin/generic_admin', array(
            'title' => $title,
            'model' => $model,
            'items' => $items,
            'errors' => $errors,
            'options' => $options,
        ), false, true);
    }

    protected function setItemAttributesForGenericAdmin(&$item, $options, $i, $j, &$attributes, &$errors, $new, $model)
    {
        $item->{$options['label_field']} = $_POST[$options['label_field']][$i];
        if ($item->hasAttribute('display_order')) {
            $options['display_order'] = true;
            $item->display_order = $j + 1;
        }

        if (array_key_exists('active', $attributes)) {
            $item->active = (isset($_POST['active'][$i]) || $item->isNewRecord) ? 1 : 0;
        }

        foreach ($options['extra_fields'] as $field) {
            $name = $field['field'];
            if (!array_key_exists($name, $attributes)) {
                // getAttributes doesn't return relations, so this sets this up
                // to enable the change check below. This will give false positives for saves
                // but is a simple solution for now.
                $attributes[$name] = $item->$name;
            }
            $item->$name = @$_POST[$name][$i];
        }

        if ($item->hasAttribute('default')) {
            $item->default = (isset($_POST['default']) && $_POST['default'] !== 'NONE' && $_POST['default'] == $j) ?  1: 0;
        }

        foreach ($options['filter_fields'] as $field) {
            $item->{$field['field']} = $field['value'];
        }

        if ($new || $item->getAttributes() != $attributes) {
            if (!$item->save()) {
                $errors = $item->getErrors();
                foreach ($errors as $error) {
                    $errors[$i] = $error[0];
                }
            }
            Audit::add('admin', $new ? 'create' : 'update', $item->primaryKey, null, array(
                'module' => (is_object($this->module)) ? $this->module->id : 'core',
                'model' => $model::getShortModelName(),
            ));
        }
    }

    protected function renderForGenericAdmin($title, $model, $items, $errors, $options)
    {
        $this->render('//admin/generic_admin', array(
            'title' => $title,
            'model' => $model,
            'items' => $items,
            'errors' => $errors,
            'options' => $options,
        ));
    }

    protected function postRequest($model, $options, &$errors)
    {
        $tx = Yii::app()->db->beginTransaction();
        $j = 0;

        foreach ((array) @$_POST['id'] as $i => $id) {
            $item = ($id) ? $model::model()->findByPk($id) : new $model();
            $new = ($id) ? false : true ;
            $attributes = $item->getAttributes();
            if (!empty($_POST[$options['label_field']][$i])) {
                $this->setItemAttributesForGenericAdmin($item, $options, $i, $j, $attributes, $errors, $new, $model);
                $items[] = $item;
                ++$j;
            }
        }
        $errors = $this->amendCommonOphthalmicDisorderGroups($model, $options, $errors, $items, $tx);
    }

    protected function genericAdmin($title, $model, array $options = array(), $key = null)
    {
        $options = $this->setOptionsForGenericAdmin($options, $model);
        $items = array();
        $errors = array();
        if ($key !== null) {
            $this->renderPartialForGenericAdmin($key, $model, $options, $title, $errors);
        } else {
            if ($options['filters_ready']) {
                if (Yii::app()->request->isPostRequest) {
                    $this->postRequest($model, $options, $errors);
                } else {
                    list($options, $items) = $this->optionsFiltersNotAvailable($model, $options);
                }
            }
            $this->renderForGenericAdmin($title, $model, $items, $errors, $options);
        }
    }

    private function addFilterCriteria(CDbCriteria $crit, array $filter_fields)
    {
        foreach ($filter_fields as $filter_field) {
            $crit->compare($filter_field['field'], $filter_field['value']);
        }
    }

    /**
     * Generate array structure of disorder for JSON structure return
     *
     * @param Disorder $disorder
     * @return array
     */
    protected function disorderStructure(Disorder $disorder)
    {
        return array(
            'label' => $disorder->term,
            'value' => $disorder->term,
            'id' => $disorder->id,
            'is_diabetes' => Disorder::model()->ancestorIdsMatch(array($disorder->id), Disorder::$SNOMED_DIABETES_SET),
            'is_glaucoma' => (strpos(strtolower($disorder->term), 'glaucoma') !== false),
        );
    }


    /**
     * Lists all disorders for a given search term.
     */
    public function actionAutoComplete()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $criteria = new CDbCriteria();
            $params = array();
            if (isset($_GET['term']) && $term = $_GET['term']) {
                $criteria->addCondition(array('LOWER(term) LIKE :term', 'LOWER(aliases) LIKE :term'), 'OR');
                $params[':term'] = '%'.strtolower(strtr($term, array('%' => '\%'))).'%';
            }
            $criteria->order = 'term';

            // Limit results
            $criteria->limit = '200';
            if (@$_GET['code']) {
                if (@$_GET['code'] == 'systemic') {
                    $criteria->addCondition('specialty_id is null');
                } else {
                    $criteria->join = 'join specialty on specialty_id = specialty.id AND specialty.code = :specode';
                    $params[':specode'] = $_GET['code'];
                }
            }

            $criteria->params = $params;

            $disorders = Disorder::model()->active()->findAll($criteria);
            $return = array();
            foreach ($disorders as $disorder) {
                $return[] = $this->disorderStructure($disorder);
            }
            $this->renderJSON($return);
        }
    }

    /**
     * @param $type
     */
    public function actionGetCommonlyUsedDiagnoses($type)
    {
        $return = array();
        if ($type === 'systemic') {
            foreach (CommonSystemicDisorder::getDisorders() as $disorder) {
                $return[] = $this->disorderStructure($disorder);
            };
        } else {
            $return = $this->actionGetCommonOphthalmicDisorders(Yii::app()->session['selected_firm_id']);
        }

        $this->renderJSON($return);
        Yii::app()->end();
    }

    public function actionDetails()
    {
        if (!isset($_REQUEST['name'])) {
            echo CJavaScript::jsonEncode(false);
        } else {
            $disorder = Disorder::model()->find('fully_specified_name = ? OR term = ?', array($_REQUEST['name'], $_REQUEST['name']));
            if ($disorder) {
                echo $disorder->id;
            } else {
                echo CJavaScript::jsonEncode(false);
            }
        }
    }

    public function actionIsCommonOphthalmic($id)
    {
        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

        if ($cd = CommonOphthalmicDisorder::model()->find('disorder_id=? and subspecialty_id=? and institution_id=?', array($id, $firm->serviceSubspecialtyAssignment->subspecialty_id, Institution::model()->getCurrent()))) {
            echo "<option value=\"$cd->disorder_id\" data-order=\"{$cd->display_order}\">".$cd->disorder->term.'</option>';
        }
    }

    /**
     * Gets the common ophthalmic disorders for the given firm.
     *
     * @param int $firm_id
     *
     * @return array
     * @throws \CException
     */
    public function actionGetCommonOphthalmicDisorders($firm_id)
    {
        if (empty($firm_id)) {
            throw new \CException('Firm is required');
        }
        $firm = \Firm::model()->findByPk($firm_id);
        if ($firm) {
            return \CommonOphthalmicDisorder::getListByGroupWithSecondaryTo($firm);
        }
    }

    public function actionEditSecondaryToCommonOphthalmicDisorder()
    {
        $errors = array();
        $parent_id = Yii::app()->request->getParam('parent_id', 1);

        if (Yii::app()->request->isPostRequest) {
            $transaction = Yii::app()->db->beginTransaction();

            $display_orders = Yii::app()->request->getParam('display_order', array());
            $disorders = Yii::app()->request->getParam('SecondaryToCommonOphthalmicDisorder', array());

            $ids = array();
            foreach ($disorders as $key => $disorder) {
                $common_ophtalmic_disorder = SecondaryToCommonOphthalmicDisorder::model()->findByPk($disorder['id']);
                if (!$common_ophtalmic_disorder) {
                    $common_ophtalmic_disorder = new SecondaryToCommonOphthalmicDisorder;
                    $disorder['id'] = null;
                }

                $common_ophtalmic_disorder->attributes = $disorder;
                $common_ophtalmic_disorder->display_order = $display_orders[$key];

                //$_GET['parent_id'] must be present, we do not use the default value 1
                $common_ophtalmic_disorder->parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : null;

                if (!$common_ophtalmic_disorder->save()) {
                    $errors[] = $common_ophtalmic_disorder->getErrors();
                }

                $ids[$common_ophtalmic_disorder->id] = $common_ophtalmic_disorder->id;
            }

            if (empty($errors)) {
                //Delete items
                $criteria = new CDbCriteria();

                if ($ids) {
                    $criteria->addNotInCondition('id', array_map(function ($id) {
                        return $id;
                    }, $ids));
                }

                $criteria->compare('parent_id', $parent_id);

                $to_delete = SecondaryToCommonOphthalmicDisorder::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION, $criteria);

                foreach ($to_delete as $item) {
                    if (!$item->delete()) {
                        throw new Exception("Unable to delete SecondaryToCommonOphthalmicDisorder:{$item->primaryKey}");
                    }
                    Audit::add('admin', 'delete', $item->primaryKey, null, array(
                        'module' => (is_object($this->module)) ? $this->module->id : 'core',
                        'model' => SecondaryToCommonOphthalmicDisorder::getShortModelName(),
                    ));
                }

                $transaction->commit();

                Yii::app()->user->setFlash('success', 'List updated.');
            } else {
                foreach ($errors as $error) {
                    foreach ($error as $attribute => $error_array) {
                        $display_errors = '<strong>'.$common_ophtalmic_disorder->getAttributeLabel($attribute) . ':</strong> ' . implode(', ', $error_array);
                        Yii::app()->user->setFlash('warning.failure-' . $attribute, $display_errors);
                    }
                }

                $transaction->rollback();
            }
            $this->redirect(Yii::app()->request->url);
        }

        $generic_admin = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.widgets.js') . '/GenericAdmin.js', true);
        Yii::app()->getClientScript()->registerScriptFile($generic_admin);

        Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.UI.DiagnosesSearch.js'), ClientScript::POS_END);

        $criteria = new CDbCriteria();
        $criteria->compare('parent_id', $parent_id);

        $this->render('editSecondaryToCommonOphthalmicdisorder', array(
            'dataProvider' => new CActiveDataProvider('SecondaryToCommonOphthalmicDisorder', array(
                'criteria' => $criteria,
                'pagination' => false,
            )),
            'parent_id' => $parent_id,
        ));
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view', array(
            'model'=>$this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model=new Disorder;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Disorder'])) {
            foreach ($_POST['Disorder'] as $key=> $value) {
                $model->$key = $value;
            }
            $model->attributes=$_POST['Disorder'];
            if ($model->save()) {
                $this->redirect(array('view','id'=>$model->id));
            }
        }

        $this->render('create', array(
            'model'=>$model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model=$this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Disorder'])) {
            foreach ($_POST['Disorder'] as $key=> $value) {
                $model->$key = $value;
            }

            if ($model->save()) {
                $this->redirect(array('view','id'=>$model->id));
            }
        }

        $this->render('update', array(
            'model'=>$model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
            $this->loadModel($id)->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax'])) {
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $model=new Disorder('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Disorder'])) {
            $model->attributes=$_GET['Disorder'];
        }

        if (Yii::app()->user->checkAccess('TaskCreateDisorder') || Yii::app()->user->checkAccess('admin')) {
            $this->render('admin', array(
                'model'=>$model,
            ));
        } else {
            $this->render('index', array(
                'model'=>$model,
            ));
        }
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Disorder the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model=Disorder::model()->findByPk($id);
        if ($model===null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Disorder $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax']==='disorder-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function getSpecialtyNameFromId($data)
    {
        $specialties = Specialty::model()->findAll();
        if ($data->specialty_id !== null || $data->specialty_id != '') {
            foreach ($specialties as $specialty) {
                if ($specialty->id == $data->specialty_id) {
                    return $specialty->name;
                }
            }
        }
        return 'NA';
    }

    /**
     * @param array $options
     * @param $columns
     * @return array
     */
    protected function addExtraFieldsToOptions(array $options, $columns)
    {
        foreach ($options['extra_fields'] as $extraKey => $extraField) {
            switch ($extraField['type']) {
                case 'lookup':
                    $options['extra_fields'][$extraKey]['allow_null'] = $columns[$extraField['field']]->allowNull;
                    break;
            }
            if ($extraField['field'] === $options['label_field']) {
                $options['label_extra_field'] = true;
            }
        }

        foreach ($options['filter_fields'] as $filterKey => $filterField) {
            $options['filter_fields'][$filterKey]['value'] = null;
            if (isset($_GET[$filterField['field']])) {
                $options['filter_fields'][$filterKey]['value'] = $_GET[$filterField['field']];
            }

            if ($options['filter_fields'][$filterKey]['value'] === null && !$columns[$filterField['field']]->allowNull) {
                $options['filters_ready'] = false;
            }
        }
        return $options;
    }

    /**
     * @param $model
     * @param array $options
     * @param $errors
     * @param $items
     * @param $tx
     * @return mixed
     * @throws Exception
     */
    protected function amendCommonOphthalmicDisorderGroups($model, array $options, $errors, $items, $tx)
    {
        if (empty($errors)) {
            $criteria = new CDbCriteria();

            if ($items) {
                $criteria->addNotInCondition('id', array_map(function ($i) {
                    return $i->id;
                }, $items));
            }
            $this->addFilterCriteria($criteria, $options['filter_fields']);

            $to_delete = $model::model()->findAll($criteria);
            foreach ($to_delete as $i => $item) {
                if (!$item->delete()) {
                    $tx->rollback();
                    $error = $item->getErrors();
                    foreach ($error as $e) {
                        $errors[$i] = $e[0];
                    }

                    Yii::app()->user->setFlash('error.error', implode('<br/>', $errors));
                    $this->redirect(Yii::app()->request->url);
                }
                Audit::add('admin', 'delete', $item->primaryKey, null, array(
                    'module' => (is_object($this->module)) ? $this->module->id : 'core',
                    'model' => $model::getShortModelName(),
                ));
            }

            $tx->commit();

            Yii::app()->user->setFlash('success', 'List updated.');

            $this->redirect(Yii::app()->request->url);
        } else {
            $tx->rollback();
        }
        return $errors;
    }

    /**
     * @param $model
     * @param array $options
     * @return array
     */
    protected function optionsFiltersNotAvailable($model, array $options)
    {
        $order = array();

        if ($model::model()->hasAttribute('display_order')) {
            $order = array('order' => 'display_order');
            $options['display_order'] = true;
        }
        $crit = new CDbCriteria($order);
        $this->addFilterCriteria($crit, $options['filter_fields']);
        $items = $model::model()->findAll($crit);
        return array($options, $items);
    }
}
