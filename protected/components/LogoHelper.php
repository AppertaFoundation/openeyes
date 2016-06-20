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
        
    $path = Yii::app()->basePath . '/images/logo/';
    $yourImageUrl = Yii::app()->assetManager->publish($path);
    $imageList = scandir($path, 1);

    $headerPosition = strpos($imageList[1], "header");
    $secondaryPosition = strpos($imageList[0], "secondary");
    //$logo = array();
    if ($headerPosition !== false) 
    {
        $logo['headerLogo'] = $yourImageUrl."/".$imageList[1];
    }
    if($secondaryPosition !== false) 
    {
        $logo['secondaryLogo'] = $yourImageUrl."/".$imageList[0];
    }
   return $logo;
    
}   
}