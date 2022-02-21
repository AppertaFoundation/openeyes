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
class PatientRelationshipController extends BaseAdminController
{
    public $group = 'Consent';

    /**
     * @throws Exception
     */
    public function actionList()
    {
        $relationship_model = OphTrConsent_PatientRelationship::model();

        if (Yii::app()->request->isPostRequest) {
            $relationships = Yii::app()->request->getParam('OphTrConsent_PatientRelationship');
            foreach ($relationships as $key => $relationship) {
                if ( strlen($relationship['name'])===0) {
                    continue;
                }

                $relationship_object = $relationship_model->findByPk($relationship['id']);
                if (!$relationship_object) {
                    $relationship_object = new OphTrConsent_PatientRelationship();
                }

                $relationship_object->name = $relationship['name'];
                $relationship_object->display_order = $relationship['display_order'];
                $relationship_object->active = $relationship['active'];

                if (!$relationship_object->save()) {
                    throw new Exception('Unable to save relationships: ' . print_r($relationship_object->getErrors(), true));
                }
            }
        }

        $this->render('/oeadmin/relationships/index', [
            'relationships' => $relationship_model->findAll()
        ]);
    }
}
