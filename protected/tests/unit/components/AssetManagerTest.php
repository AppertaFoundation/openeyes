<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class CustomBasePathAliasAssetManager extends AssetManager
{
    const BASE_PATH_ALIAS = 'application.tests.fixtures.assets';
}

class AssetManagerTest extends PHPUnit_Framework_TestCase
{
    const BASE_PATH_ALIAS = 'application.tests.assets';
    const BASE_URL = 'assets';

    private $globalInstance;

    public function setUp(): void
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

    /**
     * @covers AssetManager
     */
    public function testInstanceCreated()
    {
        $this->assertTrue(
            $this->globalInstance instanceof AssetManager,
            'Yii::app()->assetManager should be an instance of AssetManager'
        );

        $this->assertTrue(
            $this->globalInstance instanceof CAssetManager,
            'AssetManager should extend CAssetManager'
        );

        $cacheBuster = PHPUnit_Framework_Assert::readAttribute($this->globalInstance, 'cacheBuster');
        $this->assertTrue(
            $cacheBuster instanceof CacheBuster,
            'cacheBuster property on AssetManager instance should be an instance of CacheBuster'
        );

        $clientScript = PHPUnit_Framework_Assert::readAttribute($this->globalInstance, 'clientScript');
        $this->assertTrue(
            $clientScript instanceof ClientScript,
            'clientScript property on AssetManager instance should be an instance of ClientScript'
        );
    }

    /**
     * @covers AssetManager
     */
    public function testGetPublishedPathOfAlias()
    {
        $instance = self::getInstance();

        // Test the published path matches the expected published path.
        $alias = CustomBasePathAliasAssetManager::BASE_PATH_ALIAS;
        $publishedPath = $instance->getPublishedPathOfAlias($alias);
        $expectedPublishedPath = $instance->publish(Yii::getPathOfAlias($alias), true);
        $this->assertEquals(
            $publishedPath,
            $expectedPublishedPath,
            'The published path of specified alias should match the expected path'
        );

        // Test the published path matches the expected published path *when no alias is specified*.
        $publishedPath = $instance->getPublishedPathOfAlias();
        $expectedPublishedPath = $instance->publish(Yii::getPathOfAlias($alias), true);
        $this->assertEquals(
            $publishedPath,
            $expectedPublishedPath,
            'The published path should match the expected path when no alias is specified'
        );
    }

    /**
     * @covers AssetManager
     */
    public function testCreateUrl()
    {
        $instance = self::getInstance();
        $alias = CustomBasePathAliasAssetManager::BASE_PATH_ALIAS;
        $path = 'img/cat.gif';

        // Test the url matches when specifying a path alias.
        $url = $instance->createUrl($path, $alias, false);
        $expectedUrl = $instance->getPublishedPathOfAlias($alias).'/'.$path;
        $this->assertEquals(
            $expectedUrl,
            $url,
            'The URL should match when specifying a path alias'
        );

        // Test the url matches when *when no alias is specified*.
        $url = $instance->createUrl($path, null, false);
        $this->assertEquals(
            $expectedUrl,
            $url,
            'The URL should match when no alias is specified'
        );

        // Test the url matches when an alias path is prevented from being preprended to the path.
        $url = $instance->createUrl($path, false, false);
        $expectedUrl = Yii::app()->createUrl($path);
        $this->assertEquals(
            $expectedUrl,
            $url,
            'The URL should match when an alias is prevented from being prepended to the path'
        );

        // Test a cache buster string is appended to url.
        $path1 = $path;
        $path2 = $path1.'?cats=rule';

        $url1 = $instance->createUrl($path1, false, true);
        $url2 = $instance->createUrl($path2, false, true);

        $expectedUrl1 = Yii::app()->cacheBuster->createUrl(Yii::app()->createUrl($path));
        $expectedUrl2 = Yii::app()->cacheBuster->createUrl(Yii::app()->createUrl($path2));

        $this->assertEquals(
            $expectedUrl1,
            $url1,
            'The URL, without query string params, should be cache busted'
        );

        $this->assertEquals(
            $expectedUrl2,
            $url2,
            'The URL, with query string params, should be cache busted'
        );

        // Test default params.
        $url = $instance->createUrl($path);
        $expectedUrl = Yii::app()->cacheBuster->createUrl($instance->getPublishedPathOfAlias($alias).'/'.$path);
        $this->assertEquals(
            $expectedUrl,
            $url,
            'The URL should match the expected format when no additional params are specified'
        );
    }

