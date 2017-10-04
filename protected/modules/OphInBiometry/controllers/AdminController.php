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
class AdminController extends ModuleAdminController
{
    public function actionLensTypes($id = false)
    {
        $this->render('lens_types', array(
            'lens_types' => OphInBiometry_LensType_Lens::model()->active()->findAll(array('order' => 'display_order asc')),
        ));
    }

    public function actionEditLensType($id)
    {
        if (!$lens_type = OphInBiometry_LensType_Lens::model()->findByPk($id)) {
            throw new Exception("Lens type not found: $id");
        }

        if (!empty($_POST)) {
            $lens_type->attributes = $_POST['OphInBiometry_LensType_Lens'];

            if (!$lens_type->validate()) {
                $errors = $lens_type->getErrors();
            } else {
                if (!$lens_type->save()) {
                    throw new Exception('Unable to save lens type: '.print_r($lens_type->getErrors(), true));
                }
                $this->redirect('/OphInBiometry/admin/lensTypes');
            }
        }

        $this->render('edit_lens_type', array(
            'lens_type' => $lens_type,
            'errors' => @$errors,
        ));
    }

    public function actionAddLensType()
    {
        $lens_type = new OphInBiometry_LensType_Lens();

        if (!empty($_POST)) {
            $lens_type->attributes = $_POST['OphInBiometry_LensType_Lens'];

            if (!$lens_type->validate()) {
                $errors = $lens_type->getErrors();
            } else {
                if (!$lens_type->save()) {
                    throw new Exception('Unable to save lens type: '.print_r($lens_type->getErrors(), true));
                }
                $this->redirect('/OphInBiometry/admin/lensTypes');
            }
        }

        $this->render('edit_lens_type', array(
            'lens_type' => $lens_type,
            'errors' => @$errors,
        ));
    }

    public function actionDeleteLensTypes()
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', @$_POST['lens_type_id']);

        OphInBiometry_LensType_Lens::model()->deleteAll($criteria);
    }

    public function actionFileLog()
    {
        return $this->redirect('../../DicomLogViewer/log');
    }
}
