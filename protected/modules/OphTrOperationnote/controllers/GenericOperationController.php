<?php

/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class GenericOperationController extends BaseAdminController
{
    public $itemsPerPage = 100;
    public $group = 'Operation Note';

    /**
     * @throws CHttpException
     */
    public function actionList()
    {
        $admin = new Admin(OphTrOperationNote_Generic_Procedure_Data::model(), $this);

        $admin->setListFields(array(
            'id',
            'proc_id',
            'procedure.term',
            'default_text',
        ));

        $admin->setModelDisplayName('Generic Procedure Default Text');
        $admin->div_wrapper_class = 'cols-5';
        $admin->searchAll();
        $admin->getSearch()->addActiveFilter();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }

    public function actionEdit($id = false)
    {
        $admin = new Admin(OphTrOperationNote_Generic_Procedure_Data::model(), $this);
        $admin->setModelDisplayName('Generic Procedure Default Text');
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setEditFields(array(
            'proc_id' => 'text',
            'default_text' => 'text',
        ));
        $admin->div_wrapper_class = 'cols-5';
        $admin->editModel();
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $admin = new Admin(OphTrOperationNote_Generic_Procedure_Data::model(), $this);
        $admin->deleteModel();
    }
}