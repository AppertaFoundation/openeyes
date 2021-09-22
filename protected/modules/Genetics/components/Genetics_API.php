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

class Genetics_API extends BaseAPI
{
    /**
     * @param $type
     * @param $partial
     *
     * @return bool|string
     */
    public function findViewFile($type, $partial)
    {
        $viewFile = Yii::getPathOfAlias('Genetics.views.' . $type . '.' . $partial) . '.php';

        if (file_exists($viewFile)) {
            return $viewFile;
        }

        return false;
    }

    /**
     * Validating Genes
     *
     * @param $variant
     * @return mixed
     */
    public function validateGene($variant)
    {
        $return = json_encode(['valid' => 'failed']);
        if (is_callable(Yii::app()->params['external_gene_validation'])) {
            $data = call_user_func(Yii::app()->params['external_gene_validation'], $variant);
            $json = json_decode($data, true);
            // false means the external service failed to validate the gene, eg. cURL wasn't able to access the internet
            if (isset($json['valid']) && $json['valid'] === 'failed') {
                // return value already set - default value
            } else {
                // external service returned valid json
                $return = $data;
            }
        }

        echo $return;
    }

    /**
     * @param Patient $patient
     * @return GeneticsPatient
     */
    public function getSubject(Patient $patient)
    {
        return GeneticsPatient::model()->findByAttributes(array('patient_id' => $patient->id));
    }
}
