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
 * This is a custom AssetManager class that provides convenient features for
 * managing assets. Using this class will also ensure that assets are published correctly.
 * You can access this class instance via: Yii::app()->getAssetManager().
 *
 * @example
 * $assetManager = Yii::app()->getAssetManager();
 *
 * // Pre-register a base application stylesheet for all requests:
 * $assetManager->registerCssFile('css/style.css');
 *
 * // Pre-register a base application stylesheet with a low priority:
 * $assetManager->registerCssFile('css/style.css', null, 10);
 *
 * // Pre-register a base application stylesheet for only screen, meaning it will not
 * // be outputted for print nor AJAX requests:
 * $assetManager->registerCssFile('css/style.css', null, null, $assetManager::OUTPUT_SCREEN);
 *
 * // Pre-register a base application stylesheet for only print requests:
 * $assetManager->registerCssFile('css/style.css', null, null, $assetManager::OUTPUT_PRINT);
 *
 * // Pre-register a base application script:
 * $assetManager->registerScriptFile('css/script.js');
 *
 * // Pre-register a module stylesheet:
 * $path = $assetManager->getPath('application.modules.mymodule.assets');
 * $assetManager->registerCssFile('css/module.css', $path);
 *
 * // Once assets have been pre-registered, you have to register the files.
 * $assetManager->registerFiles(false);
 *
 * // Once the assets have been registered, you can adjust the script mapping
 * // to prevent certain assets from being outputted in certain situations.
 * $assetManager->adjustScriptMapping();
 */
class AssetManager extends CAssetManager
{
	const OUTPUT_PRINT = 'print';
	const OUTPUT_SCREEN = 'screen';
	const OUTPUT_ALL = 'all';
	const OUTPUT_AJAX = 'ajax';

	protected $css = array();
	protected $js = array();
	protected $cssPriority = 200;
	protected $jsPriority = 200;
	protected $basePath;
	protected $clientScript;
	protected $isPrintRequest = false;
	protected $isAjaxRequest = false;

	/**
	 * Initializes the component.
	 */
	public function init()
	{
		$this->basePath = $this->getPath('application.assets');
		$this->clientScript = Yii::app()->clientScript;
		$this->isAjaxRequest = Yii::app()->getRequest()->getIsAjaxRequest();
		parent::init();
	}

	/**
	 * Returns the published asset path for a given asset directory alias.
	 * @param  string $alias The alias to the assets.
	 * @return string        The publish assets path.
	 */
	public function getPath($alias = null)
	{
		return $this->publish(Yii::getPathOfAlias($alias), false, -1);
	}

	/**
	 * Register a core style.
	 * @param  string $style The core style string to be registered. Eg:
	 * 'dir/file.css'
	 */
	public function registerCoreCssFile($style = '')
	{
		$this->registerCssFile($this->clientScript->getCoreScriptUrl().'/'.$style, $this->cssPriority--);
	}

	/**
	 * Register an application style.
	 * @param  string         $style    The style path. Eg: 'css/style.css'
	 * @param  null|string    $basePath The basepath for the asset.
	 * @param  null|integer   $priority The priority for the asset. Higher priority
	 * styles will be outputted in the page first.
	 */
	public function registerCssFile($style = '', $basePath = null, $priority = null, $output = self::OUTPUT_ALL)
	{
		$basePath = $basePath ?: $this->basePath;
		$priority = $priority !== null ? $priority : $this->cssPriority--;
		$this->addOrderedCssFile($basePath.Yii::app()->createUrl($style), $priority, $output);
	}

	/**
	 * Register a core (framework) script.
	 * @param  string $script The core script to be registered. Eg: 'jquery'
	 */
	public function registerCoreScriptFile($script = '')
	{
		$this->clientScript->registerCoreScript($script);
	}

