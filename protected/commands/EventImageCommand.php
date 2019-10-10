<?php


class EventImageCommand extends CConsoleCommand
{

    private $curlConnection = null;

    public function actionIndex()
    {
        echo $this->getHelp();
    }

    public function getHelp()
    {
        return <<<EOH

        
EventImage preview manager
        
This command creates preview images for events to be used in the lightning viewer and patient sidebar popups

USAGE
  php yiic.eventimage [action] [parameter]
        
Following actions are available:

 - create                           : Create an event image for every event that isn't already imaged or has an outdated image 
                                      (starting with the most recent event)
 - create --event=[event_id]...     : Creates an event image for the given event(s)
 - create --patient=[patient_id]... : Creates an event image for all the events for the given patients
 - clean                            : Removes all EventImage records that are not in the CREATED state 
                                      (i.e. those that failed to generate for some reason)
 - reset                            : Removes all EventImage records
 - help                             : Display this help and exit
 
EOH;
    }

    public function actionClean()
    {
        EventImage::model()->deleteAll('status_id != ?',
            array(EventImageStatus::model()->find('name = "CREATED"')->id));
    }

    public function actionReset()
    {
        if ($this->confirm('Remove all EventImages, regardless of status?')) {
            EventImage::model()->deleteAll();
        }
    }

    public function actionCreate($args, array $patient = null, array $event = null)
    {
        $this->openCurlConnection();
        if (isset($patient)) {
            foreach ($patient as $patient_id) {
                $p = Patient::model()->findByPk($patient_id);
                if (!$p) {
                    throw new Exception('Could not find patient with id: ' . $patient_id);
                }

                foreach ($p->episodes as $episode) {
                    foreach ($episode->events as $e) {
                        $this->createImageForEvent($e);
                    }
                }
            }
        } elseif (isset($event)) {
            foreach ($event as $event_id) {
                $e = Event::model()->findByPk($event_id);
                $this->createImageForEvent($e);
            }
        } else {
            $this->actionClean();
            $count = isset($args[0]) ? (int)$args[0] : INF;
            $this->createEventImages($count);
        }
        $this->closeCurlConnection();

    }

    public function openCurlConnection()
    {
        if ($this->curlConnection) {
            throw new Exception('Curl connection already open');
        }

        $login_page = Yii::app()->params['docman_login_url'];
        $username = Yii::app()->params['docman_user'];
        $password = Yii::app()->params['docman_password'];

        $this->curlConnection = curl_init();

        curl_setopt($this->curlConnection, CURLOPT_URL, $login_page);
        // disable SSL certificate check for locally issued certificates
        if (Yii::app()->params['disable_ssl_certificate_check']) {
            curl_setopt($this->curlConnection, CURLOPT_SSL_VERIFYPEER, false);
        }
        curl_setopt($this->curlConnection, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($this->curlConnection, CURLOPT_COOKIESESSION, true);
        curl_setopt($this->curlConnection, CURLOPT_COOKIEJAR, '/tmp/cookie.txt');
        curl_setopt($this->curlConnection, CURLOPT_COOKIEFILE, '/tmp/cookie.txt');
        curl_setopt($this->curlConnection, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($this->curlConnection);
        if (curl_errno($this->curlConnection)) {
            die(curl_error($this->curlConnection));
        }

        curl_setopt($this->curlConnection, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($this->curlConnection, CURLOPT_POST, true);

        $params = array(
            'LoginForm[username]' => $username,
            'LoginForm[password]' => $password,
        );
        curl_setopt($this->curlConnection, CURLOPT_POSTFIELDS, http_build_query($params));

        curl_exec($this->curlConnection);
        curl_setopt($this->curlConnection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlConnection, CURLOPT_POST, false);
    }

    private function closeCurlConnection()
    {
        curl_close($this->curlConnection);
        $this->curlConnection = null;
    }

    public function createEventImages($imageCount)
    {
        $events = EventImage::model()->getNextEventsToImage($imageCount);
        foreach ($events as $event) {
            $this->createImageForEvent($event);
        }
    }

    public function createImageForEvent($event)
    {
        $this->deleteEventImagesForEvent($event);
        $url = Yii::app()->params['event_image']['base_url'] . $event->eventType->class_name . '/default/createImage/' . $event->id;

        if (@Yii::app()->params['lightning_viewer']['debug_logging']) {
            Yii::log('Curling URL "' . $url);
        }

        curl_setopt($this->curlConnection, CURLOPT_URL, $url);
        $content = curl_exec($this->curlConnection);
        $http_code = curl_getinfo($this->curlConnection, CURLINFO_HTTP_CODE);

        if (@Yii::app()->params['lightning_viewer']['debug_logging']) {
            Yii::log('Result: ' . $http_code);
        }
    }

    private function deleteEventImagesForEvent($event)
    {
        EventImage::model()->deleteAll('event_id = ?', [$event->id]);
    }
}
