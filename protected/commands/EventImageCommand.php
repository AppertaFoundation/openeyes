<?php


class EventImageCommand extends CConsoleCommand
{
    private static $image_url = 'http://localhost/eventImage/create/';

    private $curlConnection = null;

    public function getHelp()
    {
        return 'Creates the preview image for the given event ID, or creates the preview image for the latest event without an image if no event ID is specified';
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
            $count = isset($args[0]) ? (int)$args[0] : 1;
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
        for ($i = 0; $i < $imageCount; ++$i) {
            $event = EventImage::model()->getNextEventToImage();
            if ($event === null) {
                echo "No out of date events found\n";

                return;
            }

            $this->createImageForEvent($event);
        }

    }

    public function createImageForEvent($event)
    {
        echo 'Curling URL "' . self::$image_url . $event->id . "\"\n";
        curl_setopt($this->curlConnection, CURLOPT_URL, self::$image_url . $event->id);
        $content = curl_exec($this->curlConnection);
        $http_code = curl_getinfo($this->curlConnection, CURLINFO_HTTP_CODE);
        echo 'Result: ' . $http_code . "\n";
    }
}
