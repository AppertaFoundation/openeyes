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

class ConsentLayoutsController extends BaseAdminController
{
    public $group = 'Consent';

    /**
     * Render the view for controller
     */
    public function actionList()
    {
        $this->render('/oeadmin/consent_layouts/index', []);
    }

    public function actionGetLayoutElements($type_id)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('type_id = :type_id');
        $criteria->params[':type_id'] = $type_id;
        $criteria->order = 'display_order asc';

        $elements = OphTrConsent_Type_Assessment::model()->findAll($criteria);

        $rows = '';
        if ($elements) {
            foreach ($elements as $key => $element) {
                $rows .= $this->renderPartial(
                    '/oeadmin/consent_layouts/_row',
                    [
                        'key' => $key,
                        'data' => $element
                    ],
                    true,
                    true
                );
            }
        }

         $this->renderJSON([
            'success' => 1,
            'rows' => $rows
         ]);
    }

    public function actionAddLayoutElements()
    {
        if (\Yii::app()->request->isPostRequest) {
            $type_assessment = new OphTrConsent_Type_Assessment();

            $criteria = new CDbCriteria();
            $criteria->select = 'MAX(display_order) AS display_order';
            $criteria->condition = 'type_id = :type_id';
            $criteria->params = array(':type_id' => \Yii::app()->request->getPost('type_id'));
            $order = $type_assessment->model()->find($criteria);

            $type_assessment->element_id = \Yii::app()->request->getPost('element_id');
            $type_assessment->type_id = \Yii::app()->request->getPost('type_id');
            $type_assessment->display_order = (int)$order['display_order'] + 1;

            if (!$type_assessment->save()) {
                $error_messages = '';
                foreach ($type_assessment->getErrors() as $attr => $errors) {
                    $error_messages = implode(' ', $errors);
                }
                $this->renderJSON([
                    'success' => 0,
                    'message' => $error_messages
                ]);
                exit;
            }

            $this->renderJSON([
                'success' => 1
            ]);
        }
    }

    public function actionDeleteLayoutElements()
    {
        if (\Yii::app()->request->isPostRequest) {
            $id = \Yii::app()->request->getPost('row_id');
            $type_id = \Yii::app()->request->getPost('type_id');
            $assessment = OphTrConsent_Type_Assessment::model()
                ->findByAttributes([
                    'id' => $id,
                    'type_id' => $type_id
                ]);
            if ($assessment) {
                $assessment->delete();
            }

            $this->renderJSON([
                'success' => 1
            ]);
        }
    }

    public function actionSortAssessments()
    {
        if (\Yii::app()->request->isPostRequest) {
            $order = \Yii::app()->request->getPost('order');
            foreach ($order as $i => $id) {
                if ($assessment = OphTrConsent_Type_Assessment::model()->findByPk($id)) {
                    $assessment->display_order = $i + 1;
                    if (!$assessment->update()) {
                        throw new Exception('Unable to save order: '.print_r($assessment->getErrors(), true));
                    }
                }
            }
        }
    }
}
