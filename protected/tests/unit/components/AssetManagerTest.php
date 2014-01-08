<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class CustomBasePathAliasAssetManager extends AssetManager {
	const BASE_PATH_ALIAS = 'application.tests.fixtures.assets';
}

class AssetManagerTest extends PHPUnit_Framework_TestCase
{
	const BASE_PATH_ALIAS ='application.tests.assets';
	const BASE_URL = 'assets';

	public function setUp()
	{
		$this->globalInstance = Yii::app()->assetManager;

		// As YII uses $_SERVER['SCRIPT_FILENAME'] to build the base asset path, things will
		// certainly break when the unit tests are run, so we have to explicitly set the basepath
		// to something that is valid.
		$this->globalInstance->setBasePath(Yii::getPathOfAlias(self::BASE_PATH_ALIAS));
		$this->globalInstance->setBaseUrl(self::BASE_URL);
	}

	private static function getInstance()
	{
		$instance = new CustomBasePathAliasAssetManager();
		$instance->init();
		$instance->setBasePath(Yii::getPathOfAlias(self::BASE_PATH_ALIAS));
		$instance->setBaseUrl(self::BASE_URL);
		$instance->isAjaxRequest = false;
		return $instance;
	}

	public function testInstanceCreated()
	{
		$this->assertTrue($this->globalInstance instanceof CAssetManager);
		$this->assertTrue($this->globalInstance instanceof AssetManager);
		$this->assertTrue(PHPUnit_Framework_Assert::readAttribute($this->globalInstance, 'cacheBuster') instanceof CacheBuster);
		$this->assertTrue(PHPUnit_Framework_Assert::readAttribute($this->globalInstance, 'clientScript') instanceof ClientScript);
	}

	public function testGetPublishedPathOfAlias()
	{
		$instance = self::getInstance();

		// Test the published path matches the expected published path.
		$alias = CustomBasePathAliasAssetManager::BASE_PATH_ALIAS;
		$publishedPath = $instance->getPublishedPathOfAlias($alias);
		$expectedPublishedPath = $instance->publish(Yii::getPathOfAlias($alias));
		$this->assertEquals($publishedPath, $expectedPublishedPath);

		// Test the published path matches the expected published path *when no alias is specified*.
		$publishedPath = $instance->getPublishedPathOfAlias();
		$expectedPublishedPath = $instance->publish(Yii::getPathOfAlias($alias));
		$this->assertEquals($publishedPath, $expectedPublishedPath);
	}

	public function testCreateUrl()
	{
		$instance = self::getInstance();
		$alias = CustomBasePathAliasAssetManager::BASE_PATH_ALIAS;
		$path = 'img/cat.gif';

		// Test the url matches when specifying a path alias.
		$url = $instance->createUrl($path, $alias, false);
		$expectedUrl = Yii::app()->createUrl($instance->getPublishedPathOfAlias($alias).'/'.$path);
		$this->assertEquals($url, $expectedUrl);

		// Test the url matches when *when no alias is specified*.
		$url = $instance->createUrl($path, null, false);
		$this->assertEquals($url, $expectedUrl);

		// Test the url matches when an alias path is prevented from being preprended to the path.
		$url = $instance->createUrl($path, false, false);
		$expectedUrl = Yii::app()->createUrl($path);
		$this->assertEquals($url, $expectedUrl);

		// Test a cache buster string is appended to url.
		$path1 = $path;
		$path2 = $path1.'?cats=rule';

		$url1 = $instance->createUrl($path1, false, true);
		$url2 = $instance->createUrl($path2, false, true);

		$expectedUrl1 = Yii::app()->cacheBuster->createUrl(Yii::app()->createUrl($path));
		$expectedUrl2 = Yii::app()->cacheBuster->createUrl(Yii::app()->createUrl($path2));

		$this->assertEquals($url1, $expectedUrl1);
		$this->assertEquals($url2, $expectedUrl2);

		// Test default params.
		$url = $instance->createUrl($path);
		$expectedUrl = Yii::app()->cacheBuster->createUrl(Yii::app()->createUrl($instance->getPublishedPathOfAlias($alias).'/'.$path));
		$this->assertEquals($url, $expectedUrl);
	}

