<?php

/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoCvi\components;

use \ODTTemplateManager;

/**
 * A class for odt document modify and change 70 x 36,5mm labels on an A4 sheet and generate pdf
 */
class LabelManager extends ODTTemplateManager
{
    /**
     * @var string
     */
    public $cols = 3;
    public $rows = 8;

    /**
     * @param $filename
     * @param $templateDir
     * @param $outputDir
     * @param $outputName
     */
    public function __construct($filename, $templateDir, $outputDir, $outputName)
    {
        parent::__construct($filename, $templateDir, $outputDir, $outputName);
    }

    /**
     * Fill labels in document table by table-name
     * @param $tableName
     * @param $addressesArray
     * @param $firstEmptyCell
     */
    public function fillLabelsInTable($tableName, $addressesArray, $firstEmptyCell)
    {
        $dataArray = $this->generateArrayToTable($addressesArray, $firstEmptyCell);
        $this->fillTableByName($tableName, $dataArray);
    }

    /**
     * Generate array to ODTTemplatemanager fillTableByName valid data array from a simple array
     * @param $addressesArray
     * @param $firstEmptyCell
     */
    private function generateArrayToTable($addressesArray, $firstEmptyCell)
    {
        $result[] = array();
        $row = 0;
        if ($firstEmptyCell > 1) {
            for ($i = 1; $i < $firstEmptyCell; $i++) {
                if ($i % $this->cols == 1) {
                    $colCount = 0;
                } else {
                    if ($i % $this->cols == 2) {
                        $colCount = 1;
                    } else {
                        $colCount = 2;
                    }
                }

                $result[$row][$colCount] = '';
                if ($i % $this->cols == 0) {
                    $row++;
                }
            }
        } else {
            $i = 1;
        }

        foreach ($addressesArray as $val) {
            if ($i % $this->cols == 1) {
                $colCount = 0;
            } else {
                if ($i % $this->cols == 2) {
                    $colCount = 1;
                } else {
                    $colCount = 2;
                }
            }

            $result[$row][$colCount] = str_replace(array("\r\n", ','), array('', "\\n"), $val);

            if ($i % $this->cols === 0) {
                $row++;
            }
            $i++;
        }

        return $result;
    }
}
