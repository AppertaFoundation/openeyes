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
     * @throws CException
     */
    public function render($template = '//base/_logo', $size = 100, $site_id = null, $get_base_64 = false, $logo_id = null)
    {
        if(!isset($site_id)) {
            $site_id = Yii::app()->session['selected_site_id'];
        }
        return Yii::app()->controller->renderPartial(
            $template,
            array(
                'logo' => $this->getLogoURLs($site_id, $get_base_64, $logo_id),
                'size' => $size
            ),
            true
        );
    }
    
    public function getLogoURLs($site_id = null, $get_base_64 = false, $logo_id = null)
    {
        // default logo
        $default_logo_id = 1;
        $default_logo = SiteLogo::model()->findByPk($default_logo_id); 
        if(!$logo_id) {
            if($site_id) {
                $site = Site::model()->findByPk($site_id);
                $institution = $site->institution;
            }
            if (isset($site->logo_id)) {
                // get logos for site
                $logo_id = $site->logo_id;
                $logo = $site->logo;
            } elseif(isset($institution->logo_id)) {
                // get logos for site
                $logo_id = $institution->logo_id;
                $logo = $institution->logo;
            } else {
                // use default logo
                $logo_id = $default_logo_id;
                $logo = $default_logo;
            }
        } else {
            $logo= SiteLogo::model()->findByPk($logo_id);
        }

        $logoOut = array();
        $url1 = 'sitelogo/primary/';
        $url2 = 'sitelogo/secondary/';
        $options = array();
        if($get_base_64) {
            if($logo->primary_logo) {
                $imageData = base64_encode($logo->primary_logo);
                // Format the image SRC:  data:{mime};base64,{data};
                $logoOut['primaryLogo'] = 'data:;base64,'.$imageData;
            } elseif($default_logo->primary_logo) {
                $imageData = base64_encode($default_logo->primary_logo);
                // Format the image SRC:  data:{mime};base64,{data};
                $logoOut['primaryLogo'] = 'data:;base64,'.$imageData;
            }
            if($logo->secondary_logo) {
                $imageData = base64_encode($logo->secondary_logo);
                // Format the image SRC:  data:{mime};base64,{data};
                $logoOut['secondaryLogo'] = 'data:;base64,'.$imageData;
            } elseif($default_logo->secondary_logo) {
                $imageData = base64_encode($default_logo->secondary_logo);
                // Format the image SRC:  data:{mime};base64,{data};
                $logoOut['secondaryLogo'] = 'data:;base64,'.$imageData;
            }
        } else {        
            if($logo_id) {
                $options = array('id' => $logo_id);
            }
            if($logo->primary_logo || $default_logo->primary_logo) {
                $logoOut['primaryLogo'] = Yii::app()->createAbsoluteUrl($url1, $options);
            }
            if($logo->secondary_logo || $default_logo->secondary_logo) {
                $logoOut['secondaryLogo'] = Yii::app()->createAbsoluteUrl($url2, $options);
            }
        }
        return $logoOut;
    }
}