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
 * 
 */

class ODTDataHandler
{
    /**
     * Wrapper function to consistently encode text where appropriate for template functionality
     *
     * @param $text
     * @return string
     */
    public static function encodeTextForODT($text)
    {
        if (is_string($text)) {
            return htmlspecialchars($text, ENT_XML1);
        }
        return $text;
    }

    /**
     * @var array
     */
    protected $dataSource = array();

    /**
     * @param $tableName
     * @return ODTTable
     * @throws Exception
     */
    protected function createTable($tableName)
    {
        $inArray = $this->alreadyInDataSource('table', $tableName);
        if ($inArray) {
            throw(new Exception("Cannot create table when it already exists: {$tableName}"));
        }

        return new ODTTable($tableName);
    }

    /**
     * @return ODTRow
     */
    protected function createRow()
    {
        return new ODTRow();
    }

    /**
     * @return ODTCell
     */
    protected function createCell()
    {
        return new ODTCell();
    }

    /**
     * @param $name
     * @return ODTSimpleText
     */
    protected function createSimpleText($name)
    {
        return new ODTSimpleText($name);
    }

    public function createImage($name, $type, $binarySource)
    {
        $image = new ODTImage($name, $type, $binarySource);
        return $image;
    }

    public function addRow($table, $row)
    {
        return $table->addRow($row);
    }

    public function addCell($row, $cell)
    {
        return $row->addCell($cell);
    }

    public function setObjType($obj, $type)
    {
        $obj->setObjType($type);
    }

    public function setAttribute($obj)
    {
        $args = func_get_args();

        $type = $obj->getObjType();
        $args = func_get_args();

        switch (count($args)) {
            case 2 :
                if (is_array($args[1])) {

                    foreach ($args[1] as $name => $value) {
                        $obj->data[$name] = static::encodeTextForODT($value);
                    }
                }
                break;
            case 3 :
                if (!is_array($args[1]) && !is_array($args[2])) {
                    $obj->data[$args[1]] = static::encodeTextForODT($args[2]);
                }
                break;
            default:
                throw new Exception('Invalid parameter list.');
        }

    }

    public function setTableCellData($table, $row, $col, $cellData)
    {
        $table->setCellData($row, $col, $cellData);
    }

    public function setTableRowData($table, $row, $rowData)
    {
        $table->setRowData($row, $rowData);
    }

    public function fillTableData($table, $tableData)
    {
        $table->fillData($tableData);
    }

    public function alreadyInDataSource($objectType, $name)
    {
        if (!isset($this->dataSource[$objectType])) {
            return false;
        }
        foreach ($this->dataSource[$objectType] as $oneSpecData) {
            if ($oneSpecData['name'] == $name) {
                return true;
            }
        }

        return false;
    }

    public function createText($name, $data = null)
    {
        return new ODTSimpleText($name, $data);
    }

    /**
     * @param array $data
     * @throws Exception
     */
    public function setTableAndSimpleTextDataFromArray($data)
    {
        foreach ($data as $key => $value) {

            if (is_array($value)) { // generate table data
                $table = $this->createTable($key);
                foreach ($value as $row_data) {
                    $row = $this->createRow();
                    if (is_array($row_data)) {
                        foreach ($row_data as $cell_data) {
                            $cell = $this->createCell();
                            $this->setAttribute($cell, 'data', $cell_data);
                            $row->addCell($cell);
                        }
                    } else {
                        throw new Exception("Table {$key} is incorrectly structured - cell data not provided");
                    }
                    $this->addRow($table, $row);
                }
                $this->import($table);
            } else { // simple-text datas
                $text = $this->createText($key, $value); // name, data
                $this->import($text);
            }
        }
    }

    /**
     * @return array
     */
    public function getSimpleTexts()
    {
        $texts = isset($this->dataSource['simple-text']) ? $this->dataSource['simple-text'] : array();
        return $texts;
    }

    /**
     * @return array
     */
    public function getTables()
    {
        $tables = isset($this->dataSource['table']) ? $this->dataSource['table'] : array();
        return $tables;
    }

    /**
     * @param $obj
     * @throws Exception
     */
    protected function import($obj)
    {
        $data = $obj->getData();
        $name = $data['name'];
        $type = $data['element-type'];

        $inArray = $this->alreadyInDataSource($type, $name);
        if ($inArray) {
            throw new Exception('Table name already exists.');
        }
        $this->dataSource[$type][] = $data;
    }

    /**
     * @return array
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * @param array $table
     * @return array
     */
    public function generateSimpleTableHashData($table)
    {
        $ret = array();
        foreach ($table['rows'] as $rowID => $oneRow) {
            if (array_key_exists('cells', $oneRow)) {
                foreach ($oneRow['cells'] as $colID => $oneCell) {
                    $ret[$rowID][$colID] = $oneCell['data'];
                }
            }
        }

        return $ret;
    }
}