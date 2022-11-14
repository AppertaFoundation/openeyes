<?php
/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This script has been added to perform the necessary manipulations to the php/Yii environment
 * to support running specific tests in isolated processes. This has proven necessary as the test
 * suite has expanded to cover multiple classes that have the same name. Specifically this arises
 * with tests that involve controller classes, because of the convention of using the same class
 * names across multiple non-namespaced modules.
 */

if ($_SERVER['PHP_SELF'] !== 'Standard input code') {
    return;
}

// we are running in process isolation, need to adjust server global array for Yii
$phpunit_location = implode(DIRECTORY_SEPARATOR, ['..', 'vendor', 'phpunit', 'phpunit', 'phpunit']);
foreach (['PHP_SELF', 'SCRIPT_NAME', 'SCRIPT_FILENAME'] as $server_arg) {
    $_SERVER[$server_arg] = __DIR__ . DIRECTORY_SEPARATOR .  $phpunit_location;
}

// Because we have non-namespaced test classes, PHP will be trying to instantiate the
// isolated test to run it. We register an autoload filter in Yii to support this
Yii::$autoloaderFilters['autoloadTestClasses'] = function ($className) {
    if (!str_ends_with($className, 'Test') || strpos($className, '\\') !== false) {
        return;
    }

    // module tests path
    $test_dirs = glob(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'modules', '*', 'tests', '*']), GLOB_ONLYDIR);
    // core tests path
    $test_dirs[] = __DIR__;
    foreach ($test_dirs as $possible_location) {
        $recursive_dir = new RecursiveDirectoryIterator($possible_location);
        $iterator = new RecursiveIteratorIterator($recursive_dir);
        $match = iterator_to_array(new RegexIterator($iterator, "/$className.php$/", RegexIterator::GET_MATCH));
        if (count($match)) {
            require_once(array_keys($match)[0]);
            return true; // autoload success
        }
    }
};
