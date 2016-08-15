<?PHP
namespace OEModule\OphCoCvi\components;
    class ODTRow{
        var $data = array();
        
        function __construct() {
            $this->data['element-type'] = 'row';
        }
        
        function addCell($cell){
            $this->data['cells'][]=$cell->getData();
        }
        
        function getData(){
            return $this->data;    
        }
        
        function setObjType($type){
            $this->data['row-type'] = $type;
        }
        
        function getObjType(){
            return $this->data['element-type'];
        }    
    }
?>