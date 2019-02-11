<?PHP
/**
 * OpenEyes
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
/*
 * Generate xml table to odt file
 */

class ODTTable
{
    /**
     * @var array
     */
    protected $data = array();

    public function __construct($tableName)
    {
        $this->data = array('name' => $tableName, 'element-type' => 'table');
    }

    public function createRow()
    {
        return new Row();
    }

    public function addRow($row)
    {
        $rowData = $row->getData();
        $this->data['rows'][] = $rowData;
    }

    public function createCell()
    {
        return new Cell();
    }

    public function getData()
    {
        return $this->data;
    }

    public function setCellData($row, $col, $cellData)
    {
        $this->data['rows'][$row - 1]['cells'][$col - 1]['data'] = ODTDataHandler::encodeTextForODT($cellData);
    }

    public function setRowData($row, $rowData)
    {
        foreach ($this->data['rows'][$row - 1]['cells'] as $key => $oneCell) {
            $this->data['rows'][$row - 1]['cells'][$key]['data'] = $rowData[$key];
        }
    }

    public function fillData($tableData)
    {
        foreach ($this->data['rows'] as $rowKey => $oneRow) {
            foreach ($oneRow['cells'] as $colKey => $oneCell) {
                if ($oneCell['cell-type'] != 'covered') {
                    $this->data['rows'][$rowKey]['cells'][$colKey]['data'] = $tableData[$rowKey][$colKey];
                }
            }
        }
    }

    public function getObjType()
    {
        return $this->data['element-type'];
    }
}