	public function testGetPublishedPath()
	{
		$instance = self::getInstance();
		$alias = CustomBasePathAliasAssetManager::BASE_PATH_ALIAS;
		$path = 'img/cat.gif';

		// Test with specific params.
		$publishedPath = $instance->getPublishedPath($path, $alias);
		$expectedParts = array(
			Yii::getPathOfAlias('webroot'),
			$instance->publish(Yii::getPathOfAlias($alias), false, -1),
			$path
		);
		$expectedPath = implode(DIRECTORY_SEPARATOR, $expectedParts);
		$this->assertEquals($publishedPath, $expectedPath);

		// Test with default params.
		$publishedPath = $instance->getPublishedPath($path);
		$expectedParts = array(
			Yii::getPathOfAlias('webroot'),
			$instance->publish(Yii::getPathOfAlias($alias), false, -1),
			$path
		);
		$expectedPath = implode(DIRECTORY_SEPARATOR, $expectedParts);
		$this->assertEquals($publishedPath, $expectedPath);
	}

	public function testRegisterCoreCssFile()
	{
		$assetManager = $this->getMockBuilder('CustomBasePathAliasAssetManager')
			->setMethods(array('registerCssFile'))
			->getMock();

		$assetManager->init();
		$assetManager->setBasePath(Yii::getPathOfAlias(self::BASE_PATH_ALIAS));
		$assetManager->setBaseUrl(self::BASE_URL);

		// Calling registerCoreCssFile with only the path specified.
		$assetManager->expects($this->at(0))
			->method('registerCssFile')
			->with(
				Yii::app()->clientScript->getCoreScriptUrl().'/test.css',
				false,
				null,
				AssetManager::OUTPUT_ALL,
				true
			);

		// Calling registerCoreCssFile with path and priority
		$assetManager->expects($this->at(1))
			->method('registerCssFile')
			->with(
				Yii::app()->clientScript->getCoreScriptUrl().'/test.css',
				false,
				10,
				AssetManager::OUTPUT_ALL,
				true
			);

		// Calling registerCoreCssFile with path, priority and output
		$assetManager->expects($this->at(2))
			->method('registerCssFile')
			->with(
				Yii::app()->clientScript->getCoreScriptUrl().'/test.css',
				false,
				10,
				AssetManager::OUTPUT_PRINT,
				true
			);

		// Calling registerCoreCssFile with path, priority, output and preRegister
		$assetManager->expects($this->at(3))
			->method('registerCssFile')
			->with(
				Yii::app()->clientScript->getCoreScriptUrl().'/test.css',
				false,
				10,
				AssetManager::OUTPUT_PRINT,
				false
			);

		$assetManager->registerCoreCssFile('test.css');
		$assetManager->registerCoreCssFile('test.css', 10);
		$assetManager->registerCoreCssFile('test.css', 10, AssetManager::OUTPUT_PRINT);
		$assetManager->registerCoreCssFile('test.css', 10, AssetManager::OUTPUT_PRINT, false);
	}

	public function testRegisterCoreScript()
	{
		// As this method is simply a proxy to clientscript we just need to
		// test if the clientScript->registerCoreScript is called with the correct
		// params.

		$instance = self::getInstance();

		$clientScript = $this->getMockBuilder('ClientScript')
			->setMethods(array('registerCoreScript'))
			->getMock();

		$instance->setClientScript($clientScript);

		$clientScript->expects($this->at(0))
			->method('registerCoreScript')
			->with('script');

		$instance->registerCoreScript('script');
	}

