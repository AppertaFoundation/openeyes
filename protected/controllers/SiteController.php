<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class SiteController extends BaseController
{
    public function accessRules()
    {
        return array(
            // Allow unauthenticated users to view certain pages
            array('allow',
                'actions' => array('error', 'login', 'loginFromOverlay', 'debuginfo'),
            ),
            array('allow',
                'actions' => array('index', 'changeSiteAndFirm', 'search', 'logout'),
                'users' => array('@'),
            ),
        );
    }

    /**
     * Omnibox search form.
     */
    public function actionIndex()
    {
        $search_term = Yii::app()->session['search_term'];
        Yii::app()->session['search_term']='';
        $this->pageTitle = 'Home';
        $this->fixedHotlist = false;
        $this->layout = 'home';
        $this->render('index', array('search_term' => $search_term));
    }

    /**
     * Omnibox search handler.
     */
    public function actionSearch()
    {
        if (isset($_POST['query']) && $query = trim($_POST['query'])) {
                //empty string
            if (strlen($query) == 0) {
                Yii::app()->user->setFlash('warning.search_error', 'Please enter either a hospital number or a firstname and lastname.');
            } else {
                // Event ID
                if (preg_match('/^(E|Event)\s*[:;]\s*([0-9]+)$/i', $query, $matches)) {
                    $event_id = $matches[2];
                    if ($event = Event::model()->findByPk($event_id)) {
                        $event_class_name = $event->eventType->class_name;
                        $this->redirect(array($event_class_name.'/default/view/'.$event_id));
                    } else {
                        Yii::app()->user->setFlash('warning.search_error', 'Event ID not found');
                        $this->redirect('/');
                    }

                    return;
                } else {
                    $patientSearch = new PatientSearch();

                    // lets check if it is a NHS number, Hospital number or Patient name

                    if ($patientSearch->getNHSnumber($query) || $patientSearch->getHospitalNumber($query) || $patientSearch->getPatientName($query)) {
                        $this->redirect(array('patient/search', 'term' => $query));
                    } else {
                        // not a valid search
                        Yii::app()->user->setFlash('warning.search_error', '<strong>"'.CHtml::encode($query).'"</strong> is not a valid search.');
                    }
                }
            }
        }

        $this->redirect('/');
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest) {
                echo htmlspecialchars($error['message']);
            } else {
                $error_code = (int) $error['code'];
                /*
                if ($error_code == 403) {
                    $this->redirect(Yii::app()->createURL('site/index'));
                    Yii::app()->exit();
                }
                */
                if (($view = $this->getViewFile('/error/error'.$error_code)) !== false) {
                    $this->render('/error/error'.$error_code, $error);
                } else {
                    $this->render('/error/error', $error);
                }
            }
        }
    }

    /**
     * Display form to change site/firm.
     *
     * @throws CHttpException
     */
    public function actionChangeSiteAndFirm()
    {
        $return_url = \Yii::app()->request->getParam('returnUrl');
        $this->renderPartial('/site/change_site_and_firm', array('returnUrl' => $return_url), false, true);
    }

    public function authSourceIsSSO()
    {
        return Yii::app()->params['auth_source'] === 'SAML' || Yii::app()->params['auth_source'] === 'OIDC';
    }

    /**
     * Displays the login page.
     */
    public function actionLogin()
    {
        $this->layout = 'home';
        $this->pageTitle = 'Login';

        if (!Yii::app()->user->isGuest) {
            $this->redirect('/');
            Yii::app()->end();
        }

        if (Yii::app()->params['required_user_agent'] && !preg_match(Yii::app()->params['required_user_agent'], @$_SERVER['HTTP_USER_AGENT'])) {
            if (!Yii::app()->params['required_user_agent_message']) {
                throw new Exception('You must define the required_user_agent_message parameter.');
            }

            return $this->render('login_wrong_browser');
        }

        if (isset($_SERVER['HTTP_USER_AGENT']) && (
                strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false
                || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false)) {
            $this->layout = 'unsupported_browser';

            return $this->render('login_unsupported_browser');
        }

        $model = new LoginForm();

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            if (!$this->authSourceIsSSO() || in_array($model->username, Yii::app()->params['local_users'])) {
                // validate user input and redirect to the previous page if valid
                if ($model->validate() && $model->login()) {
                    // Flag site for confirmation
                    Yii::app()->session['confirm_site_and_firm'] = true;
                    Yii::app()->session['shown_version_reminder'] = true;
                    // Check the user has admin role and auto version check enabled
                    $autoVersionEnabled = strpos(strtolower(SettingInstallation::model()->findByAttributes(['key' => "auto_version_check"])->value), 'enable');
                    if (Yii::app()->user->checkAccess('admin') && !($autoVersionEnabled === false)) {
                        $this->doVersionCheck();
                    }
                    $this->redirect(Yii::app()->user->returnUrl);
                }
            }
        }
        if ($this->authSourceIsSSO()) {
            // User signing-in through portal should not be shown default OE login screen
            return $this->render('/sso/sso_login', array('sso_login_url' => Yii::app()->createUrl('sso/login')));
        }

        // display the login form
        $this->render(
            'login',
            array(
                'model' => $model,
            )
        );
    }

    public function actionLoginFromOverlay()
    {
        $model = new LoginForm();

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // Check if its the same user that wants to continue the session
            if (Yii::app()->session['user']->username !== $model->username) {
                $this->renderJSON('Username different');
                return;
            }
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login()) {
                // Flag site for confirmation
                Yii::app()->session['confirm_site_and_firm'] = true;
                $this->renderJSON('Login success');
                return;
            }
        }

        $this->renderJSON('Login failed');
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        $user = Yii::app()->session['user'];

        $user->audit('logout', 'logout');

        OELog::log("User $user->username logged out");

        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionDebuginfo()
    {
        $this->renderPartial('/site/debuginfo', array());
    }

    private function doVersionCheck()
    {
        $ammoniteURL = Yii::App()->params['ammonite_url'];
        $lastCheckDate = \Yii::app()->db->createCommand()->select('last_version_check_date')->from('ammonite')->queryScalar();
        $uuid = \Yii::app()->db->createCommand()->select('uuid')->from('ammonite')->queryScalar();

        if (empty($uuid)) {
            $apiInfo = $this->registerAPI($ammoniteURL);
            if ($apiInfo === "error") {
                return;
            } else {
                Yii::app()->session['shown_version_reminder'] = false;
                $db = Yii::app()->db;
                $db->createCommand()->insert('ammonite', array(
                    'uuid' => $apiInfo->uuid,
                    'last_version_check_date' => null,
                    'first_install_date' => date('Y-m-d H:i:s'),
                ));
            }
        } elseif ($this->versionExpired($lastCheckDate)) {
            $uuid = \Yii::app()->db->createCommand()->select('uuid')->from('ammonite')->queryScalar();
            $this->sendVersionInfo($ammoniteURL, $uuid);
        } else {
            Yii::log("The version has been checked recently");
        }
    }

    private function sendVersionInfo($ammoniteURL, $uuid)
    {
        $updateURL = $ammoniteURL."api/clients/".$uuid."/update";
        if (file_exists('/etc/hostname')) {
            $hostname = trim(file_get_contents('/etc/hostname'));
        } else {
            $hostname = trim(`hostname`);
        }
        $commit = preg_replace('/[\s\t].*$/s', '', @file_get_contents(Yii::app()->basePath . '/../.git/FETCH_HEAD'));
        $thisEnv = 'LIVE';
        if (file_exists('/etc/openeyes/env.conf')) {
            $envvars = parse_ini_file('/etc/openeyes/env.conf');
            if ($envvars['env'] === 'DEV') {
                $thisEnv = 'DEV';
            }
        }

        if ($thisEnv === 'DEV') {
            $branch = "<br/><div style='height:150px; overflow-y:scroll;border:1px solid #000; margin-bottom:10px'>";
            $result = exec('oe-which', $lines);
            foreach ($lines as $line) {
                $branch .= trim(strtr($line, array('[32m' => '', '[39m' => '', '--' => ':'))) . '<br/>';
            }
            $branch .= '</div>';
        } else {
            $ex = explode('/', file_get_contents('.git/HEAD'));
            $branch = array_pop($ex);
        }
        if (!empty(Yii::app()->session['user'])) {
            $user = Yii::app()->session['user'];
        } else {
            $user = User::model()->findByPk(Yii::app()->user->id);
        }

        $firm_id = $this->getApp()->session->get('selected_firm_id');
        $firm = Firm::model()->findByPk($firm_id);
        if (is_object($user)) {
            $username = "$user->username ($user->id)";
            $firm = "$firm->name ($firm->id)";
        } else {
            $username = 'Not logged in';
            $firm = 'Not logged in';
        }

        $ipaddress = '';
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        }

        $user_count = Yii::app()->db->createCommand('select count(id) from user')->queryScalar();
        $event_count = Yii::app()->db->createCommand('select count(id) from event')->queryScalar();
        $patient_count = Yii::app()->db->createCommand('select count(id) from patient')->queryScalar();
        $install_date = Yii::app()->db->createCommand('select first_install_date from ammonite')->queryScalar();
        exec("apachectl -v", $webserver_version);

        $json_array = array(
            "host_info" => array(
                "ip" => htmlspecialchars(@$_SERVER['REMOTE_ADDR']),
                "mariadb_version" => explode(" ", exec("mysql -V"))[5],
                "php_version" => explode(" ", exec("php -version"))[7],
                "os_version" => Yii::App()->params['oe_version'],
                "webserver_version" => explode(" ", $webserver_version[0])[2]
            ),
            "instance_info" => array(
                "user_count" => $user_count,
                "patient_count" => $patient_count,
                "event_count" => $event_count,
                "install_date" => "" . date("Y-m-d\TH:i:s.v\Z", strtotime($install_date))
            ),
            "repo_info" => array(
                "oe_version" => Yii::App()->params['oe_version'],
                "repo_head_commit" => ($commit ?: "no commit"),
                "repo_head_commit_date" => "" . date("Y-m-d\TH:i:s.v\Z", strtotime(exec(" git log -1 --format=%cd " . $commit))),
                "repo_origin" => exec("git remote -v"),
                "branch" => preg_replace("/\n/", "", $branch),
            ),
        );

        $payload = json_encode($json_array);
        $this->sendInfoToAmmonite($payload, $updateURL);
    }

    private function sendInfoToAmmonite($payload, $url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 1,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS =>$payload,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        curl_exec($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if (isset($error_msg)) {
            // log curl error message
            \Yii::log($error_msg);
        } else {
            $db = Yii::app()->db;
            $db->createCommand()->update(
                'ammonite',
                array('last_version_check_date' => date('Y-m-d H:i:s'))
            );
        }
    }

    private function registerAPI($ammoniteURL)
    {
        $url = $ammoniteURL."api/register/";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);
        if (isset($error_msg)) {
            // log curl error message
            \Yii::log($error_msg);
            return "error";
        }
        return json_decode($response);
    }

    private function versionExpired($lastCheckDate)
    {
        // the first time version check after install.
        if (!isset($lastCheckDate)) {
            return true;
        }
        $now = new DateTime();
        $lastCheckDate = new DateTime($lastCheckDate);

        $interval = date_diff($lastCheckDate, $now);
        $dateDiff = $interval->format("%d");
        if ($dateDiff > 7) {
            // if the last check is more than 7 days, do the check again.
            return true;
        } else {
            // if the last check is within 7 days, skip the check.
            return false;
        }
    }

//    Advanced search is not integrated at the moment, but we leave the code here for later
//    public function actionAdvancedSearch()
//    {
//        $this->layout = 'advanced_search';
//        $this->render('advanced_search_core');
//    }
}
