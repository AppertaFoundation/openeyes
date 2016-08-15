<?PHP
namespace OEModule\OphCoCvi\components;

    class ODTSimpleText{
        var $data = '';
        
        function __construct($name,$data=null) {
            $this->data['element-type'] = 'simple-text';
            $this->data['name'] = $name;
            if($data !== null){
                $this->data['data'] = $data;
            }
        }        
        
        function getData(){
            return $this->data;
        }    
        
        function getObjType(){
            return $this->data['element-type'];
        }                
    }
?>