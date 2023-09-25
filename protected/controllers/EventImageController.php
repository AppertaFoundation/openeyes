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

    public function actionGetImageUrl($event_id, $return_value = false, $eye_id = null)
    {
        $created_image_status_id = EventImageStatus::model()->find('name=?', [EventImageStatus::STATUS_CREATED])->id;
        $condition = 'event_id = ? AND status_id = ? AND attachment_data_id IS NULL';
        $params = [$event_id, $created_image_status_id];
        if ($eye_id) {
            $condition .= " AND eye_id=?";
            $params[] = $eye_id;
        }
        $event_image = EventImage::model()->find($condition, $params);

        $event = $event_image ? $event_image->event : Event::model()->findByPk($event_id);

        // If the event image doesn't already exist
        // OR event is deleted
        // OR event is modified after the image is generated
        if (!isset($event_image) ||
            (isset($event) && ((int)$event->deleted === 1)) ||
            (isset($event) && ($event_image->last_modified_date < $event->last_modified_date))
        ) {
            // Then try to make it
            EventImageManager::actionGenerateImage($event->id);
        }

        // Check again to see if it exists (an error might have occurred during generation)
        $image_exists = EventImage::model()->exists($condition, $params);
        if ($image_exists) {
            $url_params = [
                'id' => $event_id,
                'modified' => !empty($event->last_modified_date) ? strtotime($event->last_modified_date) : '',
            ];

            $page = \Yii::app()->request->getParam('page', null);
            if (isset($page)) {
                $url_params['page'] = $page;
            }

            if ($eye_id) {
                $url_params['eye_id'] = $eye_id;
            }

            // Then return that url
            $url = $this->createUrl('view', $url_params);

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
            $is_bilateral_document = EventImage::model()->count('event_id = ? AND eye_id is not null AND attachment_data_id IS NULL', [$event_id]) > 0;
            if ($is_bilateral_document) {
                foreach (["left" => Eye::LEFT, "right" => Eye::RIGHT] as $side => $eye_id) {
                    $url = $this->actionGetImageUrl($event_id, true, $eye_id);
                    $page_count = EventImage::model()->count('event_id=? AND eye_id=? AND attachment_data_id IS NULL', [$event_id, $eye_id]);
                    if ($page_count > 0) {
                        $image_info[$side] = ['page_count' => $page_count, 'url' => $url];
                    }
                }
                $this->renderJSON($image_info);
            } else {
                $url = $this->actionGetImageUrl($event_id, true);
                $page_count = EventImage::model()->count('event_id = ? AND attachment_data_id IS NULL', [$event_id]);
                if ($page_count != 0) {
                    $image_info = ['page_count' => $page_count, 'url' => $url];
                    $this->renderJSON($image_info);
                }
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
        if (!$page) {
            $page = \Yii::app()->request->getParam('page', null);
        }
        if (is_numeric($page)) {
            $criteria->addCondition('page = :page');
            $criteria->params[':page'] = $page;
        }
        if (!$eye) {
            $eye = \Yii::app()->request->getParam('eye_id', null);
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
        $criteria->order = 'eye_id DESC';

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
