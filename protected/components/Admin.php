<?php
/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * A class for generic admin actions on a model.
 */
class Admin
{
    /**
     * @var BaseActiveRecord
     */
    protected $model;

    /**
     * @var string
     */
    protected $modelName;

    /**
     * @var string
     */
    protected $modelDisplayName;

    /**
     * @var string
     */
    public $div_wrapper_class = 'cols-full';

    /**
     * Determines whether to display an 'All institutions' option in the institution drop-down list.
     *
     * @var boolean
     */
    public bool $has_global_institution_option = false;

    /**
     * @var string
     */
    protected $listTemplate = '//admin/generic/list';

    /**
     * @var string
     */
    protected $editTemplate = '//admin/generic/edit';

    /**
     * @var array
     */
    protected $listFields = array();

    /**
     * @var type
     */
    protected $listFieldsAction = 'edit';

    /**
     * @var array
     */
    protected $editFields = array();

    /**
     * @var array
     */
    protected $unsortableColumns = array('active');

    /**
     * @var BaseAdminController
     */
    protected $controller;

    /**
     * @var CPagination
     */
    protected $pagination;

    /**
     * @var ModelSearch
     */
    protected $search;

    /**
     * @var int
     */
    protected $modelId;

    /**
     * @var string
     */
    protected $customSaveURL;

    /**
     * Asset manager.
     *
     * @var CAssetManager
     */
    protected $assetManager;

    /**
     * Current HTTP request.
     *
     * @var CHttpRequest
     */
    protected $request;

    /**
     * @var string
     */

    protected $customAddURL;

    /**
     * @var string
     */
    protected $customCancelURL;

    /**
     * @var bool
     */
    protected $isSubList = false;

    /**
     * @var int
     */
    public $displayOrder = 0;

    /**
     * @var array
     */
    protected $filterFields = array();

    /**
     * Contains key value of parent object relation for a sublist.
     *
     * @var array
     */
    protected $subListParent = array();

    /**
     * Contains extra buttons next to save and cancel
     *
     * @var array
     */
    protected $extraButtons = array();

    /**
     * Forces to display title even if this is a sublist
     *
     * @var bool
     */

    protected $forceTitleDisplay = false;

    /**
     * Forces to display form on list page even if this is a sublist
     *
     * @var bool
     */

    protected $forceFormDisplay = false;

    /**
     * @return bool
     */
    public function isForceTitleDisplay()
    {
        return $this->forceTitleDisplay;
    }

    /**
     * @param bool $forceTitleDisplay
     * @return Admin
     */
    public function setForceTitleDisplay($forceTitleDisplay)
    {
        $this->forceTitleDisplay = $forceTitleDisplay;
        return $this;
    }

    /**
     * @return bool
     */
    public function isForceFormDisplay()
    {
        return $this->forceFormDisplay;
    }

    /**
     * @param bool $forceFormDisplay
     */
    public function setForceFormDisplay($forceFormDisplay)
    {
        $this->forceFormDisplay = $forceFormDisplay;
        return $this;
    }


    /**
     * @param $filters
     */
    public function setFilterFields($filters)
    {
        $this->filterFields = $filters;
    }

    /**
     * @return array
     */
    public function getFilterFields()
    {
        return $this->filterFields;
    }

    /**
     * @return BaseActiveRecord
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param BaseActiveRecord $model
     */
    public function setModel(BaseActiveRecord $model)
    {
        $this->model = $model;
        if (!$this->modelName) {
            $this->modelName = get_class($model);
        }
    }

    /**
     * @return string
     */
    public function getModelName()
    {
        return $this->modelName;
    }

    /**
     * @param string $modelName
     */
    public function setModelName($modelName)
    {
        $this->modelName = $modelName;
    }

    /**
     * @return string
     */
    public function getModelDisplayName()
    {
        if (isset($this->modelDisplayName)) {
            return $this->modelDisplayName;
        } else {
            return $this->modelName;
        }
    }

    /**
     * @param string $modelName
     */
    public function setModelDisplayName($displayName)
    {
        $this->modelDisplayName = $displayName;
    }

    /**
     * @return ModelSearch
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param ModelSearch $search
     */
    public function setSearch($search)
    {
        $this->search = $search;
    }

    /**
     * @return string
     */
    public function getListTemplate()
    {
        return $this->listTemplate;
    }

    /**
     * @param string $listTemplate
     */
    public function setListTemplate($listTemplate)
    {
        $this->listTemplate = $listTemplate;
    }

    /**
     * @return array
     */
    public function getListFields()
    {
        return $this->listFields;
    }

    /**
     * @param array $listFields
     */
    public function setListFields($listFields)
    {
        $this->listFields = $listFields;
    }

    /**
     * @return string
     */
    public function getListFieldsAction()
    {
        return $this->listFieldsAction;
    }

