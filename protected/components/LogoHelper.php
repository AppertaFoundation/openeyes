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
    public function render($template = '//base/_logo', $size = 100, $site_id=null)
    {
        if(!isset($site_id)){
            $site_id = Yii::app()->session['selected_site_id'];
        }
        return Yii::app()->controller->renderPartial(
            $template,
            array(
                'logo' => $this->getLogoURLs($site_id),
                'size' => $size
            ),
            true
        );
    }

    
    public function getLogoURLs($site_id=null , $return_value = false)
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
        if(isset($site_id)){
            $options = array('id' => $logo_id);
        }
        if(isset($logo->primary_logo)){
            $logoOut['primaryLogo'] = Yii::app()->createAbsoluteUrl($url1, $options);
        }
        if(isset($logo->secondary_logo)){
            $logoOut['secondaryLogo'] = Yii::app()->createAbsoluteUrl($url2, $options);
        }
        return $logoOut;
    }
    
    public function getUploadedLogo($id=1, $secondary_logo = null,  $return_value = false)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('id = :logo_id');
        $criteria->params[':logo_id'] = $id;
        $model = SiteLogo::model()->find($criteria);
        if (isset($model)) {
            // If we need to return the value
            if($return_value){
                if(!empty($secondary_logo)){
                    $image_data = $model->secondary_logo;
                }
                else{                  
                    $image_data = $model->primary_logo;
                }
                    return $image_data;
            }
            // If we need to echo the value for viewing with a browser
            else{            
                $fileModTime = strtotime($model->last_modified_date);
                $headers = $this->getRequestHeaders();

                header('Content-type: image/png');
                header('Cache-Control: public');
                header('Pragma:');
                // Check if the client is validating his cache and if it is current.
                if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == $fileModTime)) {
                    // Client's cache IS current, so we just respond '304 Not Modified'.
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $fileModTime) . ' GMT', true, 304);
                } else {
                    if(!empty($secondary_logo)){
                        $image_data = $model->secondary_logo;
                    }
                    else{                  
                        $image_data = $model->primary_logo;
                        
                    }
                    // Image not cached or cache outdated, we respond '200 OK' and output the image.
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $fileModTime) . ' GMT', true, 200);

                    header('Content-transfer-encoding: binary');
                    header('Content-length: ' . strlen($image_data));
                    echo $image_data;
                }
            }
        }
    }

    /**
     * @return array|false
     */
    private function getRequestHeaders()
    {
        if (function_exists("apache_request_headers")) {
            if ($headers = apache_request_headers()) {
                return $headers;
            }
        }

        $headers = array();
        // Grab the IF_MODIFIED_SINCE header
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $headers['If-Modified-Since'] = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
        }

        return $headers;
    }
}