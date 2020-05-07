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
 - create --debug                   : Enables logging of output
 - clean                            : Removes all EventImage records that are not in the CREATED state 
                                      (i.e. those that failed to generate for some reason)
 - clean  --debug                   : Enables logging of output
 - reset                            : Removes all EventImage records
 - help                             : Display this help and exit
 
EOH;
    }

    public function actionClean($debug = null)
    {
        if ($debug) {
            echo "Cleaning EventImages with invalid statuses.";
        }
        EventImage::model()->deleteAll(
            'status_id != ?',
            array(EventImageStatus::model()->find('name = "CREATED"')->id)
        );
    }

    public function actionReset()
    {
        if ($this->confirm('Remove all EventImages, regardless of status?')) {
            EventImage::model()->deleteAll();
        }
    }

    public function actionCreate($args, array $patient = null, array $event = null, $debug = null)
    {
        if ($debug) {
            echo "Creating EventImages, inputs are: debug,".
            " patient = ".
            (isset($patient)? array_reduce($patient, function ($accumulator, $current) {
                return $current .",".$accumulator;
            }): "NULL")
            ." event = ".
            (isset($event)?  array_reduce($event, function ($accumulator, $current) {
                return $current .",".$accumulator;

            }) : "NULL")."\n";
        }
        $this->openCurlConnection();
        if (isset($patient)) {
            $pCount = count($patient);
            $pDigits = $pCount !== 0 ? floor(log10($pCount) + 1) : 1; // how many digits in the count?
            $pIndex = 1;
            foreach ($patient as $patient_id) {
                $p = Patient::model()->findByPk($patient_id);
                if (!$p) {
                    throw new Exception('Could not find patient with id: ' . $patient_id);
                }
                if ($debug) {
                    echo "\nFound patient " . $patient_id . ", this is patient ". $pIndex." of ".$pCount.".";
                }
                $epCount = count($p->episodes);
                $epDigits = $epCount !== 0 ? floor(log10($epCount) + 1) : 1; // how many digits in the count?
                $epIndex = 1;
                foreach ($p->episodes as $episode) {
                    if ($debug) {
                        echo "\n  Found episode ".$episode->id. ", this is episode ". $epIndex." of ".$epCount.".";
                    }
                    $eCount = count($episode->events);
                    $eDigits = $eCount !== 0 ? floor(log10($eCount) + 1) : 1; // how many digits in the count?
                    $eIndex = 1;
                    foreach ($episode->events as $e) {
                        $this->createImageForEvent($e);
                        if ($debug) {
                            echo "\n    ".str_pad($eIndex, $eDigits, "0", STR_PAD_LEFT)." of ". $eCount." -> Creating EventImage for event ".$e->id.".";
                        }
                        $eIndex++;
                    }
                    $epIndex++;
                }
                $pIndex++;
            }
        }
        if (isset($event)) {
            foreach ($event as $event_id) {
                if ($debug) {
                    echo "\nFinding event ".$event_id.".";
                }
                $e = Event::model()->findByPk($event_id);
                $this->createImageForEvent($e);
                if ($debug) {
                    echo "\n    Created EventImage for event ".$e->id.".";
                }
            }
        }
        if (!isset($patient)&&!isset($event)) {
            $this->actionClean($debug);
            $count = isset($args[0]) ? (int)$args[0] : INF;
            if ($debug) {
                echo "\nCreating ".$count." EventImages.";
            }
            $this->createEventImages($count, $debug);
        }
        $this->closeCurlConnection();
        if ($debug) {
            echo "\nSuccessfully created EventImages.\n";
        }
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
            curl_setopt($this->curlConnection, CURLOPT_SSL_VERIFYHOST, false);
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

    public function createEventImages($imageCount, $debug = null)
    {
        $events = EventImage::model()->getNextEventsToImage($imageCount, $debug);
        $eCount = count($events);
        if ($debug) {
            echo " and identified " . $eCount . " events in loaded modules that require generation.";
        }
        $eDigits = $eCount !== 0 ? floor(log10($eCount) + 1) : 1; // how many digits in the count?
        $eIndex = 1;
        foreach ($events as $event) {
            if ($debug) {
                echo "\n    ".str_pad($eIndex, $eDigits, "0", STR_PAD_LEFT)." of ". $eCount." -> Creating image for event: ".$event->id.", eventType: ".$event->eventType->class_name;
            }
                    $this->createImageForEvent($event);
            $eIndex++;
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
