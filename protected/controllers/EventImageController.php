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
                'actions' => array('view', 'create'),
                'users' => array('@'),
            ),
            array(
                'deny',
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     * @param integer $page The page number for multi image events
     */
    public function actionView($id, $page=null)
    {
        // Adapted from http://ernieleseberg.com/php-image-output-and-browser-caching/
        $criteria = new CDbCriteria();
        $criteria->addCondition('event_id = :event_id');
        $criteria->params[':event_id'] = $id;
        if($page !== null)
        {
            $criteria->addCondition('page = :page');
            $criteria->params[':page'] = $page;
        }

        $model = EventImage::model()->find($criteria);
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

        $this->createImageForEvent($event);
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
    public function createImageForEvent($event)
    {
        switch ($event->eventType->class_name) {
            case 'OphCoCorrespondence':
                $this->createImageForPdfEvent($event);
                break;
            case 'OphCoDocument':
                $this->createImageForImageEvent($event);
                break;
            default:
                $this->createImageForHtmlEvent($event);
                break;
        }
    }

    /**
     * @param Event $event
     * @throws Exception
     */
    public function createImageForHtmlEvent($event)
    {
        $eventImage = $this->stubEventImageForEvent($event);

        try {
            $content = $this->getEventAsHtml($event);

            $image = new WKHtmlToImage();
            $image->setCanvasImagePath($event->getImageDirectory());

            $image->generateImage($event->getImageDirectory(), 'preview', '', $content,
                array('width' => 1250, 'quality' => 85));

            $input_image = $event->getImagePath('preview');
            $output_image = $event->getImagePath('preview_small');
            $imagick = new \Imagick($input_image);
            $this->resizeImage($imagick);
            $imagick->writeImage($output_image);

            $eventImage->event_id = $event->id;
            $eventImage->image_data = file_get_contents($output_image);
            $eventImage->status_id = EventImageStatus::model()->find('name = "CREATED"')->id;

            if (!$eventImage->save()) {
                throw new Exception('Could not save event image: ' . print_r($eventImage->getErrors(), true));
            }

            if (!self::$KEEP_WORKING_FILES) {
                $image->deleteFile($input_image);
                $image->deleteFile($output_image);
            }


        } catch (Exception $ex) {
            $eventImage->status_id = EventImageStatus::model()->find('name = "FAILED"')->id;
            $eventImage->save();
            throw $ex;
        }
    }

    protected function getPreviewImageWidth()
    {
        return 520;
    }

    protected function resizeImage($imagick)
    {
        $width = $this->getPreviewImageWidth();
        $height = $width * $imagick->getImageHeight() / $imagick->getImageWidth();
        $imagick->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 0.5);
    }


    /**
     * @param Event $event
     * @return string
     */
    protected function getImageDirectoryForEvent($event)
    {
        return $event->getImageDirectory();
    }

    /**
     * @param Event $event
     * @return string
     */
    protected function getEventAsHtml($event)
    {
        ProfileController::changeDisplayTheme(Yii::app()->user->id, 'dark');
        ob_start();
        $url = '/' . $event->eventType->class_name . '/default/image/' . $event->id;
        Yii::app()->runController($url);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * @param Event $event
     *
     * @throws Exception
     */
    public function createImageForPdfEvent($event)
    {
        $this->removeImagesForEvent($event);

        ob_start();
        $url = '/' . $event->eventType->class_name . '/default/image/' . $event->id;
        Yii::app()->runController($url);
        $pdf_path = ob_get_contents();
        ob_end_clean();

        $pdf_imagick = new Imagick();
        $pdf_imagick->readImage($pdf_path);
        $pdf_imagick->setImageFormat('png');

        $output_path = $this->getImagePath($event);

        if (!$pdf_imagick->writeImages($output_path, false)) {
            throw new Exception();
        }

        for ($page = 0; ; ++$page) {

            $pagePreviewPath = $this->getImagePath($event, $page);
            if (!file_exists($pagePreviewPath)) {
                break;
            }

            $imagickPage = new Imagick();
            $imagickPage->readImage($pagePreviewPath);
            $this->resizeImage($imagickPage);

            if ($imagickPage->getImageAlphaChannel()) {
                $imagickPage->setImageAlphaChannel(11);
                $imagickPage->setImageBackgroundColor('white');
                $imagickPage->mergeImageLayers(imagick::LAYERMETHOD_FLATTEN);
            }
            $imagickPage->writeImage($pagePreviewPath);

            $eventImage = EventImage::model()->find('event_id = :event_id AND (page IS NULL OR page = :page)',
                array(':event_id' => $event->id, ':page' => $page)) ?: new EventImage();
            $eventImage->event_id = $event->id;
            $eventImage->page = $page;
            $eventImage->image_data = file_get_contents($pagePreviewPath);
            $eventImage->status_id = EventImageStatus::model()->find('name = "CREATED"')->id;

            if (!$eventImage->save()) {
                throw new Exception('Could not save event image: ' . print_r($eventImage->getErrors(), true));
            }

        }
    }

    public function getImagePath($event, $page = null)
    {
        if ($page === null) {
            return $event->getImageDirectory() . DIRECTORY_SEPARATOR . 'preview.png';
        } else {
            return $event->getImageDirectory() . DIRECTORY_SEPARATOR . 'preview-' . $page . '.png';
        }
    }

    /**
     * @param $event
     */
    public function createImageForImageEvent($event)
    {
    }

    /**
     * @param $event
     */
    protected function removeImagesForEvent($event)
    {
        foreach (EventImage::model()->findAll('event_id = :event_id',
            array(':event_id' => $event->id)) as $eventImage) {
            $eventImage->delete();
        }

        for ($imageCount = 0; ; ++$imageCount) {
            $filename = $event->getImageDirectory() . DIRECTORY_SEPARATOR . 'preview-' . $imageCount . '.png';
            if (file_exists($filename)) {
                @unlink($filename);
            } else {
                break;
            }
        }

    }

    /**
     * @param $event
     * @return EventImage
     */
    protected function stubEventImageForEvent($event)
    {
        $eventImage = new EventImage();
        $eventImage->event_id = $event->id;
        $eventImage->status_id = EventImageStatus::model()->find('name = "GENERATING"')->id;
        $eventImage->save();

        return $eventImage;
    }

}