    /**
     * @param string $listFieldsAction
     */
    public function setListFieldsAction($listFieldsAction)
    {
        $this->listFieldsAction = $listFieldsAction;
    }

    /**
     * @return mixed
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @param mixed $pagination
     */
    public function setPagination($pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * @return int
     */
    public function getModelId()
    {
        return $this->modelId;
    }

    /**
     * @param int $modelId
     */
    public function setModelId($modelId)
    {
        $this->modelId = $modelId;
        $this->model = $this->model->findByPk($modelId);
    }

    /**
     * @return array
     */
    public function getEditFields()
    {
        return $this->editFields;
    }

    /**
     * @param array $editFields
     */
    public function setEditFields($editFields)
    {
        $this->editFields = $editFields;
    }

    /**
     * @return string
     */
    public function getEditTemplate()
    {
        return $this->editTemplate;
    }

    /**
     * @param string $editTemplate
     */
    public function setEditTemplate($editTemplate)
    {
        $this->editTemplate = $editTemplate;
    }

    /**
     * @param $saveURL
     */
    public function setCustomSaveURL($saveURL)
    {
        $this->customSaveURL = $saveURL;
    }

    /**
     * @return string
     */
    public function getCustomSaveURL()
    {
        return $this->customSaveURL;
    }

    /**
     * @return string
     */
    public function getCustomAddURL()
    {
        return $this->customAddURL;
    }

    /**
     * @param string $customAddURL
     * @return Admin
     */
    public function setCustomAddURL($customAddURL)
    {
        $this->customAddURL = $customAddURL;
        return $this;
    }

    /**
     * @param $cancelURL
     */
    public function setCustomCancelURL($cancelURL)
    {
        $this->customCancelURL = $cancelURL;
    }

    /**
     * @return string
     */
    public function getCustomCancelURL()
    {
        return $this->customCancelURL;
    }

    /**
     * @return bool
     */
    public function isSubList()
    {
        return $this->isSubList;
    }

    /**
     * @param bool $isSubList
     */
    public function setIsSubList($isSubList)
    {
        $this->isSubList = $isSubList;
    }

    /**
     * @return BaseAdminController
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param BaseAdminController $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return array
     */
    public function getUnsortableColumns()
    {
        return $this->unsortableColumns;
    }

    /**
     * @param array $unsortableColumns
     */
    public function setUnsortableColumns($unsortableColumns)
    {
        $this->unsortableColumns = $unsortableColumns;
    }

    /**
     * @return array
     */
    public function getSubListParent()
    {
        return $this->subListParent;
    }

    /**
     * @param array $subListParent
     */
    public function setSubListParent($subListParent)
    {
        $this->subListParent = $subListParent;
    }

    /**
     * Add extra button (next to 'save' and 'cancel'...)
     * e.g.: array('cancel-uri' => 'url' )
     * @param array $button
     */
    public function addExtraButton(array $button)
    {
        $this->extraButtons = $button;
    }

    public function getExtraButton()
    {
        return $this->extraButtons;
    }



    /**
     * @param BaseActiveRecord    $model
     * @param BaseController $controller
     */
    public function __construct(BaseActiveRecord $model, BaseController $controller)
    {
        $this->setModel($model);
        $this->controller = $controller;
        $this->search = new ModelSearch($this->model);
        $this->request = $request = Yii::app()->getRequest();
        $this->assetManager = Yii::app()->getAssetManager();
        $this->assetManager->registerScriptFile('js/oeadmin/OpenEyes.admin.js');
    }

    /**
     * Lists all the rows returned from the search in a table.
     *
     * @throws CHttpException
     */
    public function listModel($buttons = true)
    {
        if (!$this->model) {
            throw new CHttpException(500, 'Nothing to list');
        }

        $order = $this->request->getParam('d');

        if ($order == 0) {
            $this->displayOrder = 1;
        }

        $this->assetManager->registerScriptFile('/js/oeadmin/list.js');
        $this->audit('list');
        $this->pagination = $this->getSearch()->initPagination();
        if ($this->request->isAjaxRequest){
            $this->ajaxResponse();
        } else {
            $this->render($this->listTemplate, array('admin' => $this, 'displayOrder' => $this->displayOrder, 'buttons' => $buttons));
        }
    }

    /**
     * Edits the model, runs validation and renders the edit form.
     *
     * @throws CHttpException
     * @throws Exception
     */
    public function editModel($redirect = true, $partial = false)
    {
        $this->assetManager->registerScriptFile('/js/oeadmin/edit.js');
        $errors = array();
        if (Yii::app()->request->isPostRequest) {
            $post = Yii::app()->request->getPost(\CHtml::modelName($this->modelName));
            if (array_key_exists('id', $post) && $post['id']) {
                $this->model->attributes = $post;
            } else {
                $this->model = new $this->modelName();
                $this->model->attributes = $post;
            }

            foreach($this->editFields as $editField => $type){
                //widgets et al can be dealt with in the widget
                if (is_array($type)){
                    continue;
                }
                if (method_exists($this, $type.'Format')){
                    $this->model->$editField = $this->{$type.'Format'}($this->model->attributes[$editField]);
                }
            }

            if ($this->model->hasAttribute('display_order') && !$this->model->display_order) {
                $table = $this->model->tableName();

                $max_order = (int)Yii::app()->db->createCommand()
                    ->select('MAX(display_order)')
                    ->from($table)
                    ->queryScalar();
                $this->model->display_order = ++$max_order;
            }

            if (!$this->model->validate()) {
                $errors = $this->model->getErrors();
                if (!$redirect){
                    return false;
                }
            } else {

                // Model's id property must be null to be populated after save
                if ( empty($this->model->id) ){
                    $this->model->id = null;
                }
                if (!$this->model->save()) {
                    throw new CHttpException(500, 'Unable to save '.$this->modelName.': '.print_r($this->model->getErrors(), true));
                }
                $this->audit('edit', $this->model->id);
                if ($redirect){
                    $this->redirect();
                } else {
                    $this->model = $this->model->findByPk($this->model->id);
                    return true;
                }
            }
        } else {
            $defaults = Yii::app()->request->getParam('default', array());
            foreach ($defaults as $key => $defaultValue) {
                if ($this->model->hasAttribute($key)) {
                    $this->model->$key = $defaultValue;
                }
            }
        }

        if ($partial === false){
            $this->render($this->editTemplate, array('admin' => $this, 'errors' => $errors));
        } else {
            $this->controller->renderPartial($this->editTemplate, array('admin' => $this, 'errors' => $errors));
        }

    }

    /**
     * Deletes the models for which an array of IDs has been posted.
     */
    public function deleteModel()
    {
        $response = 1;
        if (Yii::app()->request->isPostRequest) {
            $post = Yii::app()->request->getPost($this->modelName);
            if (array_key_exists('id', $post) && is_array($post['id'])) {
                foreach ($post['id'] as $id) {
                    $model = $this->model->findByPk($id);
                    $attributes = $model->getAttributes();
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

                    if ($response == 1){
                        Audit::add(get_class($model),'delete', serialize($attributes), get_class($model). ' deleted');
                    }
                }
            }
        }

        echo $response;
    }

    /**
     * Saves the display_order.
     *
     * @throws CHttpException
     */
    public function sortModel()
    {
        if (!$this->model->hasAttribute('display_order')) {
            throw new CHttpException(400, 'This object cannot be ordered');
        }

        if (Yii::app()->request->isPostRequest) {
            $post = Yii::app()->request->getPost($this->modelName);
            $page = Yii::app()->request->getPost('page');
            if (!array_key_exists('display_order', $post) || !is_array($post['display_order'])) {
                throw new CHttpException(400, 'No objects to order were provided');
            }

            foreach ($post['display_order'] as $displayOrder => $id) {
                $model = $this->model->findByPk($id);
                if (!$model) {
                    throw new CHttpException(400, 'Object to be ordered not found');
                }
                //Add one because display_order not zero indexed.
                //Times by page number to get correct order across pages.
                $model->display_order = ($displayOrder + 1) * $page;
                if (!$model->validate()) {
                    throw new CHttpException(400, 'Order was invalid');
                }
                if (!$model->save()) {
                    throw new CHttpException(500, 'Unable to save order');
                }
            }
            $this->audit('sort');
        }
    }

    /**
     * Sets up search on all listed elements.
     */
    public function searchAll()
    {
        $searchArray = array('type' => 'compare', 'compare_to' => array());
        $searchFirst = '';
        foreach ($this->listFields as $field) {
            if (method_exists($this->model, 'get_'.$field)) {
                //we don't currently support searching on magic attributes not from the DB so continue
                continue;
            }
            if ($searchFirst === '') {
                $searchFirst = $field;
            } else {
                $searchArray['compare_to'][] = $field;
            }
        }
        $this->search->addSearchItem($searchFirst, $searchArray);
    }

    /**
     * @param $row
     * @param $attribute
     *
     * @return string
     */
    public function attributeValue($row, $attribute)
    {
        if ($row->hasAttribute($attribute)) {
            return $row->$attribute;
        }

        if (method_exists($row, $attribute)){
            return $row->$attribute();
        }

        if (strpos($attribute, '.')) {
            $splitAttribute = explode('.', $attribute);
            $relationTable = $splitAttribute[0];
            if (isset($row->$relationTable->{$splitAttribute[1]})) {
                return $row->$relationTable->{$splitAttribute[1]};
            }

            if (is_array($row->$relationTable)) {
                $manyResult = array();
                foreach ($row->$relationTable as $relationResult) {
                    if (isset($relationResult->{$splitAttribute[1]})) {
                        $manyResult[] = $relationResult->{$splitAttribute[1]};
                    }
                }

                return implode(',', $manyResult);
            }
        }

        if ($row->getMetaData()->hasRelation($attribute)) {
            $res = $row->$attribute;
            if (is_array($res)) {
                $res = implode(', ', $res);
            }
            return $res;
        }


        return '';
    }

    /**
     * Returns wether a given column is sortable or not.
     *
     * @param $attribute
     *
     * @return bool
     */
    public function isSortableColumn($attribute)
    {
        if ($this->isSubList) {
            return false;
        }

        if (in_array('display_order', $this->listFields, true)) {
            return false;
        }

        if (strpos($attribute, 'has_') === 0) {
            return false;
        }

        if (in_array($attribute, $this->unsortableColumns, true)) {
            return false;
        }

        return true;
    }

    /**
     * Takes the current URL, sets two values in it and returns it.
     *
     * @param $attribute
     * @param $order
     * @param $queryString
     *
     * @return string
     */
    public function sortQuery($attribute, $order, $queryString)
    {
        $queryArray = array();
        parse_str($queryString, $queryArray);
        $queryArray['c'] = $attribute;
        $queryArray['d'] = $order;

        return http_build_query($queryArray);
    }

    /**
     * @param       $relation
     * @param array $listFields
     * @return Admin
     */
    public function generateAdminForRelationList($relation, array $listFields)
    {
        $relatedModel = $this->relationClassFromRelation($relation);
        $relatedAdmin = new self($relatedModel, $this->controller);
        $relatedAdmin->setListFields($listFields);
        $relatedAdmin->setIsSubList(true);
        $relationField = $this->relationFieldFromRelation($relation);
        if ($relationField) {
            $criteria = $relatedAdmin->getSearch()->getCriteria();
            $criteria->addCondition($relationField.' = '.$this->model->id);
            $relatedAdmin->setSubListParent(array($relationField => $this->model->id));
        }

        return $relatedAdmin;
    }

    /**
     * @param $relation
     *
     * @return BaseActiveRecord
     *
     * @throws CException
     */
    protected function relationClassFromRelation($relation)
    {
        $relationDefinition = $this->getRelationDefnition($relation);
        $relationClass = $relationDefinition[1];
        if (!class_exists($relationClass)) {
            throw new CException('Relation model does not exist');
        }

        return new $relationClass();
    }

    protected function relationFieldFromRelation($relation)
    {
        $relationDefinition = $this->getRelationDefnition($relation);

        return $relationDefinition[2];
    }

    /**
     * @param $template
     * @param array $data
     */
    public function render($template, $data = array())
    {
        $this->controller->render($template, $data);
    }

    /**
     * @param $type
     *
     * @throws Exception
     */
    protected function audit($type, $data = null)
    {
        Audit::add('admin-'.$this->modelName, $type, $data);
    }

    /**
     * @param $relation
     *
     * @return mixed
     *
     * @throws CException
     */
    protected function getRelationDefnition($relation)
    {
        $relations = $this->model->relations();
        if (!array_key_exists($relation, $relations)) {
            throw new CException('Relation does not exist');
        }

        return $relations[$relation];
    }

    /**
     * @return string
     */
    public function generateReturnUrl($requestUri)
    {
        $split = explode('?', $requestUri);
        if (count($split) > 1) {
            $queryArray = array();
            parse_str($split[1], $queryArray);
            unset($queryArray['returnUri']);
            $split[1] = urlencode(http_build_query($queryArray));
        }

        return implode('?', $split);
    }

    /**
     * Respond with JSON for ajax requests
     */
    protected function ajaxResponse()
    {
        $results = $this->search->retrieveResults();
        $jsonArray = array();
        foreach ($results as $result) {
            $resultJson = array();
            foreach ($this->getListFields() as $listItem) {
                $resultJson[$listItem] = $this->attributeValue($result, $listItem);
            }
            $jsonArray[] = $resultJson;
        }

        header('Content-type: application/json');
        echo CJSON::encode($jsonArray);

        foreach (Yii::app()->log->routes as $route) {
            if ($route instanceof CWebLogRoute) {
                $route->enabled = false; // disable any weblogroutes
            }
        }
        Yii::app()->end();
    }

    /**
     * Redirect to somewhere
     */
    public function redirect()
    {
        $return = '/' . $this->controller->uniqueid . '/list';
        if (Yii::app()->request->getPost('returnUriEdit')) {
            $return = urldecode(Yii::app()->request->getPost('returnUriEdit'));
        }
        $this->controller->redirect($return);
    }

    /**
     * @param $date
     * @return string
     */
    protected function dateFormat($date)
    {
        return Helper::convertNHS2MySQL($date);
    }
}
