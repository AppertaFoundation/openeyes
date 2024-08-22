<?php
/**
 * OpenEyes.
 *
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

/**
 * Class AttachmentDisplayController
 */
class AttachmentDisplayController extends BaseApiController
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('view', 'create'),
                'users' => array('@'),
            ),
            array(
                'deny',
                'users' => array('*'),
            ),
        );
    }

    public function actionView($id)
    {
        // Adapted from http://ernieleseberg.com/php-image-output-and-browser-caching/
        $criteria = new CDbCriteria();
        $criteria->addCondition('id = :attachment_id');
        $criteria->params[':attachment_id'] = $id;

        $model = AttachmentData::model()->find($criteria);

        if (isset($model)) {
            $file_mod_time = strtotime($model->last_modified_date);
            $headers = $this->getRequestHeaders();
            $mime_type = isset($_GET['mime']) ? $_GET['mime'] : '';

            header('Content-type: ' . $mime_type);
            /*
            * It should be safe to make the file immutable, as attachment references should always be unique ids, and modifications result in a new id.
            * However, if not then the last modified timestamp should be appended to all urls to ensure cachebusting (same as EventImages)
            */
            header('Cache-Control: private, max-age=31536000, immutable');
            header('Pragma:');
            // Check if the client is validating his cache and if it is current.
            if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == $file_mod_time)) {
                // Client's cache IS current, so we just respond '304 Not Modified'.
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $file_mod_time) . ' GMT', true, 304);
            } else {
                $data = isset($model[$_GET['attachment']]) ? $model[$_GET['attachment']] : null;
                // not cached or cache outdated, we respond '200 OK' and output the attachment.
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $file_mod_time) . ' GMT', true, 200);
                header('Content-transfer-encoding: binary');

                if ($decoded_data = base64_decode($data, true)) {
                    header('Content-length: ' . strlen($decoded_data));
                    echo $decoded_data;
                } else {
                    header('Content-length: ' . strlen($data));
                    echo $data;
                }
            }
        }
    }


    /**
     * @return array|false
     */
    private function getRequestHeaders()
    {
        if (function_exists("apache_request_headers")) {
            if ($headers = apache_request_headers()) {
                return $headers;
            }
        }

        $headers = array();
        // Grab the IF_MODIFIED_SINCE header
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $headers['If-Modified-Since'] = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
        }

        return $headers;
    }

    /**
     * Loads the model with the given ID
     *
     * @param int $id The ID to find
     * @return AttachmentData The attachment that is found
     * @throws CHttpException Thrown if an AttachmentData with the given ID cannot be found
     */
    public function loadModel($id)
    {
        $model = AttachmentData::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }
}
