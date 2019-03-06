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
class OperativeDeviceController extends BaseAdminController
{
    /**
     * @var int
     */
    public $itemsPerPage = 100;

    public $group = 'Operation note';

    /**
     * Lists operative devices.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $admin = new Admin(OperativeDevice::model(), $this);
        $admin->setModelDisplayName('Operative Devices');
        $admin->setListFields(array(
                            'id',
                            'name',
                            'active',
        ));
        $admin->searchAll();
        $admin->getSearch()->addActiveFilter();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->div_wrapper_class = 'cols-5';
        $admin->listModel();
    }

    /**
     * Edits or adds an operative device.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $admin = new Admin(OperativeDevice::model(), $this);
        $admin->setModelDisplayName('Operative Device');
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setEditFields(array(
            'name' => 'text',
            'active' => 'checkbox',
        ));
        $admin->div_wrapper_class = 'cols-5';
        $admin->editModel();
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $admin = new Admin(OperativeDevice::model(), $this);
        $admin->deleteModel();
    }
}
