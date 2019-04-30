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
                'actions' => array('error', 'login', 'debuginfo'),
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
                echo $error['message'];
            } else {
                $error_code = (int) $error['code'];
                /*
                if ($error_code == 403) {
                    $this->redirect(Yii::app()->baseUrl.'/');
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
        if (!$return_url = @$_GET['returnUrl']) {
            if($_POST['returnUrl'] && Yii::app()->session['redirectToStep']){
                $return_url = str_replace("view", "step", $_POST['returnUrl']);
                unset(Yii::app()->session['redirectToStep']);
            } else if (!$return_url = @$_POST['returnUrl']) {
                throw new CHttpException(500, 'Return URL must be specified');
            }
        }
        if (@$_GET['patient_id']) {
            $patient = Patient::model()->findByPk(@$_GET['patient_id']);
        }
        $this->renderPartial('/site/change_site_and_firm', array('returnUrl' => $return_url), false, true);
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
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login()) {

                // Flag site for confirmation
                Yii::app()->session['confirm_site_and_firm'] = true;

                $this->redirect(Yii::app()->user->returnUrl);
            }
        }

        $institution = Institution::model()->getCurrent();

        $criteria = new CDbCriteria();
        $criteria->compare('institution_id', $institution->id);
        $criteria->order = 'short_name asc';

        // display the login form
        $this->render('login',
            array(
                'model' => $model,
            )
        );
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


//    Advanced search is not integrated at the moment, but we leave the code here for later
//    public function actionAdvancedSearch()
//    {
//        $this->layout = 'advanced_search';
//        $this->render('advanced_search_core');
//    }
}
