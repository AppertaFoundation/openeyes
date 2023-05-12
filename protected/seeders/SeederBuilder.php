<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OE\seeders;

use OE\contracts\ProvidesApplicationContext;
use OE\seeders\contracts\Seedable;

/**
 * Factory class for initialising seeders and handling the context of that initialisation
 */
final class SeederBuilder
{
    private static ?SeederBuilder $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): SeederBuilder
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Build a seeder with the appropriately resolved context for invocation
     *
     * @param string $class_name
     * @param string|null $module_name
     * @param array $context_data
     * @return Seedable
     */
    public function build(string $class_name, string $module_name = null, array $context_data = []): Seedable
    {
        if ($module_name) {
            $class_name = '\\OEModule\\' . $module_name . '\\seeders\\' . $class_name;
        } else {
            $class_name = 'OE\\seeders\\seeders\\' . $class_name;
        }

        $context = $this->getApplicationContextForSeeder($context_data);

        /** @var Seedable */
        $seeder = new $class_name($context);
        $seeder->setSeederAttributes($context_data);

        return $seeder;
    }

    protected function getApplicationContextForSeeder(array $attributes = []): ProvidesApplicationContext
    {
        $context_pk_attributes = ['firm_id', 'institution_id', 'site_id'];
        $override_attributes = array_filter($attributes, function ($key) use ($context_pk_attributes) {
            return in_array($key, $context_pk_attributes);
        },
        ARRAY_FILTER_USE_KEY);

        if (!count($override_attributes)) {
            return \ApplicationContext::fromSession();
        }

        $context_pks = [];
        foreach ($context_pk_attributes as $key) {
            $context_pks[$key] = $override_attributes[$key] ?? \Yii::app()->session()[$key];
        }

        return \ApplicationContext::fromPrimaryKeys($context_pks);
    }
}
