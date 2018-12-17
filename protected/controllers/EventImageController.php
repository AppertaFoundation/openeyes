<?php

/**
 * Class EventImageController
 */
class EventImageController extends BaseController
{
    private static $KEEP_WORKING_FILES = true;

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
                'actions' => array('view', 'create','getImageInfo', 'getImageUrl'),
                'users' => array('@'),
            ),
            array(
                'deny',
                'users' => array('*'),
            ),
        );
    }

    public function actionGetImageUrl($event_id, $return_value = false)
    {
        $created_image_status_id = EventImageStatus::model()->find('name = "CREATED"')->id;
        // If the event image doesn't already exist
        $event_image = EventImage::model()->find('event_id = ? AND status_id = ?',
            array($event_id, $created_image_status_id));
        $event = Event::model()->findByPk($event_id);
        if (!isset($event_image) || isset($event) && $event_image->last_modified_date < $event->last_modified_date) {
            // Then try to make it
            EventImageManager::actionGenerateImage($event);
        }

        // Check again to see if it exists (an error might have occurred during generation)
        if (EventImage::model()->exists('event_id = ? AND status_id = ?',
            array($event_id, $created_image_status_id))) {
            // THen return that url
            $url = $this->createUrl('view', array('id' => $event_id));
            if ($return_value) {
                return $url;
            } else {
                echo $url;
            }
        }
        // otherwise return nothing
        return '';
    }

    public function actionGetImageInfo($event_id)
    {
        try {
            $url = $this->actionGetImageUrl($event_id , true);
            $page_count = count(EventImage::model()->findAll('event_id = ?', array($event_id)));
            if ($page_count != 0) {
                $image_info = ['page_count' => $page_count, 'url' => $url];
                echo CJSON::encode($image_info);
            }
        }
        catch (Exception $exception){
            echo CJSON::encode(['error' => $exception->getMessage()]);
        }
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     * @param integer $page The page number for multi image events
     */
    public function actionView($id, $page = null, $eye = null)
    {
        // Adapted from http://ernieleseberg.com/php-image-output-and-browser-caching/
        $criteria = new CDbCriteria();
        $criteria->addCondition('event_id = :event_id');
        $criteria->params[':event_id'] = $id;
        if ($page !== null) {
            $criteria->addCondition('page = :page');
            $criteria->params[':page'] = $page;
        }
        if ($eye !== null) {
            $criteria->addCondition('eye_id = :eye');
            $criteria->params[':eye'] = $eye;
        }
        $criteria->order = 'eye_id = ' . Eye::RIGHT . ' DESC';

        $model = EventImage::model()->find($criteria);
        if(isset($model)) {
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
                $image_data = $model->image_data;
                // Image not cached or cache outdated, we respond '200 OK' and output the image.
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $fileModTime) . ' GMT', true, 200);

                header('Content-transfer-encoding: binary');
                header('Content-length: ' . strlen($image_data));
                echo $image_data;
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

    /**
     * Loads the model with the given ID
     *
     * @param int $id The ID to find
     * @return EventImage The image that is found
     * @throws CHttpException Thrown if an EvebtImage with the given ID cannot be found
     */
    public function loadModel($id)
    {
        $model = EventImage::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }
}
