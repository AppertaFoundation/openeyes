<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class RefSetAdminController extends BaseAdminController
{
    public function actionList()
    {
        $admin = new Admin(RefSet::model(), $this);
        $admin->setListFields(array(
            'id',
            'name'
        ));

        $admin->getSearch()->addSearchItem('name');
        $admin->getSearch()->setItemsPerPage(30);
        $crit = new CDbCriteria();
        $crit->order = 'id ASC';
        $admin->getSearch()->setCriteria($crit);

        $admin->setModelDisplayName("Medication sets");

        $admin->listModel();
    }

    public function actionEdit($id)
    {
        $admin = new Admin(RefSet::model(), $this);

        $admin->setEditFields(array(
            'name'=>'Name',
            'rules' =>  array(
                'widget' => 'GenericAdmin',
                'options' => array(
                    'model' => RefSetRule::class,
                    //'relation_field_id' => 'id',
                    'extra_fields' =>  array(
                        array(
                            'field' => 'site_id',
                            'type' => 'lookup',
                            'model' => Site::class,
                            'allow_null' => true
                        ),
                        array(
                            'field' => 'subspecialty_id',
                            'type' => 'lookup',
                            'model' => Subspecialty::class,
                            'allow_null' => true
                        )
                    ),
                    //'label_field' => 'id',
                    'label_extra_field' => true,
                    'items' => RefSet::model()->findByPk($id)->refSetRules,
                    'filters_ready' => true,
                    'cannot_save' => true,
                    'no_form' => true,
                ),
                'label' => 'Rules'
            ),

        ));
        $admin->setModelDisplayName("Medication set");
        $admin->setModelId($id);

        $admin->editModel();
    }
}