<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * A base controller class that helps display the firm dropdown and the patient name.
 * It is extended by all other controllers.
 */
class BaseController extends Controller
{
    use RenderJsonTrait;
    public $renderPatientPanel = false;
    public bool $fixedHotlist = true;
    public $selectedFirmId;
    public $selectedSiteId;
    public $selectedInstitutionId;
    public $firms;
    public $jsVars = array();
    public $assetManager;

    /**
     * @var CApplication
     */
    protected $app;

    /**
     * @param CApplication $app
     */
    public function setApp(CApplication $app)
    {
        $this->app = $app;
    }

    /**
     * @return CApplication
     */
    public function getApp()
    {
        if (!$this->app) {
            $this->app = Yii::app();
        }

        return $this->app;
    }

    public function filters()
    {
        return array('accessControl');
    }

    public function filterAccessControl($filterChain)
    {
        $rules = $this->accessRules();
        // Fallback to denying everyone
        $rules[] = array('deny');

        $filter = new CAccessControlFilter();
        $filter->setRules($rules);
        $filter->filter($filterChain);
    }

    public function events()
    {
        return [
            'onBeforeAction' => 'beforeAction',
            'onAfterAction' => 'afterAction',
        ];
    }

    /**
     * List of print actions.
     *
     * @return array:
     */
    public function printActions()
    {
        return array();
    }

    /**
     * Override-able render function for sidebar
     *
     * @param $default_view
     */
    public function renderSidebar($default_view)
    {
        $this->renderPartial($default_view);
    }

    /**
     * @param string $action
     *
     * @return bool
     */
    protected function isPrintAction($action)
    {
        return in_array($action, $this->printActions());
    }

    /**
     * Sets up the yii assetManager properties.
     */
    protected function setupAssetManager()
    {
        $assetManager = Yii::app()->assetManager;

        // Set AssetManager properties.
        $assetManager->isPrintRequest = $this->isPrintAction($this->action->id);
        $assetManager->isAjaxRequest = Yii::app()->getRequest()->getIsAjaxRequest();
        if (!isset(Yii::app()->params['tinymce_default_options']['content_css'])) {
            $newblue_path = Yii::getPathOfAlias('application.assets.newblue');
            $print_css_path = $assetManager->getPublishedUrl($newblue_path, true) . '/dist/css/style_oe_print.3.css';
            $newparams =
                array_merge_recursive(
                    Yii::app()->getParams()->toArray(),
                    array('tinymce_default_options' => array('content_css' => $print_css_path))
                );
            Yii::app()->setParams($newparams);
        }
        //FIXME: currently we are resetting the assetmanager list for PDFs because of the TCPDF processing of
        // stylesheets. Ideally we should suppress the inclusion here. (Or we should be using a different approach
        // to render the HTML template for the TCPDF engine)

        // Prevent certain assets from being outputted in certain conditions.
        $assetManager->adjustScriptMapping();
    }

    public function onBeforeAction(\CEvent $event)
    {
        $this->raiseEvent('onBeforeAction', $event);
    }

    public function onAfterAction(\CEvent $event)
    {
        $this->raiseEvent('onAfterAction', $event);
    }

    public function afterAction($action)
    {
        $this->onAfterAction(new \CEvent($this, ["action" => $action]));
    }

    protected function beforeAction($action)
    {
        $this->onBeforeAction(new \CEvent($this, ["action" => $action]));

        $app = Yii::app();
        if(!in_array($action->id, array('settings'))){
            foreach (SettingMetadata::model()->findAll() as $metadata) {
                if (!$metadata->element_type) {
                    if (!isset(Yii::app()->params[$metadata->key])) {
                        Yii::app()->params[$metadata->key] = $metadata->getSetting($metadata->key);
                    }
                }
            }
        }

        $this->setupAssetManager();

        if ($app->params['ab_testing']) {
            if ($app->user->isGuest) {
                $identity = new UserIdentity('admin', '');
                $identity->authenticate('force');
                $app->user->login($identity, 0);
                $this->selectedFirmId = 1;
                $app->session['patient_id'] = 1;
                $app->session['patient_name'] = 'John Smith';
            }
        } else {
            $user_authentication = Yii::app()->session['user_auth'];
            $special_usernames = Yii::app()->params['special_usernames'] ?? [];

            if ($user_authentication && !in_array($user_authentication->username, $special_usernames)) {
                $user = $user_authentication->user;
                // if not a active user, force log out
                if (!$user_authentication->active || PasswordUtils::testStatus('locked', $user_authentication)) {
                    $user->audit('BaseController', 'force-logout', null, "User $user_authentication->username logged out because their account is not active");
                    Yii::app()->user->logout();
                    $this->redirect(Yii::app()->homeUrl);
                }
                PasswordUtils::testPasswordExpiry($user_authentication);

                if (PasswordUtils::testStatus('softlocked', $user_authentication) && $user_authentication->password_softlocked_until < date("Y-m-d H:i:s")) {
                    $user_authentication->password_failed_tries = 0;
                    $user_authentication->password_status = 'current';
                    $user_authentication->saveAttributes(array('password_status', 'password_failed_tries'));
                    $user_authentication->audit('login', 'user-soft-unlock', null, "User: {$user_authentication->username} has finished their softlock period ");
                }

                $whitelistedRequestCheck = $user->CheckRequestOnExpiryWhitelist($_SERVER['REQUEST_URI']);

                // if user is expired, force them to change their password
                if (PasswordUtils::testStatus('expired', $user_authentication) && !$whitelistedRequestCheck) {
                    Yii::app()->user->setFlash('alert', 'Your password has expired, please reset it now.');
                    $this->redirect(array('/profile/password'));
                }
            }
        }

        if (!empty($app->session['selected_firm_id'])) {
            $this->selectedFirmId = $app->session['selected_firm_id'];
        }

        $this->selectedInstitutionId = $app->session['selected_institution_id'];
        if (isset($app->session['selected_site_id'])) {
            $this->selectedSiteId = $app->session['selected_site_id'];
        }

        $this->attachBehavior('DisplayDeletedEventsBehavior', array('class' => 'DisplayDeletedEventsBehavior'));
        return parent::beforeAction($action);
    }

