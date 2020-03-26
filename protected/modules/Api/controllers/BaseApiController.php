<?php
/**
 * (C) Copyright Apperta Foundation 2020
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

class BaseApiController extends \CController
{

    public function beforeAction($action)
    {
        $this->onBeforeAction(new \CEvent($this, ["action" => $action]));
        return parent::beforeAction($action);
    }

    public function onBeforeAction(\CEvent $event)
    {
        $this->raiseEvent('onBeforeAction', $event);
    }

    public function renderJSON($status, $data)
    {
        ob_clean(); // clear output buffer to avoid rendering anything else
        header('HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status));
        header('Content-type: application/json'); // set content type header as json
        if ($status == 401) {
            header('WWW-Authenticate: Basic realm="OpenEyes"');
        }

        echo json_encode($data);
        \Yii::app()->end();
    }

    private function _getStatusCodeMessage($status)
    {
        $codes = [
            200 => 'OK',
            401 => 'Unauthorized',
            401 => 'Forbidden',
            422 => 'Unprocessable Entity',
        ];

        return (isset($codes[$status])) ? $codes[$status] : '';
    }
}
