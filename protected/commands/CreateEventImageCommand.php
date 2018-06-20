<?php


class CreateEventImageCommand extends CConsoleCommand
{
    public function getHelp()
    {
        return 'Closes all currently open user activity hotlist items.';
    }

    public function run($args)
    {
        if (isset($args[0])) {
            $event = Event::model()->findByPk($args[0]);
            if ($event === null) {
                throw new InvalidArgumentException('Could not find event with id: "' . $args[0] . '"');
            }

            EventImageController::createImageForEvent($event);

        } else {
            $event_id = Yii::app()->db->createCommand()
                ->select('event.id')
                ->from('event')
                ->leftJoin('event_image', 'event_image.event_id = event.id')
                ->leftJoin('event_image_status', 'event_image_status.id = event_image.status_id')
                ->where('deleted = 0 AND 
                (
                  event_image.id IS NULL OR 
                  (
                    event.last_modified_date > event_image.last_modified_date AND 
                    event_image_status.status IN("NOT_CREATED", "GENERATED")
                  )
                )')
                ->order('event.last_modified_date DESC')
                ->queryScalar();

            if ($event_id) {
                echo 'Creating event image for event ' . $event_id . "\n";

                $login_page = Yii::app()->params['docman_login_url'];
                $username = Yii::app()->params['docman_user'];
                $password = Yii::app()->params['docman_password'];
                $image_url = 'http://localhost/eventImage/create/';
                $inject_autoprint_js = Yii::app()->params['docman_inject_autoprint_js'];

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
                echo 'Curling URL "' . $image_url . $event_id . "\"\n";
                curl_setopt($ch, CURLOPT_URL, $image_url . $event_id);
                $content = curl_exec($ch);

                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                echo 'Result: ' . $http_code . "\n";

                Yii::log($content);

            } else {
                echo 'No out of date events found';
            }
        }
    }
}
