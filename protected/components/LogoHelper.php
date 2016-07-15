<?php
/**
 * Created by PhpStorm.
 * User: PrasathKumar Sagadevan
 * Date: 07/06/16
 * Time: 15:45
 */

class LogoHelper {


	public function __construct()
	{
		
	}

	public function render()
	{
		return Yii::app()->controller->renderPartial(
			'//base/_logo',
			array(
			        'logo'=>$this->getLogo()
			),
			true
		);
	}


         protected function getLogo(){
        
        $logo = array();
        
    $path = Yii::app()->basePath . '/runtime/';
    $yourImageUrl = Yii::app()->assetManager->publish($path);
      $imageLists = scandir($path, 1);
    
    
    foreach ($imageLists as $imageList) {
 if(strpos($imageList,"header") !== false) {
        $logo['headerLogo'] = $yourImageUrl."/".$imageList;
 }
 if(strpos($imageList,"secondary") !== false)  {
        $logo['secondaryLogo'] = $yourImageUrl."/".$imageList;
        
  }
}
 
   
   return $logo;
    
}   
}