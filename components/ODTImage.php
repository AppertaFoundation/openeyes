<?PHP
namespace OEModule\OphCoCvi\components;
    class ODTImage{
        var $data = '';
        
        function __construct( $name, $type, $binarySource ) {
            $this->data['element-type'] = 'image';
            $this->data['image-type'] = $type;
            $this->data['name'] = $name;
            
            // BASE64
            $this->data['binary-source'] = $binarySource;
        }        

        function getData(){
            return $this->data;
        }    
        
        function getObjType(){
            return $this->data['element-type'];
        }                
    }
?>