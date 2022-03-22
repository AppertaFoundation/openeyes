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
class SnippetController extends ModuleAdminController
{
    protected $admin;

    /**
     * @var int
     */
    public $itemsPerPage = 100;

    protected function beforeAction($action)
    {
        $this->admin = new Admin(LetterString::model(), $this);
        $this->admin->setModelDisplayName('Letter String');
        $this->admin->div_wrapper_class = 'cols-full';

        return parent::beforeAction($action);
    }

    /**
     * Lists snippets.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $search = \Yii::app()->request->getPost('search');

        if (isset($search['institution_relations.institution_id'])) {
            $institutions_id = $search['institution_relations.institution_id'];
        }

        if (isset($search['sites.id'])) {
            $sites_id = $search['sites.id'];
        }


        $this->admin->setListFields(array(
            'display_order',
            'id',
            'sites.name',
            'name',
            'body',
            'elementTypeName',
            'eventTypeName',
        ));

        /**
         * @var CDbCriteria $criteria
         */
        $criteria = $this->admin->getSearch()->getCriteria();
        if (!isset($search['institution_relations.institution_id'])) {
            $criteria->join = "JOIN ophcocorrespondence_letter_string_institution institution_relations ON institution_relations.letter_string_id = t.id";
        }

        /**
         * @var CDbCriteria $criteria
         */
        $institution_criteria = new CDbCriteria;
        $institution_criteria->join = 'JOIN institution_authentication ia ON ia.institution_id = t.id
                                       JOIN user_authentication ua ON ua.institution_authentication_id = ia.id
                                       JOIN user u ON u.id = ua.user_id';
        $institution_criteria->compare('ua.user_id', Yii::app()->user->id);
        $is_admin = Yii::app()->user->checkAccess('admin');

        if($is_admin) {
            $this->admin->getSearch()->addSearchItem('institution_relations.institution_id', array(
                'type' => 'dropdown',
                'options' => CHtml::listData(Institution::model()->findAll($institution_criteria), 'id', 'name'),
                'default' => \Yii::app()->session['selected_institution_id']
            ));
        } else {
            $this->admin->getSearch()->addSearchItem('institution_relations.institution_id', array(
                'type' => 'dropdown',
                'options' => CHtml::listData(array(), 'id', 'name'),
                'default' => \Yii::app()->session['selected_institution_id']
            ));
        }

        if (isset($institutions_id) && strcmp("", $institutions_id) !== 0) {
            $this->admin->getSearch()->addSearchItem('sites.id', array(
                'type' => 'dropdown',
                'empty' => 'All sites',
                'options' => CHtml::listData(Site::model()->findAllByAttributes(['institution_id' => $institutions_id]), 'id', 'name')
            ));
        } else {
            if($is_admin) {
                $this->admin->getSearch()->addSearchItem('sites.id', array(
                    'type' => 'dropdown',
                    'empty' => 'All sites',
                    'options' => CHtml::listData(Site::model()->findAllByAttributes(['institution_id' => \Yii::app()->session['selected_institution_id']]), 'id', 'name')
                ));
            } else {
                $this->admin->getSearch()->addSearchItem('sites.id', array(
                    'type' => 'dropdown',
                    'empty' => 'All sites',
                    'options' => CHtml::listData(array(), 'id', 'name')
                ));
            }
        }

        $this->admin->getSearch()->addSearchItem('name');

