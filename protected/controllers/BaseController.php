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

    public $renderPatientPanel = false;
    public $selectedFirmId;
    public $selectedSiteId;
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

        //FIXME: currently we are resetting the assetmanager list for PDFs because of the TCPDF processing of
        // stylesheets. Ideally we should suppress the inclusion here. (Or we should be using a different approach
        // to render the HTML template for the TCPDF engine)

        // Register the main stylesheet without pre-registering to ensure it's always output first.
        $assetManager->registerCssFile('css/style.css', null, null, AssetManager::OUTPUT_ALL, false);
        $assetManager->registerCssFile('css/new_ui.css', null, null, AssetManager::OUTPUT_SCREEN, false);

        // Prevent certain assets from being outputted in certain conditions.
        $assetManager->adjustScriptMapping();
    }

    protected function beforeAction($action)
    {
        $app = Yii::app();

        foreach (SettingMetadata::model()->findAll() as $metadata) {
            if (!$metadata->element_type) {
                if (!isset(Yii::app()->params[$metadata->key])) {
                    Yii::app()->params[$metadata->key] = $metadata->getSetting($metadata->key);
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
        }

        if (isset($app->session['firms']) && count($app->session['firms'])) {
            $this->firms = $app->session['firms'];
            $this->selectedFirmId = $app->session['selected_firm_id'];
        }

        if (isset($app->session['selected_site_id'])) {
            $this->selectedSiteId = $app->session['selected_site_id'];
        }

        return parent::beforeAction($action);
    }

    /**
     * This method is invoked after the view is rendered. We register the assets here
     * as assets might be pre-registered within the views.
     */
    protected function afterRender($view, &$output)
    {
        // Register all assets that we pre-registered.
        Yii::app()->getAssetManager()->registerFiles($this->isPrintAction($this->action->id));
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

        if (!empty($app->session['firms'])) {
            $this->firms = $app->session['firms'];
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
        $this->jsVars['uservoice_enabled'] = Yii::app()->params['uservoice_enabled'];
        $this->jsVars['uservoice_use_logged_in_user'] = Yii::app()->params['uservoice_use_logged_in_user'];
        $this->jsVars['uservoice_override_account_id'] = Yii::app()->params['uservoice_override_account_id'];
        $this->jsVars['uservoice_override_account_name'] = Yii::app()->params['uservoice_override_account_name'];
        if (isset(Yii::app()->session['user'])) {
          $user = User::model()->findByAttributes(array('id' => Yii::app()->session['user']->id));
          $this->jsVars['user_id'] = $user->id;
          $this->jsVars['user_full_name'] = $user->first_name." ".$user->last_name;
          $this->jsVars['user_email'] = $user->email;
        }
        $institution = Institution::model()->findByAttributes(array('remote_id' => Yii::app()->params['institution_code']));
        $this->jsVars['institution_code'] = $institution->remote_id;
        $this->jsVars['institution_name'] = $institution->name;
        $this->jsVars['YII_CSRF_TOKEN'] = Yii::app()->request->csrfToken;
        $this->jsVars['OE_core_asset_path'] = Yii::app()->assetManager->getPublishedPathOfAlias('application.assets');
        $this->jsVars['OE_module_name'] = $this->module ? $this->module->id : false;
        $this->jsVars['OE_html_autocomplete'] = Yii::app()->params['html_autocomplete'];
        $this->jsVars['OE_event_print_method'] = Yii::app()->params['event_print_method'];
        $this->jsVars['OE_module_class'] = $this->module ? $this->module->id : null;

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
     * Renders data as JSON, turns off any to screen logging so output isn't broken.
     *
     * @param $data
     */
    protected function renderJSON($data)
    {
        header('Content-type: application/json');
        echo CJSON::encode($data);

        foreach (Yii::app()->log->routes as $route) {
            if ($route instanceof CWebLogRoute) {
                $route->enabled = false; // disable any weblogroutes
            }
        }
        Yii::app()->end();
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
        if($userUniqueCode)
        {
            return $userUniqueCode->unique_code_id;
        }else
        {
            $uniqueCode = $this->createNewUniqueCodeMapping(null, Yii::app()->user->id);
            return $uniqueCode->unique_code_id;
        }
    }

    protected function createNewUniqueCodeMapping($eventId=null, $userId=null)
    {
        $newUniqueCode = UniqueCodeMapping::model();
        $newUniqueCode->lock();
        $newUniqueCode->unique_code_id = $this->getActiveUnusedUniqueCode();
        if($eventId > 0)
        {
            $newUniqueCode->event_id = $eventId;
            $newUniqueCode->user_id = NULL;
        }elseif($userId > 0)
        {
            $newUniqueCode->event_id = NULL;
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

        if($record){
            return $record["id"];
        }

    }

}
