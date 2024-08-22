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

trait RenderJsonTrait
{
    /**
     * Renders data as JSON, turns off any to screen logging so output isn't broken.
     *
     * @param $data
     */
    protected function renderJSON($data)
    {
        $this->sendJSONHeader('Content-type: application/json');

        echo json_encode($data);

        foreach (Yii::app()->log->routes as $route) {
            if ($route instanceof CWebLogRoute) {
                $route->enabled = false; // disable any weblogroutes
            }
        }

        $this->endJSONResponse();
    }

    protected function sendJSONHeader($header)
    {
        $wrapper = \Yii::app()->params['header_wrapper_callback'] ?? null;

        if ($wrapper) {
            if (is_callable($wrapper)) {
                call_user_func($wrapper, $header);
            }
        } else {
            header($header);
        }
    }

    protected function endJSONResponse()
    {
        if (!(\Yii::app()->params['header_wrapper_callback'] ?? null)) {
            \Yii::app()->end();
        }
    }
}
