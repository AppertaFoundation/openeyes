<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class BaseAdminController extends BaseController
{
    public $layout = '//layouts/admin';
    public $items_per_page = 30;

    /**
     * Defines that which sidebar item should be open when the action performed
     * @var string
     */
    public $group = 'Core';

    public function getPageTitle()
    {
        $admin = new AdminSidebar();
        $admin->init();

        if ($admin->getCurrentTitle()) {
            $name = $admin->getCurrentTitle();
        } else {
            $name=ucfirst(basename($this->getId()));

            if ($this->getAction()!==null && strcasecmp($this->getAction()->getId(), $this->defaultAction)) {
                $name=$this->pageTitle=ucfirst($this->getAction()->getId()).' '.$name;
            } else {
                $name=$this->pageTitle=$name;
            }
        }

        if ((string)SettingMetadata::model()->getSetting('use_short_page_titles') != "on") {
            $name=$name . ' - OE';
        }

        return 'Admin - '.$name;
    }


    public function accessRules()
    {
        return array(array('allow', 'roles' => array('admin')));
    }

    protected function beforeAction($action)
    {
        Yii::app()->assetManager->registerScriptFile('js/admin.js', null, 10);
        Yii::app()->assetManager->registerScriptFile('js/handleButtons.js', null, 10);
        $this->jsVars['items_per_page'] = $this->items_per_page;

        return parent::beforeAction($action);
    }

    /**
     * @description Initialise and handle admin pagination
     *
     * @author bizmate
     *
     * @param class  $model
     * @param string $criteria
     *
     * @return CPagination
     */
    protected function initPagination($model, $criteria = null)
    {
        $criteria = is_null($criteria) ? new CDbCriteria() : $criteria;
        $itemsCount = $model->count($criteria);
        $pagination = new CPagination($itemsCount);
        $pagination->pageSize = $this->items_per_page;
        $pagination->applyLimit($criteria);

        return $pagination;
    }

    /**
     * Allows generic CRUD operations on models.
     *
     * @param string $title   The title of the form to be rendered
     * @param string $model   The model for which we are generating a form
     * @param array  $options An array of options that will configure how the form is generated.
     *                        label_field - Will set which field is displayed as a text input for the model
     *                        extra_fields - An array of arrays for which extra fields to render. Each array should contain
     *                        an attribute of the model in assigned to field. Passing a type and model will allow
     *                        either a dropdown or search box for finding related objects eg:
     *                        array(
     *                        'field' => 'site_id',
     *                        'type' => 'lookup',
     *                        'model' => 'Site'
     *                        ),
     *                        filter_fields - Will allow you to filter results, expects an array the same as extra_fields
     * @param int    $key     - if provided will only generate a single row for a null instance of the $model (for ajax additions)
     */
    protected function genericAdmin($title, $model, array $options = array(), $key = null)
    {
        $options = array_merge(array(
            'label_field' => $model::SELECTION_LABEL_FIELD,
            'extra_fields' => array(),
            'filter_fields' => array(),
            'filters_ready' => true,
            'label_extra_field' => false,
            'description' => '',
            'input_class' => '',
            'div_wrapper_class' => 'cols-full',
            'return_url' => false,
            'action_links' => []
        ), $options);

        $columns = $model::model()->metadata->columns;

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

        $items = array();
        $errors = array();
        $options['display_order'] = false;

        if ($key !== null) {
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
        } else {
            if ($options['filters_ready']) {
                if (Yii::app()->request->isPostRequest) {
                    $tx = Yii::app()->db->beginTransaction();
                    $j = 0;

                    foreach ((array) @$_POST['id'] as $i => $id) {
                        if ($id) {
                            $item = $model::model()->findByPk($id);
                            $new = false;
                        }

                        // adding new record with the validation error will cause the id to keep rolling
                        // in that case $id will not be empty but $item will be null, then cause page crash
                        if (!$id || !$item) {
                            $item = new $model();
                            $new = true;
                        }

                        $attributes = $item->getAttributes();
                        if (!empty($_POST[$options['label_field']][$i])) {
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
                                if (isset($_POST['default']) && $_POST['default'] !== 'NONE' && $_POST['default'] == $j) {
                                    $item->default = 1;
                                } else {
                                    $item->default = 0;
                                }
                            }

                            foreach ($options['filter_fields'] as $field) {
                                $item->{$field['field']} = $field['value'];
                            }

                            if ($new || $item->getAttributes() != $attributes) {
                                if (!$item->save()) {
                                    $item_errors = $item->getErrors();
                                    foreach ($item_errors as $error) {
                                        $errors[$i][] = $error[0];
                                    }
                                    $errors[$i] = implode(' ', $errors[$i]);
                                }
                                Audit::add('admin', $new ? 'create' : 'update', $item->primaryKey, null, array(
                                    'module' => (is_object($this->module)) ? $this->module->id : 'core',
                                    'model' => $model::getShortModelName(),
                                ));
                            }

                            $items[] = $item;
                            ++$j;
                        }
                    }

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
                                    $errors[$i]=$e[0];
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

                        if ($options['return_url']) {
                            $this->redirect($options['return_url']);
                        } else {
                            $this->redirect(Yii::app()->request->url);
                        }
                    } else {
                        $tx->rollback();
                    }
                } else {
                    $order = array();

                    if ($model::model()->hasAttribute('display_order')) {
                        $order = array('order' => 'display_order');
                        $options['display_order'] = true;
                    }
                    $crit = new CDbCriteria($order);
                    $this->addFilterCriteria($crit, $options['filter_fields']);
                    $items = $model::model()->findAll($crit);
                }
            }

            $this->render('//admin/generic_admin', array(
                'title' => $title,
                'model' => $model,
                'items' => $items,
                'errors' => $errors,
                'options' => $options,
            ));
        }
    }

    private function addFilterCriteria(CDbCriteria $crit, array $filter_fields)
    {
        foreach ($filter_fields as $filter_field) {
            $crit->compare($filter_field['field'], $filter_field['value']);
        }
    }
}
