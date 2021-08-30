<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class LeafletsController extends BaseAdminController
{
    public $group = 'Consent form';

    /**
     * @throws Exception
     */
    public function actionList()
    {
        $leaflet_model = OphTrConsent_Leaflet::model();

        if (Yii::app()->request->isPostRequest) {
            $leaflets = Yii::app()->request->getParam('OphTrConsent_Leaflet');
            foreach ($leaflets as $key => $leaflet) {
                if (!$leaflet['name']) {
                    continue;
                }

                $leaflet_object = $leaflet_model->findByPk($leaflet['id']);
                if (!$leaflet_object) {
                    $leaflet_object = new OphTrConsent_Leaflet();
                }

                $leaflet_object->name = $leaflet['name'];
                $leaflet_object->display_order = $leaflet['display_order'];
                $leaflet_object->active = $leaflet['active'];

                if (!$leaflet_object->save()) {
                    throw new Exception('Unable to save Leaflets: ' . print_r($leaflet_object->getErrors(), true));
                }
            }
        }

        $this->render('/oeadmin/leaflets/index', [
            'leaflets' => $leaflet_model->findAll()
        ]);
    }
}