    /**
     * @covers AssetManager
     */
    public function testGetPublishedPath()
    {
        $instance = self::getInstance();
        $alias = CustomBasePathAliasAssetManager::BASE_PATH_ALIAS;
        $path = 'img/cat.gif';

        // Test with specific params.
        $publishedPath = $instance->getPublishedPath($path, $alias);
        $expectedParts = array(
            Yii::getPathOfAlias('webroot'),
            $instance->publish(Yii::getPathOfAlias($alias), true, -1),
            $path,
        );
        $expectedPath = implode(DIRECTORY_SEPARATOR, $expectedParts);
        $this->assertEquals(
            $publishedPath,
            $expectedPath,
            'The published path should match the expected path when an alias is specified'
        );

        // Test with default params.
        $publishedPath = $instance->getPublishedPath($path);
        $expectedParts = array(
            Yii::getPathOfAlias('webroot'),
            $instance->publish(Yii::getPathOfAlias($alias), true, -1),
            $path,
        );
        $expectedPath = implode(DIRECTORY_SEPARATOR, $expectedParts);
        $this->assertEquals(
            $publishedPath,
            $expectedPath,
            'The published path should match the expected path when no alias is specified'
        );
    }

    /**
     * @covers AssetManager
     */
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

        // Calling registerCoreCssFile with path and priority.
        $assetManager->expects($this->at(1))
            ->method('registerCssFile')
            ->with(
                Yii::app()->clientScript->getCoreScriptUrl().'/test.css',
                false,
                10,
                AssetManager::OUTPUT_ALL,
                true
            );

        // Calling registerCoreCssFile with path, priority and output.
        $assetManager->expects($this->at(2))
            ->method('registerCssFile')
            ->with(
                Yii::app()->clientScript->getCoreScriptUrl().'/test.css',
                false,
                10,
                AssetManager::OUTPUT_PRINT,
                true
            );

        // Calling registerCoreCssFile with path, priority, output and preRegister.
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

