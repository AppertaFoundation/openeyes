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
class AddressController extends BaseAdminController
{
    public function actionEdit()
    {
        $request = Yii::app()->getRequest();
        $model = Address::model()->findByPk((int)$request->getParam('id'));
        $contact_id = $request->getParam('contact_id');
        if (!$model) {
            throw new Exception('Address not found with id ' . $request->getParam('id'));
        }
        if ($request->getPost('Address')) {
            $model->attributes = $request->getPost('Address');
            if ($request->getPost('Contact')) {
                $model->contact->attributes = $request->getPost('Contact');
                $model->contact->setScenario('admin_contact');
                if (!$model->contact->save()) {
                    $errors = $model->contact->getErrors();
                }
            }
            if ($model->save()) {
                Yii::app()->user->setFlash('success', 'Address saved');
                $this->redirect(array('/admin/editContact?contact_id=' . $contact_id));
            } else {
                $errors = $model->getErrors();
            }
        }
        $this->render('/edit', array(
            'model' => $model,
            'title' => 'Edit Address',
            'errors' => isset($errors) ? $errors : null,
            'cancel_uri' => '/admin/editContact?contact_id=' . $request->getParam('contact_id'),
        ));
    }

    public function actionAdd()
    {
        $model = new Address();
        $model->date_start = null;
        $model->date_end = null;
        $request = Yii::app()->getRequest();
        $model->contact_id = $request->getParam('contact_id');
        if ($request->getPost('Address')) {
            $model->attributes = $request->getPost('Address');
            if ($request->getPost('Contact')) {
                $model->contact->attributes = $request->getPost('Contact');
                $model->contact->setScenario('admin_contact');
                if (!$model->contact->save()) {
                    $errors = $model->contact->getErrors();
                }
            }
            if ($model->save()) {
                Audit::add('admin', 'create', serialize($model->attributes), false, array('model' => 'Address'));
                Yii::app()->user->setFlash('success', 'Address created');
                $this->redirect(array('/admin/editContact?contact_id=' . $model->contact_id));
            } else {
                $errors = $model->getErrors();
            }
        }
        $this->render('/edit', array(
            'model' => $model,
            'title' => 'Add Address',
            'cancel_uri' => '/admin/editContact?contact_id=' . $request->getParam('contact_id'),
            'errors' => isset($errors) ? $errors : null,
        ));
    }

    public function actionDelete()
    {
        $location_id = isset($_POST['address_id']) ? $_POST['address_id'] : null;
        $address = Address::model()->findByPk($location_id);
        if (!$address) {
            throw new Exception('Address not found: ' . $location_id);
        }
        if (!$address->delete()) {
            echo '-1';
            return;
        }
        Audit::add('admin-Address', 'delete', $location_id);
        return '1';
    }
}
