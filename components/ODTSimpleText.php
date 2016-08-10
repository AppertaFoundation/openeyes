<?PHP
namespace OEModule\OphCoCvi\components;

    class ODTSimpleText{
        var $data = '';
        
        function __construct($name) {
            $this->data['element-type'] = 'simple-text';
            $this->data['name'] = $name;
        }        
        
        function getData(){
            return $this->data;
        }    
        
        function getObjType(){
            return $this->data['element-type'];
        }                
    }
?>