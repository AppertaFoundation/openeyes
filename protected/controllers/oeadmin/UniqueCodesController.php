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

        if (true) {
            $this->render('/oeadmin/unique_codes/index', [
                'pagination' => $this->initPagination(UniqueCodes::model(), $criteria),
                'unique_codes' => UniqueCodes::model()->findAll($criteria),
                'search' => $search,
            ]);
        } else {
            $admin = new Admin(UniqueCodes::model(), $this);

            $admin->setModelDisplayName('Unique Codes');

            $admin->setListFields(array(
                'id',
                'code',
                'active',
            ));

            $admin->searchAll();
            $admin->getSearch()->addActiveFilter();
            $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
            $admin->listModel(false);
        }
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
        $admin = new Admin(UniqueCodes::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setModelDisplayName('Unique Codes');
        $admin->setEditFields(array(
            'code' => 'label',
            'active' => 'checkbox',
        ));
        $admin->editModel();

//        $this->render('/oeadmin/unique_codes/edit', array(
//            'unique_code' => UniqueCodes::model()->findByPk($id),
//            'id' => $id,
//        ));
    }
}
