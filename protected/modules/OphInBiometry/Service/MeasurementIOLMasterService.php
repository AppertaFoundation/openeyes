<?php

/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OphInBiometry\Service;

class MeasurementIOLMasterService extends \Service\ModelService
{
    protected static $operations = array(self::OP_READ, self::OP_UPDATE, self::OP_CREATE, self::OP_SEARCH);
    protected static $primary_model = 'OphInBiometry_Measurement';

    public function search(array &$params)
    {
        $this->setUsedParams($params, 'id');

        $model = $this->getSearchModel();
        if (isset($params['id'])) {
            $model->id = $params['id'];
        }

        $searchParams = array('pageSize' => null);

        return $this->getResourcesFromDataProvider($model->search($searchParams));
    }

    /**
     * @param type $res
     * @param type $measurement
     *
     * @return type
     */
    public function resourceToModel($res, $measurement)
    {

        //$measurement->patient_id = $res->patient_id;
        foreach ($res as $key => $value) {
            if ($key == 'resourceType') {
                continue;
            }
            $measurement->{$key} = $value;
        }

        $saved = $measurement->save();
        if (!$saved) {
            print_r($measurement->getErrors());
        }

        return $measurement;
    }
}