	/**
	 * Register an application script.
	 * @param  string $script   The script path. Eg: 'js/script.js'
	 * @param  [type] $basePath The asset basepath.
	 * @param  [type] $priority The priority for the asset. Higher priority
	 * scripts will be outputted in the page first.
	 */
	public function registerScriptFile($script = '', $basePath = null, $priority = null, $output = self::OUTPUT_ALL)
	{
		$basePath = $basePath ?: $this->basePath;
		$priority = $priority !== null ? $priority : $this->jsPriority--;
		$this->addOrderedScriptFile($basePath.Yii::app()->createUrl($script), $priority, $output);
	}

	/**
	 * (Pre)register a CSS file with a priority to allow ordering.
	 * @param string $name
	 * @param string $path
	 * @param integer $priority
	 */
	public function addOrderedCssFile($path, $priority = 200, $output = self::OUTPUT_ALL)
	{
		$this->css[$path] = array(
			'priority' => $priority,
			'output' => $output
		);
	}

	/**
	 * (Pre)register a JS file with a priority to allow ordering.
	 * @param string $name
	 * @param string $path
	 * @param integer $priority
	 */
	public function addOrderedScriptFile($path, $priority = 200, $output = self::OUTPUT_ALL)
	{
		$this->js[$path] = array(
			'priority' => $priority,
			'output' => $output
		);
	}

	/**
	 * Registers all CSS files that were preregistered by priority.
	 */
	protected function registerOrderedCssFiles()
	{
		$this->sort($this->css);
		foreach ($this->css as $path => $details) {
			if ($this->canOutput($details['output'])) {
				$this->clientScript->registerCssFile($path);
			}
		}
	}

	/**
	 * Registers all JS files that were preregistered by priority.
	 */
	protected function registerOrderedScriptFiles()
	{
		$this->sort($this->js);
		foreach ($this->js as $path => $details) {
			if ($this->canOutput($details['output'])) {
				$this->clientScript->registerScriptFile($path);
			}
		}
	}

	/**
	 * Sorts an array by priority.
	 * @param  array  $arr The array to sort.
	 */
	protected function sort(array &$arr)
	{
		uasort($arr, function($a, $b) {
			return $b['priority'] - $a['priority'];
		});
	}

	/**
	 * Adjust the the client script mapping (for javascript and css files assets).
	 *
	 * If a Yii widget is being used in an Ajax request, all dependent scripts and
	 * stylesheets will be outputted in the response. This method ensures the core
	 * scripts and stylesheets are not outputted in an Ajax response.
	 */
	public function adjustScriptMapping() {
		if ($this->isAjaxRequest) {
			$scriptMap = $this->clientScript->scriptMap;
			$scriptMap['jquery.js'] = false;
			$scriptMap['jquery.min.js'] = false;
			$scriptMap['jquery-ui.js'] = false;
			$scriptMap['jquery-ui.min.js'] = false;
			$scriptMap['module.js'] = false;
			$scriptMap['style.css'] = false;
			$scriptMap['jquery-ui.css'] = false;
			$this->clientScript->scriptMap = $scriptMap;
		}
	}

	/**
	 * Determines whether an asset should be output for the current request.
	 * @param  OUTPUT_PRINT|OUTPUT_SCREEN|OUTPUT_AJAX|OUTPUT_ALL $output The output type.
	 * @return boolean        Whether the asset should be output.
	 */
	protected function canOutput($output = null)
	{
		if ($output === null) {
			return false;
		}
		if ($this->isPrintRequest) {
			return in_array($output, array(self::OUTPUT_ALL, self::OUTPUT_PRINT));
		}
		if ($this->isAjaxRequest) {
			return in_array($ouput, array(self::OUTPUT_ALL, self::OUTPUT_AJAX));
		}
		return in_array($output, array(self::OUTPUT_ALL, self::OUTPUT_SCREEN));
	}

	/**
	 * Register all assets that were preregistered by priority. This method is
	 * required to output the assets in the page.
	 */
	public function registerFiles($isPrintRequest = false)
	{
		$this->isPrintRequest = $isPrintRequest;
		$this->registerOrderedCssFiles();
		$this->registerOrderedScriptFiles();
	}
}
