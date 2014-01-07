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

class BaseModuleController extends BaseController {

	/* @var string path to asset files for the module */
	public $assetPath;
	/* @var EventType event type for this controller */
	private $_event_type;
	/* @var string css class for the module */
	public $moduleNameCssClass = '';

	/**
	 * Determines the assetPath for the controller from the module
	 */
	public function init()
	{
		// Set asset path
		if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'))) {
			$this->assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'), false, -1, YII_DEBUG);
		}
		return parent::init();
	}

	/**
	 * The EventType class for this module
	 *
	 * @return EventType
	 */
	public function getEvent_type()
	{
		if (!$this->_event_type) {
			$this->_event_type = EventType::model()->find('class_name=?', array($this->getModule()->name));
		}
		return $this->_event_type;
	}

	/**
	 * Sets up various standard js and css files for modules
	 *
	 * @param CAction $action
	 * @return bool
	 * (non-phpdoc)
	 * @see parent::beforeAction($action)
	 */
	protected function beforeAction($action)
	{
		if ($this->event_type->disabled) {
			// disabled module
			$this->redirectToPatientEpisodes();
		}


		// Set the module CSS class name.
		$this->moduleNameCssClass = strtolower(Yii::app()->getController()->module->id);

		// Automatic file inclusion unless it's an ajax call
		if ($this->assetPath && !Yii::app()->getRequest()->getIsAjaxRequest()) {
			if ($this->isPrintAction($action->id)) {
				// Register print css
				if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets.css').'/print.css')) {
					$this->registerCssFile('module-print.css', $this->assetPath.'/css/print.css');
				}

			} else {
				// Register js
				if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets.js').'/module.js')) {
					Yii::app()->clientScript->registerScriptFile($this->assetPath.'/js/module.js');
				}

				if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets.js').'/'.get_class($this).'.js')) {
					Yii::app()->clientScript->registerScriptFile($this->assetPath.'/js/'.get_class($this).'.js');
				}

				// Register css
				if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets.css').'/module.css')) {
					$this->registerCssFile('module.css',$this->assetPath.'/css/module.css',10);
				}

				if (file_exists(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets.css').'/css/'.get_class($this).'.css')) {
					$this->registerCssFile(get_class($this).'.css',$this->assetPath.'/css/'.get_class($this).'.css',10);
				}

			}
		}
		return parent::beforeAction($action);
	}

	/**
	 * Redirect to the patient episodes when the controller determines the action cannot be carried out
	 *
	 */
	protected function redirectToPatientEpisodes()
	{
		$this->redirect(array("/patient/episodes/".$this->patient->id));
	}
}