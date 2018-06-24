<?php

class EventImageController extends BaseController
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
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
        $model = EventImage::model()->find('event_id = :event_id', array(':event_id' => $id));
        header('Content-type: image/png');
        echo $model->image_data;
    }

    /**
     * @param $id
     * @throws Exception
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
     * @param $id
     * @return EventImage
     * @throws CHttpException
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
     * @param Event $event
     * @throws Exception
     */
    public static function createImageForEvent($event)
    {
        $eventImage = EventImage::model()->find('event_id = :event_id',
            array(':event_id' => $event->id)) ?: new EventImage();
        $eventImage->event_id = $event->id;
        $eventImage->status_id = EventImageStatus::model()->find('status = "GENERATING"')->id;
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
            $cmd_str = 'convert ' . $input_image . ' -geometry 540x -sharpen 0x1.0 ' . $output_image;

            $res = shell_exec($cmd_str);
            if (!file_exists($output_image)) {
                throw new Exception('Unable to generate image ' . $res);
            }

            $eventImage->event_id = $event->id;
            $eventImage->image_data = file_get_contents($output_image);
            $eventImage->status_id = EventImageStatus::model()->find('status = "CREATED"')->id;

            if (!$eventImage->save()) {
                throw new Exception('Could not save event image: ' . print_r($eventImage->getErrors(), true));
            }

        } catch (Exception $ex) {
            $eventImage->status_id = EventImageStatus::model()->find('status = "FAILED"')->id;
            $eventImage->save();
            throw $ex;
        }
    }
}
