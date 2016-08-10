<?PHP
namespace OEModule\OphCoCvi\components;
    class ODTCell{
        var $data = array();
        
        function __construct() {
            $this->data['element-type'] = 'cell';
        }
        
        function getData(){
            return $this->data;
        }
        
        function getObjType(){
            return $this->data['element-type'];
        }    
    }
?>