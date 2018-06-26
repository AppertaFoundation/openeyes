<?php

class EventImageController extends BaseController
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
                'actions' => array('view', 'create'),
                'users' => array('@'),
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        // Adapted from http://ernieleseberg.com/php-image-output-and-browser-caching/
        $model = EventImage::model()->find('event_id = :event_id', array(':event_id' => $id));
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
            // Image not cached or cache outdated, we respond '200 OK' and output the image.
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $fileModTime) . ' GMT', true, 200);

            header('Content-transfer-encoding: binary');
            header('Content-length: ' . strlen($model->image_data));
            echo $model->image_data;
        }
    }

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
     * @param int $id Creates an event image for the vent with the given ID
     * @throws CHttpException Thrown if the image cannot be found
     * @throws Exception Thrown if an issue occurs when generating the image
     */
    public function actionCreate($id)
    {
        $event = Event::model()->findByPk($id);
        if (!$event) {
            throw new CHttpException(404, 'Could not find event ' . $id);
        }

        self::createImageForEvent($event);
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


    /**
     * Creates an EventImage for the given event.
     * An EventImage record is created whether the actual image generation is successful or not.
     * The "status" of the EventIMage will be "CREATED" if the image is created successfully, "FAILED" if an error occurs, and "GENERATING" if an error occurs that cannot be caught (e.g. a
     *
     * @param Event $event The event to create the image for
     * @throws Exception Thrown if an error occurs when generating the event
     */
    public static function createImageForEvent($event)
    {
        $eventImage = EventImage::model()->find('event_id = :event_id',
            array(':event_id' => $event->id)) ?: new EventImage();
        $eventImage->event_id = $event->id;
        $eventImage->status_id = EventImageStatus::model()->find('name = "GENERATING"')->id;
        $eventImage->save();

        try {
            ProfileController::changeDisplayTheme(Yii::app()->user->id, 'dark');

            ob_start();

            $url = '/' . $event->eventType->class_name . '/default/image/' . $event->id;
            Yii::app()->runController($url);

            $content = ob_get_contents();
            ob_end_clean();

            $image = new WKHtmlToImage();
            $image->setCanvasImagePath($event->imageDirectory);
            $directory = Yii::app()->assetManager->basePath;

            $image->generateImage($directory, 'event_' . $event->id, '', $content,
                array('width' => 1250, 'quality' => 85));
            $input_image = $directory . '/event_' . $event->id . '.png';
            $output_image = $directory . '/event_' . $event->id . '_small.png';

            $imagick = new \Imagick($input_image);
            $width = 540;
            $height = $width * $imagick->getImageHeight() / $imagick->getImageWidth();
            $imagick->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 0.65);
            $imagick->writeImage($output_image);

            $eventImage->event_id = $event->id;
            $eventImage->image_data = file_get_contents($output_image);
            $eventImage->status_id = EventImageStatus::model()->find('name = "CREATED"')->id;

            if (!$eventImage->save()) {
                throw new Exception('Could not save event image: ' . print_r($eventImage->getErrors(), true));
            }

            $image->deleteFile($input_image);
            $image->deleteFile($output_image);

        } catch (Exception $ex) {
            $eventImage->status_id = EventImageStatus::model()->find('name = "FAILED"')->id;
            $eventImage->save();
            throw $ex;
        }
    }
}
