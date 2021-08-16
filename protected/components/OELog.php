<?php
/**
 * OpenEyes.
 *
 * 
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OELog
{
    public static function log($msg, $username = false)
    {
        if (Yii::app()->params['log_events']) {
            if (!$username) {
                if (Yii::app()->session['user_auth']) {
                    $username = Yii::app()->session['user_auth']->username;
                }
            }

            $msg = '[useractivity] '.$msg.' ['.@$_SERVER['REMOTE_ADDR'].']';

            if ($username) {
                $msg .= " [$username]";
            }

            Yii::log($msg);
        }
    }

    /**
     * Logs an exception as if it was thrown.
     *
     * @param $exception
     */
    public static function logException($exception)
    {
        $category = 'exception.'.get_class($exception);
        if ($exception instanceof CHttpException) {
            $category .= '.'.$exception->statusCode;
        }
        $message = $exception->__toString();
        if (isset($_SERVER['REQUEST_URI'])) {
            $message .= ' REQUEST_URI='.$_SERVER['REQUEST_URI'];
        }
        Yii::log($message, CLogger::LEVEL_ERROR, $category);
    }
}
