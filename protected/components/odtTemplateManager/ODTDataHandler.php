<?PHP
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/*
 * 
 */
class ODTDataHandler 
{
    /**
     * @var array
     */
    var $dataSource = array(); 

    function createTable( $tableName )
    {
        $inArray = $this->alreadyInDataSource( 'table', $tableName  );
        if( $inArray ){
            throw( new Exception('Table name already exists. (createTable)') );
        }

        return new ODTTable($tableName);
    }    

    function createRow()
    {
        return new ODTRow();
    }

    function createCell()
    {
        return new ODTCell();
    }

    function createSimpleText($name)
    {
        return new ODTSimpleText($name);
    }

    function createImage($name, $type, $binarySource)
    {
        $image = new ODTImage( $name, $type, $binarySource );
        return $image;
    }

    function addRow( $table, $row )
    {
        return $table->addRow($row);
    }

    function addCell( $row, $cell )
    {
        return $row->addCell($cell);
    }

    function setObjType($obj,$type) 
    {
        $obj->setObjType($type);
    }

    function setAttribute($obj)
    {
        $args = func_get_args();

        $type = $obj->getObjType();
        $args = func_get_args();

        switch( sizeof( $args ) ){
            case 2 : 
                if( is_array( $args[1] ) ){

                    foreach( $args[1] as $name => $value ){
                        $obj->data[$name] = $value;        
                    }
                }
                break;
            case 3 :
                if( !is_array( $args[1] ) && !is_array( $args[2] ) ){
                    $obj->data[$args[1]]=$args[2];
                }
                break;
            default: throw new Exception('Invalid parameter list.');
        }

    }

    function setTableCellData($table,$row,$col,$cellData)
    {
        $table->setCellData($row,$col,$cellData);
    }

    function setTableRowData($table,$row,$rowData)
    {
        $table->setRowData($row,$rowData);
    }

    function fillTableData($table,$tableData)
    {
        $table->FillData($tableData);
    }

    function alreadyInDataSource( $objectType, $name )
    {
        if( !isset( $this->dataSource[$objectType] ) ) return false;
        foreach( $this->dataSource[$objectType] as $oneSpecData ){
            if( $oneSpecData['name'] == $name )    {
                return true;    
            }
        }

        return false;
    }

    function createText($name,$data=null)
    {
        return new ODTSimpleText($name,$data);
    }

    function setTableAndSimpleTextDataFromArray( $array )
    {
        foreach($array as $key => $value){
            
            if( is_array($value) ){ // generate table data
                $table = $this -> createTable( $key );
                foreach($value as $oneRow){
                    $row = $this -> createRow();
                    if(is_array($oneRow)) {
                        foreach ($oneRow as $oneCellData) {
                            $cell = $this->createCell();
                            $this->setAttribute($cell, 'data', $oneCellData);
                            $row->addCell($cell);
                        }
                    }else{
                        echo "Not properly formatted table: ".$key;die;
                    }
                    $this -> addRow($table, $row);
                }
                $this -> import($table);
            } else { // simple-text datas
                $text = $this -> createText( $key , $value ); // name, data
                $this -> import($text);
            }
        }
    }

    function getSimpleTexts()
    {
        $texts = isset( $this->dataSource['simple-text'] ) ? $this->dataSource['simple-text'] : array();
        return $texts;
    }

    function getTables()
    {
        $tables = isset( $this->dataSource['table'] ) ? $this->dataSource['table'] : array();
        return $tables;
    }

    function import($obj)
    {
        $data = $obj->getData();
        $name = $data['name'];
        $type = $data['element-type'];

        $inArray = $this->alreadyInDataSource( $type, $name );            
        if( $inArray ){
            throw( new Exception('Table name already exists.') );
        }
        $this->dataSource[$type][]=$data;
    }
    
    function getDataSource()
    {
        return $this->dataSource;   
    }

    function generateSimpleTableHashData( $table )
    {
        $ret = array();
        foreach($table['rows'] as $rowID => $oneRow){
            if (array_key_exists('cells', $oneRow)) {
                foreach($oneRow['cells'] as $colID => $oneCell){
                    $ret[$rowID][$colID] = $oneCell['data'];
                }
            }
        }

        return $ret;
    }
}
?>