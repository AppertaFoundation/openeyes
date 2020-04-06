<?php
/**
 * Created by PhpStorm.
 * User: PrasathKumar Sagadevan
 * Date: 07/06/16
 * Time: 15:45.
 */
class LogoHelper
{

    /**
     * Renders the template
     *
     * @param $template
     *
     * @return mixed
     */
    public function render($template = '//base/_logo', $size = 100, $site_id = null, $get_base_64 = false)
    {
        if(!isset($site_id)){
            $site_id = Yii::app()->session['selected_site_id'];
        }
        return Yii::app()->controller->renderPartial(
            $template,
            array(
                'logo' => $this->getLogoURLs($site_id, $get_base_64),
                'size' => $size
            ),
            true
        );
    }
    
    public function getLogoURLs($site_id = null, $get_base_64 = false)
    {
        $site = Site::model()->findByPk($site_id);
        if(isset($site->logo_id)){
            // get logos for site
            $logo_id = $site->logo_id;
            $logo = $site->logo;
        }
        else{
            // use default logo
            $logo_id = 1;
            $logo = SiteLogo::model()->findByPk(1);
        }

        $logoOut = array();
        $url1 = 'sitelogo/primary/';
        $url2 = 'sitelogo/secondary/';
        $options = array();
        if($get_base_64){
            if(isset($logo->primary_logo)){
                $imageData = base64_encode($logo->primary_logo);
                // Format the image SRC:  data:{mime};base64,{data};
                $logoOut['primaryLogo'] = 'data:;base64,'.$imageData;
            }
            if(isset($logo->secondary_logo)){
                $imageData = base64_encode($logo->secondary_logo);
                // Format the image SRC:  data:{mime};base64,{data};
                $logoOut['secondaryLogo'] = 'data:;base64,'.$imageData;
            }
        }
        else{        
            if($site_id){
                $options = array('id' => $logo_id);
            }
            if(isset($logo->primary_logo)){
                $logoOut['primaryLogo'] = Yii::app()->createAbsoluteUrl($url1, $options);
            }
            if(isset($logo->secondary_logo)){
                $logoOut['secondaryLogo'] = Yii::app()->createAbsoluteUrl($url2, $options);
            }
        }
        return $logoOut;
    }
}