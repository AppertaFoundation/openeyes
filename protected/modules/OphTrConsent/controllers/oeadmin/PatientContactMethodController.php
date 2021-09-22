<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class PatientContactMethodController extends BaseAdminController
{
    public $group = 'Consent';

    /**
     * @throws Exception
     */
    public function actionList()
    {
        $contact_method_model = OphTrConsent_PatientContactMethod::model();

        if (Yii::app()->request->isPostRequest) {
            $contact_methods = Yii::app()->request->getParam('OphTrConsent_PatientContactMethod');
            foreach ($contact_methods as $key => $contact_method) {
                if ( strlen($contact_method['name'])===0) {
                    continue;
                }

                $contact_method_object = $contact_method_model->findByPk($contact_method['id']);
                if (!$contact_method_object) {
                    $contact_method_object = new OphTrConsent_PatientContactMethod();
                }

                $contact_method_object->name = $contact_method['name'];
                $contact_method_object->display_order = $contact_method['display_order'];
                $contact_method_object->active = $contact_method['active'];
                $contact_method_object->need_signature = $contact_method['need_signature'];

                if (!$contact_method_object->save()) {
                    throw new Exception('Unable to save contact_methods: ' . print_r($contact_method_object->getErrors(), true));
                }
            }
        }

        $this->render('/oeadmin/contact_methods/index', [
            'contact_methods' => $contact_method_model->findAll()
        ]);
    }
}