    /**
     * @covers AssetManager
     */
    public function testRegisterCoreScript()
    {
        // As this method is simply a proxy to clientscript we just need to
        // test if the clientScript->registerCoreScript is called with the correct
        // params.

        $instance = self::getInstance();

        /**
         * @var $clientScript ClientScript
         */
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
    /**
     * @param string|null $type
     */
    private function runTestAddingFiles($type = null)
    {
        $instance = self::getInstance();

        $registerMethod = $type === 'css' ? 'registerCssFile' : 'registerScriptFile';

        $instance->{$registerMethod}("{$type}/file.{$type}");
        $instance->{$registerMethod}("{$type}/file1.{$type}");

        $publishedPath = $instance->getPublishedPathOfAlias();
        $files = PHPUnit_Framework_Assert::readAttribute($instance, $type);
        $priority = PHPUnit_Framework_Assert::readAttribute(new AssetManager(), $type.'Priority');

        /* Test the files were added to the relevant array. */
        $keys = array_keys($files);

        $this->assertTrue(
            strpos($keys[0], "{$publishedPath}/{$type}/file.{$type}") !== false,
            "file.{$type} should be added to the list of pre-registered assets"
        );

        $this->assertTrue(
            strpos($keys[1], "{$publishedPath}/{$type}/file1.{$type}") !== false,
            "file1.{$type} should be added to the list of pre-registered assets"
        );

        /* Test the files array contains the correct values.*/
        $first = array_shift($files);
        $second = array_shift($files);

        $this->assertTrue(
            isset($first['priority']),
            'The files should be added with a priority'
        );

        $this->assertTrue(
            isset($first['output']),
            'The files should be added with an output'
        );

        $this->assertEquals(
            $first['priority'],
            $priority,
            'The first asset priority should match the expected priority'
        );

        $this->assertEquals(
            $second['priority'],
            $priority - 1,
            'The second asset priority should match the expected priority and be one less than the first'
        );

        /* Test with custom alias path.*/
        $alias = CustomBasePathAliasAssetManager::BASE_PATH_ALIAS.'.'.$type;
        $publishedPath = $instance->getPublishedPathOfAlias($alias);
        $instance->{$registerMethod}("file.{$type}", $alias);
        $files = PHPUnit_Framework_Assert::readAttribute($instance, $type);
        $keys = array_keys($files);

        $this->assertTrue(
            strpos($keys[2], $publishedPath.'/file.'.$type) !== false,
            'The assets should be added with the correct published path'
        );

        // /* Test with custom priority.*/
        $instance->{$registerMethod}("{$type}/file2.{$type}", null, 10);
        $files = PHPUnit_Framework_Assert::readAttribute($instance, $type);
        $last = array_pop($files);

        $this->assertEquals(
            10,
            $last['priority'],
            'The asset should be added with a custom priority if specified'
        );

        // /* Test with custom output.*/
        $instance->{$registerMethod}("{$type}/file3.{$type}", null, null, AssetManager::OUTPUT_PRINT);
        $files = PHPUnit_Framework_Assert::readAttribute($instance, $type);
        $last = array_pop($files);
        $this->assertEquals(
            AssetManager::OUTPUT_PRINT,
            $last['output'],
            'The asset should be added with a custom output if specified'
        );

        /* Test that assets are registered immediately.*/

        // First, we create a mock for the clientScript to check if the correct
        // method is called with the correct params.
        $clientScript = $this->getMockBuilder('ClientScript')
            ->setMethods(array($registerMethod))
            ->getMock();

        $clientScript->expects($this->at(0))
            ->method($registerMethod)
            ->with($instance->createUrl("{$type}/file4.{$type}", null, false));

        $instance->setClientScript($clientScript);

        $instance->{$registerMethod}("{$type}/file4.{$type}", null, null, AssetManager::OUTPUT_ALL, false);

        $publishedPath = $instance->getPublishedPathOfAlias();
        $files = PHPUnit_Framework_Assert::readAttribute($instance, $type);
        $keys = array_keys($files);
        $lastKey = array_pop($keys);

        $this->assertFalse(
            strpos($lastKey, $publishedPath.'/file4.'.$type),
            'The asset should not be added to the list of pre-registered assets if the preRegister param is set to false'
        );
    }

    /**
     * @covers AssetManager
     */
    public function testRegisterCssFile()
    {
        $this->runTestAddingFiles('css');
    }

    /**
     * @covers AssetManager
     */
    public function testRegisterScriptFile()
    {
        $this->runTestAddingFiles('js');
    }

    /**
     * @covers AssetManager
     */
    public function testAdjustScriptMapping()
    {
        $instance = self::getInstance();

        // First we test that no assets are adjusted if the request is *not*
        // an AJAX request.
        $instance->isAjaxRequest = false;
        $instance->adjustScriptMapping();

        $this->assertEquals(
            Yii::app()->clientScript->scriptMap,
            array(),
            'The clientScript script mapping should be empty if the request is not AJAX'
        );

        // Now we test that assets are adjusted if the request is an AJAX request.
        $instance->isAjaxRequest = true;
        $instance->adjustScriptMapping();

        $expectedMapping = AssetManager::$scriptMapping;

        $this->assertEquals(
            Yii::app()->clientScript->scriptMap,
            $expectedMapping,
            'The clientScript script mapping should match the expected mapping if the request is AJAX'
        );
    }

    /**
     * @covers AssetManager
     */
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
            ->with($instance->createUrl('css/style.css', null, false));

        $clientScript->expects($this->any())
            ->method('registerScriptFile')
            ->with($instance->createUrl('js/script.js', null, false));

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
            ->with($instance->createUrl('css/style2.css', null, false));

        $clientScript->expects($this->at(1))
            ->method('registerCssFile')
            ->with($instance->createUrl('css/style1.css', null, false));

        $clientScript->expects($this->at(2))
            ->method('registerScriptFile')
            ->with($instance->createUrl('js/script2.js', null, false));

        $clientScript->expects($this->at(3))
            ->method('registerScriptFile')
            ->with($instance->createUrl('js/script1.js', null, false));

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
            ->with($instance->createUrl('css/all.css', null, false));

        $clientScript->expects($this->at(1))
            ->method('registerCssFile')
            ->with($instance->createUrl('css/style.css', null, false));

        $clientScript->expects($this->exactly(2))
                ->method('registerCssFile');

        $clientScript->expects($this->at(2))
            ->method('registerScriptFile')
            ->with($instance->createUrl('js/all.js', null, false));

        $clientScript->expects($this->at(3))
            ->method('registerScriptFile')
            ->with($instance->createUrl('js/style.js', null, false));

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
            ->with($instance->createUrl('css/all.css', null, false));

        $clientScript->expects($this->at(1))
            ->method('registerCssFile')
            ->with($instance->createUrl('css/print.css', null, false));

        $clientScript->expects($this->exactly(2))
                ->method('registerCssFile');

        $clientScript->expects($this->at(2))
            ->method('registerScriptFile')
            ->with($instance->createUrl('js/all.js', null, false));

        $clientScript->expects($this->at(3))
            ->method('registerScriptFile')
            ->with($instance->createUrl('js/print.js', null, false));

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
            ->with($instance->createUrl('css/all.css', null, false));

        $clientScript->expects($this->at(1))
            ->method('registerCssFile')
            ->with($instance->createUrl('css/ajax.css', null, false));

        $clientScript->expects($this->exactly(2))
                ->method('registerCssFile');

        $clientScript->expects($this->at(2))
            ->method('registerScriptFile')
            ->with($instance->createUrl('js/all.js', null, false));

        $clientScript->expects($this->at(3))
            ->method('registerScriptFile')
            ->with($instance->createUrl('js/ajax.js', null, false));

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

    /**
     * @covers AssetManager
     */
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

        $this->assertEquals(
            array(),
            $css,
            'The list of css assets should be empty after resetting'
        );

        $this->assertEquals(
            array(),
            $js,
            'The list of js assets should be empty after resetting'
        );
    }
}
