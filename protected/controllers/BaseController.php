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
	protected $css = array();

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
	 * (Pre)register a CSS file with a priority to allow ordering
	 * @param string $name
	 * @param string $path
	 * @param integer $priority
	 */
	public function registerCssFile($name, $path, $priority = 100)
	{
		$this->css[$name] = array(
				'path' => $path,
				'priority' => $priority,
		);
	}

	/**
	 * Registers all CSS file that were preregistered by priority
	 */
	protected function registerCssFiles()
	{
		$css_array = array();
		foreach ($this->css as $css_item) {
			$css_array[$css_item['path']] = $css_item['priority'];
		}
		arsort($css_array);
		$clientscript = Yii::app()->clientScript;
		foreach ($css_array as $path => $priority) {
			$clientscript->registerCssFile($path);
		}
	}

	/**
	 * List of actions for which the style.css file should _not_ be included
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

	protected function beforeAction($action)
	{

		$app = Yii::app();

		// Register base style.css unless it's a print action
		if (!$this->isPrintAction($action->id)) {
			$this->registerCssFile('style.css', Yii::app()->createUrl('/css/style.css'), 200);
		}


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

		$this->registerCssFiles();
		$this->adjustScriptMapping();

		return parent::beforeAction($action);
	}

	/**
	 * Adjust the the client script mapping (for javascript and css files assets).
	 *
	 * If a Yii widget is being used in an Ajax request, all dependant scripts and
	 * stylesheets will be outputted in the response. This method ensures the core
	 * scripts and stylesheets are not outputted in an Ajax response.
	 */
	private function adjustScriptMapping() {
		if (Yii::app()->getRequest()->getIsAjaxRequest()) {
			$scriptMap = Yii::app()->clientScript->scriptMap;
			$scriptMap['jquery.js'] = false;
			$scriptMap['jquery.min.js'] = false;
			$scriptMap['jquery-ui.js'] = false;
			$scriptMap['jquery-ui.min.js'] = false;
			$scriptMap['module.js'] = false;
			$scriptMap['style.css'] = false;
			$scriptMap['jquery-ui.css'] = false;
			Yii::app()->clientScript->scriptMap = $scriptMap;
		}
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
}
