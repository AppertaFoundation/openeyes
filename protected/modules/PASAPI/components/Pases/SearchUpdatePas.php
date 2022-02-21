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

        return true;
    }
}
