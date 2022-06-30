<?php

namespace OEModule\PASAPI\components\Pases;
use OEModule\PASAPI\models\PasApiAssignment;

class SearchUpdatePas extends DefaultPas
{
    /**
     * Checks if the PAS request is required or not
     *
     * @param $params
     * @return bool
     * @throws Exception
     */
    public function isPASqueryRequired($params) : bool
    {
        $pasapi_allowed_search_params = $this->getValidAllowedSearchParams();

        if (is_array($pasapi_allowed_search_params) && !empty($pasapi_allowed_search_params)) {
            foreach ($params as $key => $param) {
                if ($param != null && $param != "" && !in_array($key, $pasapi_allowed_search_params)) {
                    return false;
                }
            }
        }

        if (!empty($params['patient_identifier_value']) && isset($this->cache_time)) {
            //get the patient
            $criteria = new \CDbCriteria();

            $criteria->join = " JOIN patient_identifier pi ON t.internal_id = pi.patient_id";

            $criteria->addCondition('pi.value = :value');
            $criteria->addCondition('pi.patient_identifier_type_id = :patient_identifier_type_id');
            $criteria->addCondition('resource_type = "Patient"');

            $criteria->params[':value'] = $params['patient_identifier_value'];
            $criteria->params[':patient_identifier_type_id'] = $params['patient_identifier_type_id'];

            $assignment = PasApiAssignment::model();
            $assignment->find($criteria);

            $assignment->pas_cache_time = $this->cache_time;

            if ($assignment && !$assignment->isStale()) {
                return false;
            }
        }

        return true;
    }
}
