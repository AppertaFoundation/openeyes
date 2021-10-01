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
 * Class RiskController.
 */
class WhiteboardSettingsController extends ModuleAdminController
{
    public $group = 'Operation booking';

    public function actionSettings()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('institution_id = :institution_id');
        $criteria->params[':institution_id'] = Institution::model()->getCurrent()->id;
        $this->render('/admin/whiteboard/settings', array(
            'settings' => OphTrOperationbooking_Whiteboard_Settings::model()->findAll($criteria)
        ));
    }

    public function actionEditSetting()
    {

        if (!$metadata = OphTrOperationbooking_Whiteboard_Settings::model()->find('`key`=?', array(@$_GET['key']))) {
            $this->redirect(array('/OphTrOperationbooking/oeadmin/WhiteboardSettings/settings'));
        }

        $errors = array();
        $institution_id = Institution::model()->getCurrent()->id;
        if (Yii::app()->request->isPostRequest) {
            foreach (OphTrOperationbooking_Whiteboard_Settings::model()->findAll() as $metadata) {
                if (@$_POST['hidden_' . $metadata->key] || @$_POST[$metadata->key]) {
                    if (!$setting = $metadata->getSetting($metadata->key, null, true)) {
                        $setting = new OphTrOperationbooking_Whiteboard_Settings_Data();
                        $setting->key = $metadata->key;
                        $setting->institution_id = $this->selectedInstitutionId;
                    }
                    $setting->value = @$_POST[$metadata->key];
                    if (!$setting->save()) {
                        $errors = $setting->errors;
                    } else {
                        $this->redirect(array('/OphTrOperationbooking/oeadmin/WhiteboardSettings/settings'));
                    }
                }
            }
        }

        $this->render(
            '/admin/whiteboard/edit_setting',
            array(
                'metadata' => $metadata,
                'errors' => $errors,
                'institution_id' => $institution_id,
            )
        );
    }
}
