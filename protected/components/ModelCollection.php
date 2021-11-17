<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class ModelCollection
{
    private array $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Retrieves all of the values for a given attribute
     *
     * @param $attribute
     * @return array
     * @throws Exception
     */
    public function pluck($attribute): array
    {
        return array_map(function($model) use ($attribute) {

            if (!$model->hasAttribute($attribute)) {
                throw new Exception("The '$attribute' does not exist on '" . get_class($model) . "' model");
            }

            return $model->$attribute;
        }, $this->data);
    }
}
