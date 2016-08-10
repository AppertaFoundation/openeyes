<?PHP
namespace OEModule\OphCoCvi\components;

    class ODTTable{
        var $data = array();
        
        function __construct( $tableName ){
            $this->data=array('name' => $tableName, 'element-type'=>'table');    

        }
        
        function createRow(){
            return new Row();
        }
        
        function addRow( $row ){
            $rowData = $row->getData();
            $this->data['rows'][]=$rowData;
        }
        
        function createCell(){
            return new Cell();
        }        

        function getData(){
            return $this->data;    
        }

        function setCellData($row,$col,$cellData){
            $this->data['rows'][$row-1]['cells'][$col-1]['data']=$cellData;
        }
        
        function setRowData($row,$rowData){
            foreach( $this->data['rows'][$row-1]['cells'] as $key => $oneCell ){
                $this->data['rows'][$row-1]['cells'][$key]['data'] = $rowData[ $key ];
            }
        }
        
        function FillData($tableData){
            foreach( $this->data['rows'] as $rowKey => $oneRow ){
                foreach( $oneRow['cells'] as $colKey => $oneCell ){
                    if($oneCell['cell-type']!='covered'){
                        $this->data['rows'][$rowKey]['cells'][$colKey]['data']=$tableData[$rowKey][$colKey];
                    }
                }
            }
        }
    
        function getObjType(){
            return $this->data['element-type'];
        }    
    }
?>