        if (isset($institutions_id) && strcmp("", $institutions_id) !== 0 &&
            isset($sites_id) && strcmp("", $sites_id) !== 0) {
            $criteria = new CDbCriteria;
            $criteria->join = 'JOIN ophcocorrespondence_letter_string_institution institutions ON institutions.letter_string_id = t.id';
            $criteria->join = 'JOIN ophcocorrespondence_letter_string_site sites ON sites.letter_string_id = t.id';
            $criteria->compare('institution_relations.institution_id', null);
            $criteria->compare('sites.site_id', $sites_id);
            if(isset($search['name'])) {
                $criteria->compare('name', $search['name']);
            }
            $criteria->mergeWith($criteria, 'OR');
            $this->admin->getSearch()->setCriteria($criteria);

            $this->admin->listModel();
        } elseif (!$is_admin) {
            $criteria = new CDbCriteria;
            $criteria->compare('id', 0);
            $this->admin->getSearch()->setCriteria($criteria);

            $this->admin->listModel(false);
        } else {
            $this->admin->listModel();
        }
    }

    /**
     * Edits or adds a snippets.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        if ($id) {
            $this->admin->setModelId($id);
        }

        $group_id = Yii::app()->request->getParam('group_id');
        if(!empty(Yii::app()->request->getParam('default'))) {
            $list_institution_id = Yii::app()->request->getParam('default')['institution_relations.institution_id'];
        }

        if (!$id && $group_id) {
            $this->admin->getModel()->letter_string_group_id = $group_id;
        }

        $model = $this->admin->getModel();
        $institutions = $model->institutions;
        $sites = $model->sites;

        if (Yii::app()->request->isPostRequest) {
            $post = \Yii::app()->request->getPost($this->admin->getModelName());
            if (!array_key_exists('institutions', $post) && !is_array($post['institutions'])) {
                if (isset($institutions)) {
                    $selected_institution = array_values($institutions)[0]->id;
                } elseif (isset($sites) && !empty($sites)) {
                    $selected_institution = array_values($sites)[0]->institution_id;
                } else {
                    $selected_institution = $list_institution_id;
                }
            } else {
                $selected_institution = $post['institutions'];
            }
        } else {
            if (isset($institutions) && !empty($institutions)) {
                $selected_institution = array_values($institutions)[0]->id;
            } elseif (isset($sites) && !empty($sites)) {
                $selected_institution = array_values($sites)[0]->institution_id;
            } else {
                $selected_institution = $list_institution_id;
            }
        }

        $is_admin = Yii::app()->user->checkAccess('admin');
        $this->admin->setEditFields(array(
            'institutions' => array(
                'widget' => 'DropDownList',
                'relation_field_id' => 'id',
                'options' => \Institution::model()->getList(!$is_admin),
                'htmlOptions' => [
                    'label' => 'Institution',
                    'empty' => '-- Add --',
                    'searchable' => false,
                    'class' => 'cols-8',
                    'options' => array($selected_institution => array('selected' => true)),
                    'disabled' => 'disabled'
                ],
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'sites' => array(
                'widget' => 'MultiSelectList',
                'relation_field_id' => 'id',
                'options' => CHtml::listData(Institution::model()->findByPk($selected_institution)->sites, 'id', 'name'),
                'htmlOptions' => [
                    'label' => 'Sites',
                    'empty' => 'All sites',
                    'searchable' => false,
                    'class' => 'cols-8',
                ],
            ),
            'letter_string_group_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(LetterStringGroup::model()->findAll([
                    'condition' => 'institution_id = :institution_id',
                    'params' => [':institution_id' => $selected_institution]
                ]), 'id', 'name'),
                'htmlOptions' => ['class' => 'cols-8'],
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'name' => 'text',
            'body' => array(
                'widget' => 'CustomView',
                'viewName' => '//admin/generic/shortcodeText',
                'viewArguments' => array('model' => $this->admin->getModel()),
            ),
            'element_type' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(ElementType::model()->findAll(), 'class_name', 'name'),
                'htmlOptions' => array('empty' => '- Select -'),
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'event_type' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(EventType::model()->findAll(), 'class_name', 'name'),
                'htmlOptions' => array('empty' => '- Select -'),
                'hidden' => false,
                'layoutColumns' => null,
            ),
        ));
        $saved = $this->admin->editModel(false);

        if (Yii::app()->request->isPostRequest) {
            if ($saved) {
                $post = \Yii::app()->request->getPost($this->admin->getModelName());
                $model = $this->admin->getModel();
                LetterString_Institution::model()->deleteAll('letter_string_id = :ls_id', [':ls_id' => $model->id]);
                LetterString_Site::model()->deleteAll('letter_string_id = :ls_id', [':ls_id' => $model->id]);
                if (array_key_exists('sites', $post) && is_array($post['sites'])) {
                    $model->createMappings(ReferenceData::LEVEL_SITE, $post['sites']);
                } elseif (array_key_exists('institutions', $post) && strcmp("", $post['institutions']) !== 0) {
                    $model->createMappings(ReferenceData::LEVEL_INSTITUTION, array($post['institutions']));
                }
                $this->redirect(['list']);
            } else {
                $this->admin->render($this->admin->getEditTemplate(), array('admin' => $this->admin, 'errors' => $this->admin->getModel()->getErrors()));
            }
        }
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $post = Yii::app()->request->getPost($this->admin->getModelName());
        foreach ($post['id'] as $id) {
            LetterString_Institution::model()->deleteAll('letter_string_id = :ls_id', [':ls_id' => $id]);
            LetterString_Site::model()->deleteAll('letter_string_id = :ls_id', [':ls_id' => $id]);
        }
        $this->admin->deleteModel();
    }

    /**
     * Save ordering of the objects.
     */
    public function actionSort()
    {
        $this->admin->sortModel();
    }
}