	// eg:
	// runTestAddingFiles('css')
	// runTestAddingFiles('js')
	private function runTestAddingFiles($type = null)
	{
		$instance = self::getInstance();

		$registerMethod = $type === 'css' ? 'registerCssFile' : 'registerScriptFile';

		$instance->{$registerMethod}("{$type}/file.{$type}");
		$instance->{$registerMethod}("{$type}/file1.{$type}");

		$publishedPath = $instance->getPublishedPathOfAlias();
		$files = PHPUnit_Framework_Assert::readAttribute($instance, $type);
		$priority = PHPUnit_Framework_Assert::readAttribute(new AssetManager(), $type.'Priority');

		/* Test the files were added to the relevant rray. */
		$keys = array_keys($files);

		$this->assertTrue(strpos($keys[0], "{$publishedPath}/{$type}/file.{$type}") !== FALSE);
		$this->assertTrue(strpos($keys[1], "{$publishedPath}/{$type}/file1.{$type}") !== FALSE);

		/* Test the files array contains the correct values.*/
		$first = array_shift($files);
		$second = array_shift($files);

		$this->assertTrue(isset($first['priority']));
		$this->assertTrue(isset($first['output']));

		$this->assertEquals($first['priority'], $priority);
		$this->assertEquals($second['priority'], $priority-1);

		/* Test with custom alias path.*/
		$alias = CustomBasePathAliasAssetManager::BASE_PATH_ALIAS.'.'.$type;
		$publishedPath = $instance->getPublishedPathOfAlias($alias);
		$instance->{$registerMethod}("file.{$type}", $alias);
		$files = PHPUnit_Framework_Assert::readAttribute($instance, $type);
		$keys = array_keys($files);
		$this->assertTrue(strpos($keys[2], $publishedPath.'/file.'.$type) !== FALSE);

		// /* Test with custom priority.*/
		$instance->{$registerMethod}("{$type}/file2.{$type}", null, 10);
		$files = PHPUnit_Framework_Assert::readAttribute($instance, $type);
		$last = array_pop($files);
		$this->assertEquals($last['priority'], 10);

		// /* Test with custom output.*/
		$instance->{$registerMethod}("{$type}/file3.{$type}", null, null, AssetManager::OUTPUT_PRINT);
		$files = PHPUnit_Framework_Assert::readAttribute($instance, $type);
		$last = array_pop($files);
		$this->assertEquals($last['output'], AssetManager::OUTPUT_PRINT);

		/* Test that assets are registered immediately.*/

		// First, we create a mock for the clientScript to check if the correct
		// method is called with the correct params.
		$clientScript = $this->getMockBuilder('ClientScript')
			->setMethods(array($registerMethod))
			->getMock();

		$clientScript->expects($this->at(0))
			->method($registerMethod)
			->with($instance->createUrl("{$type}/file4.{$type}"));

		$instance->setClientScript($clientScript);

		$instance->{$registerMethod}("{$type}/file4.{$type}", null, null, AssetManager::OUTPUT_ALL, false);

		$publishedPath = $instance->getPublishedPathOfAlias();
		$files = PHPUnit_Framework_Assert::readAttribute($instance, $type);
		$keys = array_keys($files);
		$lastKey = array_pop($keys);

		$this->assertFalse(strpos($lastKey, $publishedPath.'/file4.'.$type));
	}

	public function testRegisterCssFile()
	{
		$this->runTestAddingFiles('css');
	}

	public function testRegisterScriptFile()
	{
		$this->runTestAddingFiles('js');
	}

	public function testAdjustScriptMapping()
	{
		$instance = self::getInstance();

		// First we test that no assets are adjusted if the request is *not*
		// an AJAX request.
		$instance->isAjaxRequest = false;
		$instance->adjustScriptMapping();
		$this->assertEquals(Yii::app()->clientScript->scriptMap, array());

		// Now we test that assets are adjusted if the request is an AJAX request.
		$instance->isAjaxRequest = true;
		$instance->adjustScriptMapping();

		$expectedMapping = array(
			'jquery.js' => false,
			'jquery.min.js' => false,
			'jquery-ui.js' => false,
			'jquery-ui.min.js' => false,
			'module.js' => false,
			'style.css' => false,
			'jquery-ui.css' => false
		);

		$this->assertEquals(Yii::app()->clientScript->scriptMap, $expectedMapping);
	}

