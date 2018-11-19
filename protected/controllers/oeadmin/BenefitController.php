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
 * Class BenefitController.
 */
class BenefitController extends BaseAdminController
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

    public $group = 'Procedure Management';

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

        $benefit = Benefit::model();

        $this->render('/oeadmin/benefit/index', [
            'pagination' => $this->initPagination($benefit, $criteria),
            'benefits' => $benefit->findAll($criteria),
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
        $benefit_object = Benefit::model()->findByPk($id);


        if (!$benefit_object) {
            $benefit_object = new Benefit();
            if ($id) {
                $benefit_object->id = $id;
            }
        }

        if (Yii::app()->request->isPostRequest) {
            // get data from POST
            $user_data = \Yii::app()->request->getPost('Benefit');

            $benefit_object->name = $user_data['name'];
            $benefit_object->active = $user_data['active'];

            // try saving the data
            if (!$benefit_object->save()) {
                $errors = $benefit_object->getErrors();
            } else {
                $this->redirect('/oeadmin/benefit/list/');
            }
        }

        $this->render('/oeadmin/benefit/edit', array(
            'benefit' => $benefit_object,
            'errors' => $errors
        ));
    }

    /**
     * @param Benefit $benefit - benefit to look for dependencies
     * @return bool|int - true if there are no tables depending on the given benefit
     */
    protected function isBenefitDeletable(Benefit $benefit)
    {
        $check_dependencies = 1;

        $options = [':id' => $benefit->id];
        $check_dependencies &= !ProcedureBenefit::model()->count('benefit_id = :id', $options);

        return $check_dependencies;
    }


    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $benefits = \Yii::app()->request->getPost('select', []);

        foreach ($benefits as $benefit_id) {
            $benefit = Benefit::model()->findByPk($benefit_id);

            if ($this->isBenefitDeletable($benefit)) {
                if (!$benefit->delete()) {
                    echo 'Could not delete benefit with id: ' . $benefit_id . '.\n';
                }
            } else {
                echo 'Benefit with id ' . $benefit_id .' cannot be deleted. Other tables depend on it.\n';
            }
        }
        echo 1;
    }
}
