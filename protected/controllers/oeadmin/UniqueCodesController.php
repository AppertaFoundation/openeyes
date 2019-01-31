<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class UniqueCodesController.
 */
class UniqueCodesController extends BaseAdminController
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

    public $group = 'Core';

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
                if (is_numeric($search['query'])) {
                    $criteria->addCondition('id = :query');
                } else {
                    $criteria->addCondition('code = :query');
                }
                $criteria->params[':query'] = $search['query'];
            }

            if ($search['active'] == 1) {
                $criteria->addCondition('active = 1');
            } elseif ($search['active'] != '') {
                $criteria->addCondition('active != 1');
            }
        }
        // $criteria->order = 'id DESC';

        $this->render('/oeadmin/unique_codes/index', [
            'pagination' => $this->initPagination(UniqueCodes::model(), $criteria),
            'unique_codes' => UniqueCodes::model()->findAll($criteria),
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
        $unique_code_object = UniqueCodes::model()->findByPk($id);

        if (!$unique_code_object) {
            $this->redirect('/oeadmin/uniqueCodes/list/');
        }

        if (Yii::app()->request->isPostRequest) {
            // get data from POST
            $user_data = \Yii::app()->request->getPost('UniqueCodes');

            $unique_code_object->code = $user_data['code'];
            $unique_code_object->active = $user_data['active'];

            // try saving the data
            if (!$unique_code_object->save()) {
                $errors = $unique_code_object->getErrors();
            } else {
                $this->redirect('/oeadmin/uniqueCodes/list/');
            }
        }

        $this->render('/oeadmin/unique_codes/edit', array(
            'unique_code' => $unique_code_object,
            'errors' => $errors
        ));
    }


    /**
     * Deletes rows from the model.
     */
    public function actionDelete()
    {
        $unique_codes = \Yii::app()->request->getPost('select', []);

        foreach ($unique_codes as $unique_code_id) {
            $unique_code = UniqueCodes::model()->findByPk($unique_code_id);

            if (!$unique_code->delete()) {
                echo 'Could not delete unique code with id: ' . $unique_code_id;
            }
        }
        echo 1;
    }
}
