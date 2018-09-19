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

/**
 * Class ProceduresController.
 */
class OpcsCodeController extends BaseAdminController
{
    /**
     * @var string
     */
    public $layout = 'admin';

    /**
     * @var int
     */
    public $itemsPerPage = 100;
    public $items_per_page = 100;

    /**
     * Lists procedures.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $criteria = new CDbCriteria();
        $search = \Yii::app()->request->getPost('search', ['query' => '', 'active' => '']);

        if (Yii::app()->request->isPostRequest) {
            if ($search['query']) {
                $criteria->addCondition('name = :query', 'OR');
                $criteria->addCondition('id = :query', 'OR');
                $criteria->params[':query'] = $search['query'];
            }

            if ($search['active'] == 1) {
                $criteria->addCondition('t.active = 1');
            } elseif ($search['active'] != '') {
                $criteria->addCondition('t.active != 1');
            }
        }

        $opcsCode = OPCSCode::model();

        $this->render('/oeadmin/opcsCode/index', [
            'pagination' => $this->initPagination($opcsCode, $criteria),
            'opcsCodes' => $opcsCode->findAll($criteria),
            'search' => $search,
        ]);
    }

    /**
     * Edits or adds a Procedure.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $errors = [];
        $opcsCode_object = OPCSCode::model()->findByPk($id);


        if (!$opcsCode_object) {
            $opcsCode_object = new OPCSCode();
            if ($id) {
                $opcsCode_object->id = $id;
            }
        }

        if (Yii::app()->request->isPostRequest) {
            // get data from POST
            $user_data = \Yii::app()->request->getPost('OPCSCode');

            $opcsCode_object->name = $user_data['name'];
            $opcsCode_object->description = $user_data['description'];
            $opcsCode_object->active = $user_data['active'];

            // try saving the data
            if (!$opcsCode_object->save()) {
                $errors = $opcsCode_object->getErrors();
            } else {
                $this->redirect('/oeadmin/opcsCode/list/');
            }
        }

        $this->render('/oeadmin/opcsCode/edit', array(
            'opcsCode' => $opcsCode_object,
            'errors' => $errors
        ));
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $admin = new Admin(OPCSCode::model(), $this);
        $admin->deleteModel();
    }
}
