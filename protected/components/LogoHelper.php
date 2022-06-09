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
     * @param string $template
     * @param int $size
     * @param null $site_id
     * @param bool $get_base_64
     * @param null $logo_id
     * @param null $institution_id
     * @return mixed
     */
    public function render($template = '//base/_logo', $size = 100, $site_id = null, $get_base_64 = false, $logo_id = null, $institution_id = null)
    {
        if(!isset($site_id) && !isset($institution_id)) {
            $site_id = Yii::app()->session['selected_site_id'];
            $institution_id = Yii::app()->session['selected_institution_id'];
        }
        return Yii::app()->controller->renderPartial(
            $template,
            array(
                'logo' => $this->getLogoURLs($site_id, $get_base_64, $logo_id, $institution_id),
                'size' => $size
            ),
            true
        );
    }
    
    public function getLogoURLs($site_id = null, $get_base_64 = false, $logo_id = null, $institution_id = null): array
    {
        $requested_logo = array();

        //set the site and Institution if we have them (use the site's institution if site is available)
        if ($site_id) {
            $site = Site::model()->findByPk($site_id);
            $institution = $site->institution;
        } else if ($institution_id) {
            $institution = Institution::model()->findByPk($institution_id);
        }

        foreach(['primaryLogo', 'secondaryLogo'] as $logoLevel){
            $logo_type = $logoLevel == 'primaryLogo' ? 'primary_logo' : 'secondary_logo';
            // If they requested a specific logo, give them that.
            if(isset($logo_id)) {
                $requested_logo[$logoLevel] = SiteLogo::model()->findByPk($logo_id);
            }
            else{                
                //get the required logo for reference
                if (isset($site->logo_id) && !empty($site->logo->$logo_type)) {
                    // get logos for site
                    $requested_logo[$logoLevel] = $site->logo;
                }
                if(isset($institution->logo_id) && !empty($institution->logo->$logo_type)) {
                    // get logos for institution
                    $requested_logo[$logoLevel] = $institution->logo;
                } else {
                    // get default logo
                    $requested_logo[$logoLevel] = SiteLogo::model()->findByPk(1); 
                }
            }
        }

        return $this->getLogoFormatted($get_base_64, $requested_logo['primaryLogo'], $requested_logo['secondaryLogo']);
    }

    public function getLogoFormatted($get_base_64 = false, $primaryLogo = null, $secondaryLogo =null): array
    {
        //output the requested format
        $logoOut = array();
        if($get_base_64) {
            if($primaryLogo->primary_logo) {
                $imageData = base64_encode($primaryLogo->primary_logo);
                // Format the image SRC:  data:{mime};base64,{data};
                $logoOut['primaryLogo'] = 'data:;base64,'.$imageData;
            }
            if($secondaryLogo->secondary_logo) {
                $imageData = base64_encode($secondaryLogo->secondary_logo);
                // Format the image SRC:  data:{mime};base64,{data};
                $logoOut['secondaryLogo'] = 'data:;base64,'.$imageData;
            }
        } else {
            if($primaryLogo->primary_logo) {
                $options1 = array();
                $options1 = array('id' => $primaryLogo->id);
                $logoOut['primaryLogo'] = Yii::app()->createAbsoluteUrl('sitelogo/primary/', $options1);
            }
            if($secondaryLogo->secondary_logo) {
                $options2 = array();
                $options2 = array('id' => $secondaryLogo->id);
                $logoOut['secondaryLogo'] = Yii::app()->createAbsoluteUrl('sitelogo/secondary/', $options2);
            }
        }
        return $logoOut;
    }
}


