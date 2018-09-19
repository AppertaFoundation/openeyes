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
class ComplicationController extends BaseAdminController
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

        $complication = Complication::model();

        $this->render('/oeadmin/complication/index', [
            'pagination' => $this->initPagination($complication, $criteria),
            'complications' => $complication->findAll($criteria),
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
        $complication_object = Complication::model()->findByPk($id);

        if (!$complication_object) {
            $complication_object = new Complication();
            if ($id) {
                $complication_object->id = $id;
            }
        }

        if (Yii::app()->request->isPostRequest) {
            // get data from POST
            $user_data = \Yii::app()->request->getPost('Complication');

            $complication_object->name = $user_data['name'];
            $complication_object->active = $user_data['active'];

            // try saving the data
            if (!$complication_object->save()) {
                $errors = $complication_object->getErrors();
            } else {
                $this->redirect('/oeadmin/complication/list/');
            }
        }

        $this->render('/oeadmin/complication/edit', array(
            'complication' => $complication_object,
            'errors' => $errors
        ));
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $admin = new Admin(Complication::model(), $this);
        $admin->deleteModel();
    }
}
