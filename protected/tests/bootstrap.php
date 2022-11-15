<?php
if (PHP_MAJOR_VERSION >= 7 && PHP_MINOR_VERSION >= 1) {
    // skip deprecation errors in PHP 7.1 and above
    error_reporting(E_ALL & ~E_DEPRECATED);
}

ini_set("log_errors_max_len",0);

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__);
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/test-traits');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/test-helpers');

defined('YII_ENABLE_EXCEPTION_HANDLER') or define('YII_ENABLE_EXCEPTION_HANDLER', false);
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', false);
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 1);

if (version_compare(PHP_VERSION, '5.3', '>=')) {
    require_once(dirname(__FILE__) . '/compatability.php');
}

// change the following paths if necessary
$dirname = dirname(__FILE__);
if (file_exists($dirname . '/../../vendor/yiisoft/yii/framework/yiit.php')) {
    $yiit = $dirname . '/../../vendor/yiisoft/yii/framework/yiit.php';
} else {
    $yiit = $dirname . '/../../protected/yii/framework/yiit.php';
}
$config = dirname(__FILE__) . '/../config/test.php';

require_once $yiit;

/**
 * The custom autoloader for Yii will endeavour to load filenames based on the test suite names
 * in phpunit.xml - specifying this filter prevents this. There doesn't appear to be a means
 * by which this can simply be added as a filter, but at the time of creation, no other use of
 * this autoloading filter existed.
 */
Yii::$autoloaderFilters = ['filterTestSuiteNames' => function ($className) {
    return in_array($className, ['all', 'core', 'Modules']);
}];

/**
 * This workaround is in place to ensure that HtmlPurifier is loaded from the Yii standalone include
 * for other packages to access, thereby preventing class loader clashes. see OE-13296 for further details.
 *
 * would be good to abstract autoload dependencies from root index.php to ensure running consistently
 */
if (!class_exists('HTMLPurifier_Bootstrap', false)) {
    require_once(Yii::getPathOfAlias('system.vendors.htmlpurifier') . DIRECTORY_SEPARATOR . 'HTMLPurifier.standalone.php');
    HTMLPurifier_Bootstrap::registerAutoload();
}

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'bootstrap_process_isolation.php');

Yii::createWebApplication($config);

Yii::app()->event->observers = array();

// PHPUnit dies silently with FATAL ERRORS which makes it hard to debug the tests.
register_shutdown_function('PHPUnit_shutdownFunction');
function PHPUnit_shutdownFunction()
{
    // http://www.php.net/manual/en/errorfunc.constants.php
    $error = error_get_last();
    if ($error !== null) {
        if ($error['type'] & (E_ERROR + E_PARSE + E_CORE_ERROR + E_COMPILE_ERROR + E_USER_ERROR + E_RECOVERABLE_ERROR)) {
            echo 'Test Bootstrap: Caught untrapped fatal error: ';
            var_export($error);
        }
    }
}