	public function testRegisterFiles()
	{
		/* Test that files are registered with clientScript */

		$instance = self::getInstance();

		$clientScript = $this->getMockBuilder('ClientScript')
			->setMethods(array('registerCssFile', 'registerScriptFile'))
			->getMock();

		$instance->setClientScript($clientScript);

		$clientScript->expects($this->any())
			->method('registerCssFile')
			->with($instance->createUrl('css/style.css'));

		$clientScript->expects($this->any())
			->method('registerScriptFile')
			->with($instance->createUrl('js/script.js'));

		$instance->registerCssFile('css/style.css');
		$instance->registerScriptFile('js/script.js');
		$instance->registerFiles();

		/* Test ordering of pre-registered files */

		$instance = self::getInstance();

		$clientScript = $this->getMockBuilder('ClientScript')
			->setMethods(array('registerCssFile', 'registerScriptFile'))
			->getMock();

		$instance->setClientScript($clientScript);

		$clientScript->expects($this->at(0))
			->method('registerCssFile')
			->with($instance->createUrl('css/style2.css'));

		$clientScript->expects($this->at(1))
			->method('registerCssFile')
			->with($instance->createUrl('css/style1.css'));

		$clientScript->expects($this->at(2))
			->method('registerScriptFile')
			->with($instance->createUrl('js/script2.js'));

		$clientScript->expects($this->at(3))
			->method('registerScriptFile')
			->with($instance->createUrl('js/script1.js'));

		$instance->registerScriptFile('js/script1.js', null, 10);
		$instance->registerCssFile('css/style1.css', null, 10);
		$instance->registerScriptFile('js/script2.js', null, 20);
		$instance->registerCssFile('css/style2.css', null, 20);

		$instance->registerFiles();

		/* Test outputting assets depending on type of request */

		/* Neither print nor ajax request */

		$instance = self::getInstance();

		$clientScript = $this->getMockBuilder('ClientScript')
			->setMethods(array('registerCssFile', 'registerScriptFile'))
			->getMock();

		$clientScript->expects($this->at(0))
			->method('registerCssFile')
			->with($instance->createUrl('css/all.css'));

		$clientScript->expects($this->at(1))
			->method('registerCssFile')
			->with($instance->createUrl('css/style.css'));

		$clientScript->expects($this->exactly(2))
				->method('registerCssFile');

		$clientScript->expects($this->at(2))
			->method('registerScriptFile')
			->with($instance->createUrl('js/all.js'));

		$clientScript->expects($this->at(3))
			->method('registerScriptFile')
			->with($instance->createUrl('js/style.js'));

		$clientScript->expects($this->exactly(2))
				->method('registerScriptFile');

		$instance->setClientScript($clientScript);
		$instance->registerCssFile('css/all.css', null, null, AssetManager::OUTPUT_ALL);
		$instance->registerCssFile('css/ajax.css', null, null, AssetManager::OUTPUT_AJAX);
		$instance->registerCssFile('css/style.css', null, null, AssetManager::OUTPUT_SCREEN);
		$instance->registerCssFile('css/print.css', null, null, AssetManager::OUTPUT_PRINT);
		$instance->registerScriptFile('js/all.js', null, null, AssetManager::OUTPUT_ALL);
		$instance->registerScriptFile('js/ajax.js', null, null, AssetManager::OUTPUT_AJAX);
		$instance->registerScriptFile('js/style.js', null, null, AssetManager::OUTPUT_SCREEN);
		$instance->registerScriptFile('js/print.js', null, null, AssetManager::OUTPUT_PRINT);
		$instance->registerFiles();

		/* Print request */

		$instance = self::getInstance();

		$clientScript = $this->getMockBuilder('ClientScript')
			->setMethods(array('registerCssFile', 'registerScriptFile'))
			->getMock();

		$clientScript->expects($this->at(0))
			->method('registerCssFile')
			->with($instance->createUrl('css/all.css'));

		$clientScript->expects($this->at(1))
			->method('registerCssFile')
			->with($instance->createUrl('css/print.css'));

		$clientScript->expects($this->exactly(2))
				->method('registerCssFile');

		$clientScript->expects($this->at(2))
			->method('registerScriptFile')
			->with($instance->createUrl('js/all.js'));

		$clientScript->expects($this->at(3))
			->method('registerScriptFile')
			->with($instance->createUrl('js/print.js'));

		$clientScript->expects($this->exactly(2))
				->method('registerScriptFile');

		$instance->setClientScript($clientScript);
		$instance->isPrintRequest = true;
		$instance->registerCssFile('css/all.css', null, null, AssetManager::OUTPUT_ALL);
		$instance->registerCssFile('css/ajax.css', null, null, AssetManager::OUTPUT_AJAX);
		$instance->registerCssFile('css/style.css', null, null, AssetManager::OUTPUT_SCREEN);
		$instance->registerCssFile('css/print.css', null, null, AssetManager::OUTPUT_PRINT);
		$instance->registerScriptFile('js/all.js', null, null, AssetManager::OUTPUT_ALL);
		$instance->registerScriptFile('js/ajax.js', null, null, AssetManager::OUTPUT_AJAX);
		$instance->registerScriptFile('js/style.js', null, null, AssetManager::OUTPUT_SCREEN);
		$instance->registerScriptFile('js/print.js', null, null, AssetManager::OUTPUT_PRINT);
		$instance->registerFiles();

		/* AJAX request */

		$instance = self::getInstance();

		$clientScript = $this->getMockBuilder('ClientScript')
			->setMethods(array('registerCssFile', 'registerScriptFile'))
			->getMock();

		$clientScript->expects($this->at(0))
			->method('registerCssFile')
			->with($instance->createUrl('css/all.css'));

		$clientScript->expects($this->at(1))
			->method('registerCssFile')
			->with($instance->createUrl('css/ajax.css'));

		$clientScript->expects($this->exactly(2))
				->method('registerCssFile');

		$clientScript->expects($this->at(2))
			->method('registerScriptFile')
			->with($instance->createUrl('js/all.js'));

		$clientScript->expects($this->at(3))
			->method('registerScriptFile')
			->with($instance->createUrl('js/ajax.js'));

		$clientScript->expects($this->exactly(2))
				->method('registerScriptFile');

		$instance->setClientScript($clientScript);
		$instance->isAjaxRequest = true;
		$instance->registerCssFile('css/all.css', null, null, AssetManager::OUTPUT_ALL);
		$instance->registerCssFile('css/ajax.css', null, null, AssetManager::OUTPUT_AJAX);
		$instance->registerCssFile('css/style.css', null, null, AssetManager::OUTPUT_SCREEN);
		$instance->registerCssFile('css/print.css', null, null, AssetManager::OUTPUT_PRINT);
		$instance->registerScriptFile('js/all.js', null, null, AssetManager::OUTPUT_ALL);
		$instance->registerScriptFile('js/ajax.js', null, null, AssetManager::OUTPUT_AJAX);
		$instance->registerScriptFile('js/style.js', null, null, AssetManager::OUTPUT_SCREEN);
		$instance->registerScriptFile('js/print.js', null, null, AssetManager::OUTPUT_PRINT);
		$instance->registerFiles();
	}

	public function testReset()
	{
		$instance = self::getInstance();

		$clientScript = $this->getMockBuilder('ClientScript')
			->setMethods(array('reset'))
			->getMock();

		$clientScript->expects($this->at(0))
			->method('reset');

		$instance->setClientScript($clientScript);
		$instance->registerCssFile('css/style.css');
		$instance->registerScriptFile('js/style.js');
		$instance->reset();

		$css = PHPUnit_Framework_Assert::readAttribute($instance, 'css');
		$js = PHPUnit_Framework_Assert::readAttribute($instance, 'js');

		$this->assertEquals($css, array());
		$this->assertEquals($js, array());
	}
}
