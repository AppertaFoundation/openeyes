<?php

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
function getConfig($environment) {
	$configs = array();
	$config_path = dirname(__FILE__) . '/../config/';

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
	$modules_path = dirname(__FILE__) . "/.." . Yii::app()->getModulePath() . "/";
	foreach(array_unique($active_modules) as $module_key => $module_name) {
		if(is_array($module_name)) {
			$module_name = $module_key;
		}
		if(file_exists($modules_path . $module_name . "/config/common.php")) {
			$configs['modules'][] = include $modules_path . $module_name . "/config/common.php";
		}
		if(file_exists($modules_path . $module_name . "/config/$environment.php")) {
			$configs['modules'][] = include $modules_path . $module_name . "/config/$environment.php";
		}
	}

	// Merge configs
	$merged_config = array(
			'basePath' => realpath(dirname(__FILE__).'/..'),
	);
	foreach(array('core','modules','local') as $config_group) {
		foreach($configs[$config_group] as $config) {
			$merged_config = CMap::mergeArray($merged_config, $config);
		}
	}

	return $merged_config;
}
