<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class TemplateController extends BaseAdminController
{
    public $items_per_page = 60;
    public $group = 'Templates';

    public function actionList()
    {
        Audit::add(
            'admin',
            'list',
            null,
            false,
            array('module' => 'OphTrConsent',
            'model' => 'Template')
        );
        $query = \Yii::app()->request->getQuery('searchQuery');
        $subspecialty = \Yii::app()->request->getQuery('subspecialty');
        $institution = \Yii::app()->request->getQuery('institution');
        $site = \Yii::app()->request->getQuery('site');
        $criteria = new \CDbCriteria();
        $criteria->order = 'name';
        if ($query) {
            if (is_numeric($query)) {
                $criteria->addCondition('id = :id');
                $criteria->params[':id'] = $query;
            } else {
                $criteria->addSearchCondition('lower(name)', strtolower($query), true, 'OR');
            }
        }

        if ($institution) {
            if ($institution == "None") {
                $criteria->addCondition('institution_id IS NULL');
            } else {
                $criteria->compare('institution_id', $institution);
            }
        }

        if ($site) {
            if ($site == "None") {
                $criteria->addCondition('site_id IS NULL');
            } else {
                $criteria->compare('site_id', $site);
            }
        }

        if ($subspecialty) {
            if ($subspecialty == "None") {
                $criteria->addCondition('subspecialty_id IS NULL');
            } else {
                $criteria->compare('subspecialty_id', $subspecialty);
            }
        }

        $this->render('/oeadmin/templates/list_template', array(
            'pagination' => $this->initPagination(OphTrConsent_Template::model(), $criteria),
            'model_list' => OphTrConsent_Template::model()->findAll($criteria),
            'title' => 'Manage Template',
            'model_class' => 'Template',
            'query' => $query
        ));
    }

    public function actionEdit()
    {
        $request = Yii::app()->getRequest();
        $model = OphTrConsent_Template::model()->findByPk((int)$request->getParam('id'));
        if (!$model) {
            throw new Exception('Template not found with id ' . $request->getParam('id'));
        }
        if ($request->getPost('OphTrConsent_Template')) {
            $model->attributes = $request->getPost('OphTrConsent_Template');
            $templateAtt = $request->getPost('OphTrConsent_Template');
            if (!$model->validate()) {
                $errors = $model->getErrors();
            } else {
                if ($model->save()) {
                    Yii::app()->user->setFlash('success', 'Template saved');
                    if (!array_key_exists('firms', $templateAtt) || !is_array($templateAtt['firms'])) {
                        $templateAtt['firms'] = array();
                    }
                    $model->saveProcedures($templateAtt['procedures']);
                    $this->redirect(array('List'));
                } else {
                    $errors = $model->getErrors();
                }
            }
        }

        $this->render('/oeadmin/templates/edit', array(
            'model' => $model,
            'title' => 'Edit Template',
            'errors' => isset($errors) ? $errors : null,
            'cancel_uri' => '/OphTrConsent/oeadmin/Template/list',
        ));
    }

    public function actionAdd()
    {
        $model = new OphTrConsent_Template();
        $request = Yii::app()->getRequest();
        if ($request->getPost('OphTrConsent_Template')) {
            $model->attributes = $request->getPost('OphTrConsent_Template');
            $templateAtt = $request->getPost('OphTrConsent_Template');

            if ($model->save()) {
                Audit::add('admin', 'create', serialize($model->attributes), false, array('model' => 'Template'));
                Yii::app()->user->setFlash('success', 'Template created');
                if (!array_key_exists('firms', $templateAtt) || !is_array($templateAtt['firms'])) {
                    $templateAtt['firms'] = array();
                }
                $model->saveProcedures($templateAtt['procedures']);
                $this->redirect(array('List'));
            } else {
                $errors = $model->getErrors();
            }
        }
        $this->render('/oeadmin/templates/edit', array(
            'model' => $model,
            'title' => 'Add Template',
            'cancel_uri' => '/OphTrConsent/oeadmin/Template/list',
            'errors' => isset($errors) ? $errors : null,
        ));
    }

    public function actionDelete()
    {
        $result = [];
        $result['status'] = 1;
        $result['errors'] = "";

        if (!empty($_POST['templates'])) {
            foreach (OphTrConsent_Template::model()->findAllByPk($_POST['templates']) as $consent_template) {
                $templateProcedures = \OphTrConsent_TemplateProcedure::model()->findAll('template_id = :template_id', array(':template_id' => $consent_template->id));
                foreach ($templateProcedures as $templateProcedure) {
                    try {
                        if (!$templateProcedure->delete()) {
                            $result['status'] = 0;
                            $result['errors'][] = $templateProcedure->getErrors();
                        } else {
                            Audit::add('admin-templateprocedure', 'delete', $templateProcedure);
                        }
                    } catch (Exception $e) {
                        $result['status'] = 0;
                        $result['errors'][] = "TemplateProcedure: " . $templateProcedure->name . " is in use";
                    }
                }
                try {
                    if (!$consent_template->delete()) {
                        $result['status'] = 0;
                        $result['errors'][] = $consent_template->getErrors();
                    } else {
                        Audit::add('admin-template', 'delete', $consent_template);
                    }
                } catch (Exception $e) {
                    $result['status'] = 0;
                    $result['errors'][] = "Template: " . $consent_template->name . " is in use";
                }
            }
        }

        $this->renderJSON($result);
    }
}
