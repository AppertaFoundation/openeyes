<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models\traits;

/**
 * Trait HasRelationOptions
 *
 * This provides support for automatically retrieving the valid options for relationship on
 * a model. Note that it supports two approaches for retrieving these options:
 *
 * With a defined relationship named 'foo'
 * $modelInstance->foo_options
 * $modelInstance->fooOptions()
 *
 * Whilst the property accessor is still supported, the method is the preferred approach as this
 * will respect the relation values being updated on the given model.
 *
 * @package OEModule\OphCiExamination\models\traits
 */
trait HasRelationOptions
{
    public static $relation_options_lookup_cache;

    // set this array on your class to define options
    // that should not be automatically returned by this trait
    // public $relation_options_skip = [];

    public function __get($name)
    {
        if (substr($name, -8) === '_options') {
            $relation_name = strtolower(substr($name, 0, -8));
            if (!$this->shouldSkipRelation($relation_name) && $this->getRelationByName($relation_name)) {
                // call the standard method name for options so if that is
                // overridden in the containing class, that is used.
                return $this->{"{$relation_name}Options"}();
            }
        }
        return parent::__get($name);
    }

    public function __call($name, $parameters)
    {
        if (substr($name, -7) === 'Options') {
            $relation_name = strtolower(substr($name, 0, -7));
            $relation = $this->getRelationByName($relation_name);
            if ($relation) {
                return $this->getRelationOptionsForRelation($relation);
            }
        }
        parent::__call($name, $parameters);
    }

    public static function clearCache()
    {
        static::$relation_options_lookup_cache = [];
    }

    protected function shouldSkipRelation($relation_name)
    {
        return property_exists($this, 'relation_options_to_skip')
            && in_array($relation_name, $this->relation_options_to_skip);
    }

    protected static function getRelationOptionsCacheKey($related_cls, $pks)
    {
        $pk_key = count($pks) > 0 ? implode(".", $pks) : "__all__";
        return $related_cls . $pk_key;
    }

    protected static function getAndSetRelationOptionsCache($cache_key, $callback)
    {
        if (!isset(static::$relation_options_lookup_cache[$cache_key])) {
            static::$relation_options_lookup_cache[$cache_key] = $callback();
        }

        return static::$relation_options_lookup_cache[$cache_key];
    }

    /**
     * Caches at static level of model to prevent duplicate queries retrieving relation
     * options
     *
     * @param $related_cls
     * @param $current_pks
     * @return
     */
    protected static function getOptionsForRelatedClass($related_cls, $current_pks)
    {
        $cache_key = static::getRelationOptionsCacheKey($related_cls, $current_pks);

        return static::getAndSetRelationOptionsCache(
            $cache_key,
            function () use ($related_cls, $current_pks) {
                return $related_cls::model()
                    ->activeOrPk($current_pks)
                    ->findAll(['order' => 'display_order asc']);
            }
        );
    }

    protected function getRelationOptionsForRelation($relation)
    {
        $related_cls = $relation->className;
        $current_pks = $this->getCurrentlyAttachedPrimaryKeysForRelation($relation);

        return static::getOptionsForRelatedClass($related_cls, $current_pks);
    }

    private function getRelationByName($relation_name)
    {
        if (!$this->shouldSkipRelation($relation_name) && $this->getMetaData()->hasRelation($relation_name)) {
            return $this->getMetaData()->relations[$relation_name];
        }
    }

    private function getCurrentlyAttachedPrimaryKeysForRelation($relation)
    {
        if (is_a($relation, self::BELONGS_TO)) {
            return $this->{$relation->foreignKey} ? [$this->{$relation->foreignKey}] : [];
        } else {
            $currently_related = $this->getRelated($relation->name) ? : [];
            return array_map(function ($i) {
                return $i->id;
            }, $currently_related);
        }
    }
}
