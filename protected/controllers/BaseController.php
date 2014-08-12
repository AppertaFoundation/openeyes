<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
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

	public function filters()
	{
		return array('accessControl');
	}

	public function filterAccessControl($filterChain)
	{
		$rules = $this->accessRules();
		// Fallback to denying everyone
		$rules[] = array('deny');

		$filter = new CAccessControlFilter;
		$filter->setRules($rules);
		$filter->filter($filterChain);
	}

	/**
	 * List of print actions.
	 * @return array:
	 */
	public function printActions()
	{
		return array();
	}

	/**
	 * @param string $action
	 * @return boolean
	 */
	protected function isPrintAction($action)
	{
		return in_array($action, $this->printActions());
	}

	/**
	 * Sets up the yii assetManager properties
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

		// Prevent certain assets from being outputted in certain conditions.
		$assetManager->adjustScriptMapping();
	}

	protected function beforeAction($action)
	{
		$app = Yii::app();

		foreach (SettingMetadata::model()->findAll() as $metadata) {
			if (!$metadata->element_type) {
				Yii::app()->params[$metadata->key] = $metadata->getSetting($metadata->key);
			}
		}

		$this->setupAssetManager();

		if ($app->params['ab_testing']) {
			if ($app->user->isGuest) {
				$identity=new UserIdentity('admin', '');
				$identity->authenticate('force');
				$app->user->login($identity,0);
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

		Yii::log($message . ' from ' . $addr, "user", "userActivity");
	}

	protected function beforeRender($view)
	{
		$this->processJsVars();
		return parent::beforeRender($view);
	}

	public function processJsVars()
	{
		$this->jsVars['YII_CSRF_TOKEN'] = Yii::app()->request->csrfToken;
		$this->jsVars['OE_core_asset_path'] = Yii::app()->assetManager->getPublishedPathOfAlias('application.assets');
		$this->jsVars['OE_html_autocomplete'] = Yii::app()->params['html_autocomplete'];

		foreach ($this->jsVars as $key => $value) {
			$value = CJavaScript::encode($value);
			Yii::app()->getClientScript()->registerScript('scr_'.$key, "$key = $value;",CClientScript::POS_HEAD);
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
	 * Fetch a model instance and throw 404 if not found; optionally create a new instance if pk is empty
	 *
	 * @param string $class_name
	 * @param scalar $pk
	 * @param bool $create
	 */
	public function fetchModel($class_name, $pk = null, $create = false)
	{
		if (!$pk && $create) return new $class_name;

		$model = $class_name::model()->findByPk($pk);
		if (!$model) throw new CHttpException(404, "{$class_name} with PK '{$pk}' not found");
		return $model;
	}
}
