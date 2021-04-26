<?php

/**
 * Class SiteLogoController
 */
class SiteLogoController extends BaseController
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('index','view', 'getImageUrl', 'primary','secondary'),
                'users' => array('@'),
            ),
            array(
                'deny',
                'users' => array('*'),
            ),
        );
    }

    public function actionGetImageUrl($site_id, $logo_type = null, $return_value = false)
    {
        $site = Site::model()->findByPk($site_id);
        if (isset($site)) {
            if ($site->logo_id) {
                // get logos for site
                $logo = SiteLogo::model()->findByPk($site->logo_id);
            } else {
                $logo = SiteLogo::model()->findByPk(1);
            }
        } else {
            $logo = SiteLogo::model()->findByPk(1);
        }

        if (!$logo) {
            // THen return that url
            $url = $logo->getImageUrl($logo_type);
            if ($return_value) {
                return $url;
            } else {
                echo $url;
            }
        } else {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return '';
    }

    public function actionIndex($secondary_logo = null)
    {
        // Navigate to the default logo
        $this->actionView(1, $secondary_logo);
    }

    public function actionPrimary($id = 1)
    {
        $this->actionView($id);
    }

    public function actionSecondary($id = 1)
    {
        $this->actionView($id, true);
    }

    /**
     * Displays a particular logo.
     * @param integer $id the ID of the logo to be displayed
     * @param integer $page The page number for multi image events
     */
    public function actionView($id = 1, $secondary_logo = false)
    {
        // Adapted from http://ernieleseberg.com/php-image-output-and-browser-caching/
        $criteria = new CDbCriteria();
        $criteria->addCondition('id = :logo_id');
        $criteria->params[':logo_id'] = $id;
        $logo = SiteLogo::model()->find($criteria);

        if ($secondary_logo) {
            if (!$logo->secondary_logo) {
                $criteria = new CDbCriteria();
                $criteria->addCondition('id = :logo_id');
                $criteria->params[':logo_id'] = $logo->parent_logo;
                $logo = SiteLogo::model()->find($criteria);
            }
            if (!$logo->secondary_logo) {
                $criteria = new CDbCriteria();
                $criteria->addCondition('id = 1');
                $logo = SiteLogo::model()->find($criteria);
            }
        } else {
            if (!$logo->primary_logo) {
                $criteria = new CDbCriteria();
                $criteria->addCondition('id = :logo_id');
                $criteria->params[':logo_id'] = $logo->parent_logo;
                $logo = SiteLogo::model()->find($criteria);
            }
            if (!$logo->primary_logo) {
                $criteria = new CDbCriteria();
                $criteria->addCondition('id = 1');
                $logo = SiteLogo::model()->find($criteria);
            }
        }

        $file_mod_time = strtotime($logo->last_modified_date);
        $headers = $this->getRequestHeaders();

        header('Content-type: image/png');
        header('Cache-Control: public, max-age=31536000, immutable');
        // Check if the client is validating his cache and if it is current.
        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == $file_mod_time)) {
            // Client's cache IS current, so we just respond '304 Not Modified'.
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $file_mod_time) . ' GMT', true, 304);
        } else {
            if ($secondary_logo) {
                $image_data = $logo->secondary_logo;
            } else {
                $image_data = $logo->primary_logo;
            }
            // Image not cached or cache outdated, we respond '200 OK' and output the image.
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $file_mod_time) . ' GMT', true, 200);

            header('Content-transfer-encoding: binary');
            header('Content-length: ' . strlen($image_data));
            echo $image_data;
        }
    }

    /**
     * @return array
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

    /**
     * Loads the model with the given ID
     *
     * @param int $id The ID to find
     * @return SiteLogo The SiteLogo Model that is found
     * @throws CHttpException Thrown if an SiteLogo with the given ID cannot be found
     */
    public function loadModel($id)
    {
        $model = SiteLogo::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
    }
}
