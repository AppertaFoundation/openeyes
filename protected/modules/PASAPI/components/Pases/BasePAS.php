<?php

namespace OEModule\PASAPI\components\Pases;

use OEModule\PASAPI\components\XmlHelper;

/**
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


abstract class BasePAS
{
    public $curl = null;
    protected $parser = null;

    /**
     * Saving non existent patient from this PAS, only if this PAS returns 1 result
     * @var bool
     */
    private $save_non_existing_patient = false;

    /**
     * PAS instances many times needs to access the PatientIdentifierType to
     * determinate the usage_type and any other properties to make the correct request
     * @var PatientIdentifierType
     */
    protected $type = null;

    protected $cache_time;

    public function __construct()
    {
        if ($this->parser === null) {
            $this->setParser(new XmlHelper());
        }

        if ($this->curl === null) {
            $this->curl = new \Curl();
        }
    }
    abstract public function init($config);

    /**
     * Sets the parser
     *
     * @param $parser_class
     */
    public function setParser($parser_class)
    {
        $this->parser = $parser_class;
    }

    /**
     * Sets the PatientIdentifierType
     * @param PatientIdentifierType $type
     */
    public function setType(\PatientIdentifierType $type)
    {
        $this->type = $type;
    }

    public function setCacheTime($cache_time)
    {
        $this->cache_time = $cache_time;
    }

    /**
     * Determinates if the PAS is available
     *
     * @return mixed
     */
    abstract public function isAvailable(): bool;

    /**
     * Determinates if the PAS query required or not
     *
     * @param $params
     * @return mixed
     */
    abstract public function isPASqueryRequired($params): bool;

    /**
     * Making PAS request
     *
     * @param $data
     * @return \OEModule\PASAPI\resources\Patient[]
     */
    abstract public function request($data): array;
}
