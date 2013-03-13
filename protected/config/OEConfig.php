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

class OEConfig {
	
	/**
	 * Loads config files for current environment and returns a merged array
	 *
	 * Config load order is:
	 * - core/common
	 * - core/environment
	 * - module1/common
	 * - module1/environment
	 * - module2/common
	 * - module2/environment
	 * - ...
	 * - local/common
	 * - local/environment
	 *
	 * So local configs will override earlier ones.
	 *
	 * Core and local configs are pre processed in order to discover which modules to load.
	 *
	 * @param string $environment
	 */
	public static function getMergedConfig($environment) {
		$configs = array();
		$config_path = dirname(__FILE__) . '/';

		// Get core and local configs and extract active modules
		$active_modules = array();
		foreach(array('core','local') as $config_level) {
			$configs[$config_level][] = require $config_path.$config_level.'/common.php';
			if(file_exists($config_path.$config_level."/$environment.php")) {
				$configs[$config_level][] = include $config_path.$config_level."/$environment.php";
			}
			foreach($configs[$config_level] as $config) {
				if(isset($config['modules'])) {
					$active_modules = CMap::mergeArray($active_modules, $config['modules']);
				}
			}
		}

		// Get module configs
		$modules_path = dirname(__FILE__) . "/../modules/";
		$processed_modules = array();
		foreach($active_modules as $module_key => $module_name) {
			if(is_array($module_name)) {
				$module_name = $module_key;
			}
			if(!in_array($module_name, $processed_modules)) {
				$processed_modules[] = $module_name;
				
				// Import event type module's models folder
				// FIXME: We need a better way of handling this
				if(substr($module_name, 0, 3) == 'Oph') {
					$configs['modules'][] = array(
							'import' => array(
									'application.modules.' . $module_name .'.models.*',
							),
					);
				}
				
				if(file_exists($modules_path . $module_name . "/config/common.php")) {
					$configs['modules'][] = include $modules_path . $module_name . "/config/common.php";
				}
				if(file_exists($modules_path . $module_name . "/config/$environment.php")) {
					$configs['modules'][] = include $modules_path . $module_name . "/config/$environment.php";
				}
			}
		}

		// Merge configs
		$merged_config = array(
				'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
		);
		foreach(array('core','modules','local') as $config_group) {
			if(isset($configs[$config_group])) {
				foreach($configs[$config_group] as $config) {
					$merged_config = CMap::mergeArray($merged_config, $config);
				}
			}
		}

		return $merged_config;
	}
	
}