    /**
     * This method is invoked after the view is rendered. We register the assets here
     * as assets might be pre-registered within the views.
     */
    protected function afterRender($view, &$output)
    {
        // Register all assets that we pre-registered.
        if (isset($this->action)) {
            Yii::app()->getAssetManager()->registerFiles($this->isPrintAction($this->action->id));
        }

        $execution_time = CJavaScript::encode(round(Yii::getLogger()->executionTime, 3));
        $memory_usage = round(Yii::getLogger()->memoryUsage / 1024 / 1024, 3) . " MB";
        Yii::app()->getClientScript()->registerScript('scr_' . "execution_time", "execution_time = $execution_time;", CClientScript::POS_HEAD);
        Yii::app()->getClientScript()->registerScript('scr_' . "memory_usage", "memory_usage = '$memory_usage';", CClientScript::POS_HEAD);
    }

    protected function setSessionPatient($patient)
    {
        $app = Yii::app();
        $app->session['patient_id'] = $patient->id;
        $app->session['patient_name'] = $patient->title . ' ' . $patient->first_name . ' ' . $patient->last_name;
    }

    public function storeData()
    {
        $app = Yii::app();

        if (!empty($app->session['selected_firm_id'])) {
            $this->selectedFirmId = $app->session['selected_firm_id'];
        }
    }

    public function logActivity($message)
    {
        $addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';

        Yii::log($message . ' from ' . $addr, 'user', 'userActivity');
    }

    protected function beforeRender($view)
    {
        $this->processJsVars();

        return parent::beforeRender($view);
    }

    public function processJsVars()
    {
        // TODO: Check logged in before setting
        $this->jsVars['element_close_warning_enabled'] = Yii::app()->params['element_close_warning_enabled'];
        if (isset(Yii::app()->session['user_auth'])) {
            $user_auth = Yii::app()->session['user_auth'];
            $user = $user_auth->user;
            $this->jsVars['user_id'] = $user->id;
            $this->jsVars['user_full_name'] = $user->first_name . " " . $user->last_name;
            $this->jsVars['user_email'] = $user->email;
            $this->jsVars['user_username'] = $user_auth->username;
            $institution = Institution::model()->getCurrent();
            $this->jsVars['institution_code'] = $institution->remote_id;
            $this->jsVars['institution_name'] = $institution->name;
        }
        $this->jsVars['YII_CSRF_TOKEN'] = Yii::app()->request->csrfToken;
        $this->jsVars['OE_core_asset_path'] = Yii::app()->assetManager->getPublishedPathOfAlias('application.assets');
        $this->jsVars['OE_module_name'] = $this->module ? $this->module->id : false;
        $this->jsVars['OE_html_autocomplete'] = Yii::app()->params['html_autocomplete'];
        $this->jsVars['OE_event_print_method'] = Yii::app()->params['event_print_method'];
        $this->jsVars['OE_module_class'] = $this->module ? $this->module->id : null;
        $this->jsVars['OE_GP_Setting'] = \SettingMetadata::model()->getSetting('gp_label');
        $this->jsVars['NHSDateFormat'] = Helper::NHS_DATE_FORMAT;
        $this->jsVars['popupMode'] = SettingMetadata::model()->getSetting('patient_overview_popup_mode');
        $this->jsVars['auth_source'] = Yii::app()->params['auth_source'];

        foreach ($this->jsVars as $key => $value) {
            $value = CJavaScript::encode($value);
            Yii::app()->getClientScript()->registerScript('scr_' . $key, "$key = $value;", CClientScript::POS_HEAD);
        }
    }

    /*
     * Convenience function for authorisation checks
     *
     * @param string $operation
     * @param mixed $param, ...
     * @return boolean
     */
    public function checkAccess($operation)
    {
        $params = func_get_args();
        array_shift($params);

        return Yii::app()->user->checkAccess($operation, $params);
    }

