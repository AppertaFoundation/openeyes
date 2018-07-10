<?php


class EventImageCommand extends CConsoleCommand
{
    public function getHelp()
    {
        return 'Creates the preview image for the given event ID, or creates the preview image for the latest event without an image if no event ID is specified';
    }

    public function actionCreate($args)
    {
        $count = isset($args[0]) ? (int)$args[0] : 0;
        return $this->createEventImages($count);
    }

    public function openCurlConnection()
    {
        $login_page = Yii::app()->params['docman_login_url'];
        $username = Yii::app()->params['docman_user'];
        $password = Yii::app()->params['docman_password'];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $login_page);
        // disable SSL certificate check for locally issued certificates
        if (Yii::app()->params['disable_ssl_certificate_check']) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookie.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            die(curl_error($ch));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_POST, true);

        $params = array(
            'LoginForm[username]' => $username,
            'LoginForm[password]' => $password,
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        curl_exec($ch);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, false);

        return $ch;
    }

    public function createEventImages($imageCount)
    {
        $image_url = 'http://localhost/eventImage/create/';

        $ch = $this->openCurlConnection();
        for ($i = 0; $i < $imageCount; ++$i) {
            $event = EventImage::model()->getNextEventToImage();
            if ($event === null) {
                echo "No out of date events found\n";

                return;
            }

            echo 'Curling URL "' . $image_url . $event->id . "\"\n";
            curl_setopt($ch, CURLOPT_URL, $image_url . $event->id);
            $content = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            echo 'Result: ' . $http_code . "\n";
        }

        curl_close($ch);
    }
}
