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
 * Class OphCoTherapyapplication_Helper.
 *
 * Static helper class to provide various data values for use in the Therapy application module
 */
class OphCoTherapyapplication_Helper
{
    public static function getInstance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new self();
        }

        return $inst;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * get Visual Acuity value list.
     *
     * Note that this currently relies on the Examination module, and operates on the assumption that we should provide
     * the default values from its API. Also, we record these as the display value, rather than the base value, as we
     * are considering this module as a reporting module rather than recording patient data specifically.
     *
     * @return array - list of of Visual Acuity values
     */
    private $_va_list = null;

    public function getVAList()
    {
        if (is_null($this->_va_list)) {
            $res = array();
            if ($api = Yii::app()->moduleAPI->get('OphCiExamination')) {
                foreach ($api->getVAList() as $bv => $v) {
                    $res[] = $v;
                };
            }
            $this->_va_list = $res;
        }

        return $this->_va_list;
    }

    public function getVABaseValueMapping()
    {
        if ($api = Yii::app()->moduleAPI->get('OphCiExamination')) {
            return array_flip($api->getVAList());
        }

        return null;
    }

    private $_va_list_for_form = null;

    /**
     * return key value list of visual acuity for use in forms.
     *
     * @return array - list of VA values
     */
    public function getVaListForForm()
    {
        if ($this->_va_list_for_form == null) {
            $res = array();
            foreach ($this->getVAList() as $v) {
                $res[$v] = $v;
            }
            $this->_va_list_for_form = $res;
        }

        return $this->_va_list_for_form;
    }
}