    /**
     * Fetch a model instance and throw 404 if not found; optionally create a new instance if pk is empty.
     *
     * @param string $class_name
     * @param scalar $pk
     * @param bool $create
     *
     * @return CActiveRecord|null - will actually be the class of the requested model if it exists.
     *
     * @throws CHttpException
     */
    public function fetchModel($class_name, $pk = null, $create = false)
    {
        if (!$pk && $create) {
            return new $class_name();
        }

        $model = $class_name::model()->findByPk($pk);
        if (!$model) {
            throw new CHttpException(404, "{$class_name} with PK '{$pk}' not found");
        }

        return $model;
    }

    /**
     * Simple abstraction to pull a param from yii config separated by dots.
     * @param $config
     * @param $key
     * @return null
     */
    private function getParamAttribute($config, $key)
    {
        return Helper::elementFinder($key, $config);
    }

    /**
     * @param $key
     * @return bool
     */
    protected function renderOverride($key, $params = array())
    {
        if ($render_config = $this->getParamAttribute(Yii::app()->params, $key)) {
            $api = Yii::app()->moduleAPI->get($render_config['module']);
            return call_user_func_array(array($api, $render_config['method']), $params);
        }
        return false;
    }

    protected function getUniqueCodeForUser()
    {
        $userUniqueCode = UniqueCodeMapping::model()->findByAttributes(array('user_id' => Yii::app()->user->id));
        if ($userUniqueCode) {
            return $userUniqueCode->unique_code_id;
        } else {
            $uniqueCode = $this->createNewUniqueCodeMapping(null, Yii::app()->user->id);
            return $uniqueCode->unique_code_id;
        }
    }

    protected function createNewUniqueCodeMapping($eventId = null, $userId = null)
    {
        $newUniqueCode = UniqueCodeMapping::model();
        $newUniqueCode->lock();
        $newUniqueCode->unique_code_id = $this->getActiveUnusedUniqueCode();
        if ($eventId > 0) {
            $newUniqueCode->event_id = $eventId;
            $newUniqueCode->user_id = null;
        } elseif ($userId > 0) {
            $newUniqueCode->event_id = null;
            $newUniqueCode->user_id = $userId;
        }
        $newUniqueCode->isNewRecord = true;
        $newUniqueCode->save();
        $newUniqueCode->unlock();
        return $newUniqueCode;
    }


    /**
     * Getting the unused active unique codes.
     *
     * @return type
     */
    private function getActiveUnusedUniqueCode()
    {
        UniqueCodeMapping::model()->lock();
        //Yii::app()->db->createCommand("LOCK TABLES unique_codes READ, unique_codes_mapping WRITE")->execute();

        $record = Yii::app()->db->createCommand()
            ->select('unique_codes.id as id')
            ->from('unique_codes')
            ->leftJoin('unique_codes_mapping', 'unique_code_id=unique_codes.id')
            ->where('unique_codes_mapping.id is null')
            ->andWhere('active = 1')
            ->limit(1)
            ->queryRow();

        UniqueCodeMapping::model()->unlock();
        //Yii::app()->db->createCommand("UNLOCK TABLES")->execute();

        if ($record) {
            return $record["id"];
        }
    }

    public function setPageTitle($pageTitle)
    {
        if ((string)SettingMetadata::model()->getSetting('use_short_page_titles') != "on") {
            parent::setPageTitle($pageTitle . ' - OE');
        } else {
            parent::setPageTitle($pageTitle);
        }
    }

    public function sanitizeInput($input)
    {
        $allowable_tags = ["b","strong","p","input","option","select","table","thead","tbody","tr","th","td","i","em","span","br","ul","ol","li","div","col","colgroup","h1","h2","h3","h4","h5"];
        if (count($input) > 0) {
            foreach ($input as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $input[$key] = $this->sanitizeInput($value);
                    continue;
                }
                $pattern = '/<(?:(?!\b' . implode('\b|\b', $allowable_tags) . '\b).)*?>/';
                $value = preg_replace_callback($pattern, function ($matches) {
                    return CHtml::encode($matches[0]);
                }, $value);
                $input[$key] = $value;
            }
        }
        return $input;
    }

    /**
     * Get active Clinic Pathway for the patient
     * @return array|CActiveRecord|mixed|Pathway|null
     */
    public function getClinicPathwayInProgress()
    {
        $pathway = null;
        if ($this->patient && $this->checkAccess('OprnWorklist')) {
            $criteria = new CDbCriteria();
            $criteria->join = 'JOIN worklist_patient wp ON wp.id = t.worklist_patient_id';
            $criteria->addCondition('wp.patient_id = :patient_id');
            $criteria->params = [':patient_id' => $this->patient->id];
            $criteria->addInCondition('t.status', Pathway::inProgressStatuses());
            $pathway = Pathway::model()->find($criteria);
        }
        return $pathway;
    }
}
