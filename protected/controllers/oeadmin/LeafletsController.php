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
class LeafletsController extends BaseAdminController
{
    /**
     * @throws Exception
     */
    public function actionList()
    {
        if (0) {
            $this->genericAdmin('Leaflets', 'OphTrConsent_Leaflet');
        } else {
            if (Yii::app()->request->isPostRequest) {
                $leaflets = Yii::app()->request->getParam('OphTrConsent_Leaflet');

                foreach ($leaflets as $key => $leaflet) {
                    if (isset($leaflet['id'])) {
                        $leaflet_object = OphTrConsent_Leaflet::model()->findByPk($leaflet['id']);
                    } else {
                        $leaflet_object = new OphTrConsent_Leaflet();
                    }

                    //TODO: Fix ERROR: "Creating default object from empty value"
                    $leaflet_object->name = $leaflet['name'];

                    $leaflet_object->display_order = $leaflet['display_order'];
                    $leaflet_object->active = $leaflet['active'];

                    if (!$leaflet_object->save()) {
                        throw new Exception('Unable to save Leaflets: ' . print_r($leaflet_object->getErrors(), true));
                    }
                }
            }

            $this->render('/oeadmin/leaflets/index', [
                'leaflets' => OphTrConsent_Leaflet::model()->findAll(),
            ]);
        }
    }
}
