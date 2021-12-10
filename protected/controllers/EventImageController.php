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
                'actions' => array(
                    'view',
                    'create',
                    'getImageInfo',
                    'getImageUrl',
                    'getImageUrlsBulk',
                    'generateImage'
                ),
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
        $event_image = EventImage::model()->find(
            'event_id = ? AND status_id = ?',
            array($event_id, $created_image_status_id)
        );
        $event = Event::model()->findByPk($event_id);
        // If the event image doesn't already exist
        if (!isset($event_image) ||
            (isset($event) && ($event_image->last_modified_date < $event->last_modified_date))
        ) {
            // Then try to make it
            EventImageManager::actionGenerateImage($event->id);
        }

        // Check again to see if it exists (an error might have occurred during generation)
        if (EventImage::model()->exists(
            'event_id = ? AND status_id = ?',
            array($event_id, $created_image_status_id)
        )) {
            // THen return that url
            $url = $this->createUrl('view', array('id' => $event_id, 'modified' => !empty($event->last_modified_date) ? strtotime($event->last_modified_date) : ''));
            if ($return_value) {
                return $url;
            } else {
                echo $url;
            }
        }
        // otherwise return nothing
        return '';
    }

    /**
     * Get all the event image urls that are current and the remaining event ids
     *
     * @return string {"done_urs":[], "remaining_event_ids":[]}
     * @uses $_GET['event_ids']
     *
     */
    public function actionGetImageUrlsBulk()
    {
        $event_ids = CJSON::decode($_GET['event_ids']);
        $remaining_event_ids = null;
        $generated_image_event_ids = array();
        $created_image_status_id = EventImageStatus::model()->find('name = "CREATED"')->id;

        $criteria = new CDbCriteria();
        $criteria->select = 't.event_id, t.last_modified_date';
        $criteria->compare('status_id', $created_image_status_id);
        $criteria->addInCondition('event_id', $event_ids);
        $criteria->compare('t.last_modified_date', '>= e.last_modified_date');
        $criteria->addCondition('(t.page = 0 OR t.page IS NULL)');
        $criteria->join = 'join event e on t.event_id = e.id ';
        $criteria->order = 'event_date DESC';

        /**
         * @var $event_images CActiveRecord
         */
        $event_images = EventImage::model()->findAll($criteria);
        $generated_image_urls = [];

        if ($event_images) {
            foreach ($event_images as $event_image) {
                $generated_image_event_ids[] = array('id' => $event_image->event_id, 'modified' => $event_image->last_modified_date);
            }

            // Suppress error on when there are no generated image event ids
            $remaining_event_ids = isset($generated_image_event_ids[0]) ? array_diff($event_ids, $generated_image_event_ids[0]) : $event_ids;

            foreach ($generated_image_event_ids as $image) {
                $generated_image_urls[$image['id']] = $this->createUrl('view', array('id' => $image['id'], 'modified' => strtotime($image['modified'])));
            }
        }
        echo \CJSON::encode(
            array(
                'generated_image_urls' => $generated_image_urls,
                'remaining_event_ids' => $remaining_event_ids
            )
        );
    }

    public function actionGetImageInfo($event_id)
    {
        try {
            $url = $this->actionGetImageUrl($event_id, true);
            $page_count = count(EventImage::model()->findAll('event_id = ?', array($event_id)));
            if ($page_count != 0) {
                $image_info = ['page_count' => $page_count, 'url' => $url];
                $this->renderJSON($image_info);
            }
        } catch (Exception $exception) {
            $this->renderJSON(['error' => $exception->getMessage()]);
        }
    }

    /**
     * @param $id
     * @throws Exception
     */
    public function actionGenerateImage($id)
    {
        $event = Event::model()->findByPk($id);
        if (!$event) {
            throw new Exception("Event not found: $id");
        }

        if (isset($_POST['last_modified_date']) && strtotime($event->last_modified_date) != $_POST['last_modified_date']) {
            echo 'outofdate';

            return;
        }

        // Regenerate the EventImage in the background
        EventImageManager::actionGenerateImage($event->id);

        echo 'ok';
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     * @param integer $page The page number for multi image events
     */
    public function actionView($id, $page = null, $eye = null, $document_number = null)
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

        if ($document_number !== null && $document_number != "") {
            $criteria->addCondition('document_number = :document_number');
            $criteria->params[':document_number'] = $document_number;
        } else {
            $criteria->addCondition('document_number IS NULL');
        }
        $criteria->order = 'eye_id = ' . Eye::RIGHT . ' DESC';

        $model = EventImage::model()->find($criteria);
        if (isset($model)) {
            $file_mod_time = strtotime($model->last_modified_date);
            $headers = $this->getRequestHeaders();

            header('Content-type: image/jpeg');
            header('Cache-Control: private, immutable, max-age=31536000');
            // Check if the client is validating his cache and if it is current.
            if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == $file_mod_time)) {
                // Client's cache IS current, so we just respond '304 Not Modified'.
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $file_mod_time) . ' GMT', true, 304);
            } else {
                $image_data = $model->image_data;
                // Image not cached or cache outdated, we respond '200 OK' and output the image.
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $file_mod_time) . ' GMT', true, 200);

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
