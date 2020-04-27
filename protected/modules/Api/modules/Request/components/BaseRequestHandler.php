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

/**
 * Class BaseRequestHandler
 * and derived classes are responsible for collecting/processing the request data
 * and make them ready to be saved
 */
abstract class BaseRequestHandler
{
    /**
     * Defines the request type like DICOM
     * @var string
     */
    public $request_type;

    /**
     *  System messages can be added to store extra information
     * @var string
     */
    public $system_message;

    /**
     * The media type of the resource
     * @var string
     */
    public $content_type;

    /**
     * This object validates and saves all the data we are saving
     * @var RequestSaveHandler
     */
    public $save_handler;

    /**
     * Multiple AttachmentData object can be added
     * @var array of AttachmentData
     */
    public $attachment_data = [];

    public $php_file_upload_errors = [
        0 => 'There is no error, the file uploaded with success',
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.',
    ];

    public function __construct($request_type, $system_message, $content_type)
    {
        $this->save_handler = new RequestSaveHandler();

        $this->request_type = $request_type;
        $this->system_message = $system_message;
        $this->content_type = $content_type;

        //STEP 1 : save query string parameters
        $this->addQueryString();

        //STEP 2: save payload
        $this->addPayload();

        //STEP 3: save payload
        $this->addFormData();

        //STEP 4: save header data
        $this->addHeaderData();
    }

    /**
     * Saving the Query string parameters
     * Adding request_type and system_message to the Request model
     * and creating a new AttachmentData to be saved
     */
    public function addQueryString()
    {
        $this->save_handler->request->request_type = $this->request_type;
        $this->save_handler->request->system_message = $this->system_message;

        $attachment_data = new AttachmentData();
        $attachment_data->attachment_mnemonic = "REQUEST_DATA";
        $attachment_data->body_site_snomed_type = null;
        $attachment_data->attachment_type = "NONE";
        $attachment_data->mime_type = "application/json";
        $attachment_data->blob_data = null;
        $attachment_data->text_data = json_encode($_GET);
        $attachment_data->upload_file_name = null;
        $attachment_data->system_only_managed = 0;

        $this->save_handler->addAttachmentData($attachment_data);
    }

    /**
     * This method is responsible for fetching the file inputs either from $_FILES or php://input
     * the implementation is in the derived classes
     * @return mixed
     */
    abstract public function addPayload();

    /**
     * Whenever it is a POST or PUT request we fetch and save the form data
     * If the request is PUT and the content-type is simple like 'application/pdf'
     * than this method should be overwritten in the child class as the message body
     * contains only the (binary) file - no form data will be there
     * see SingleRequestHandler class
     */
    public function addFormData()
    {
        $data = $_POST;
        if (Yii::app()->request->isPutRequest) {
            $data = Yii::app()->request->restParams;
        }

        $attachment_data = new AttachmentData();
        $attachment_data->attachment_mnemonic = "FORM_DATA";
        $attachment_data->body_site_snomed_type = null;
        $attachment_data->attachment_type = "NONE";
        $attachment_data->mime_type = $this->content_type; // application/x-www-form-urlencoded or multipart/form-data
        $attachment_data->blob_data = null;
        $attachment_data->text_data = json_encode($data);
        $attachment_data->upload_file_name = null;
        $attachment_data->scenario = 'insert';
        $attachment_data->system_only_managed = 0;

        $this->save_handler->addAttachmentData($attachment_data);
    }

    /**
     * Collecting header information, client IP address and create AttachmentData to be saved
     */
    public function addHeaderData()
    {
        $ip = $this->getIpAddress();
        $headers = getallheaders();
        $text_data = $ip ? ($headers + ['origin-ip-address' => $ip]) : $headers;

        $attachment_data = new AttachmentData();
        $attachment_data->scenario = 'insert';

        $attachment_data->attachment_mnemonic = "HEADER_DATA";
        $attachment_data->body_site_snomed_type = null;
        $attachment_data->attachment_type = "NONE";
        $attachment_data->mime_type = "application/json";
        $attachment_data->blob_data = null;
        $attachment_data->text_data = json_encode($text_data);
        $attachment_data->upload_file_name = null;
        $attachment_data->system_only_managed = 0;

        $this->save_handler->addAttachmentData($attachment_data);
    }

    /**
     * Determinate the client's IP address
     * @return string|null
     */
    public function getIpAddress()
    {
        $ip = null;
        if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Return error summary of the models
     * @return array
     */
    public function errorSummary()
    {
        return $this->save_handler->errorSummary();
    }

    /**
     * Save the models
     * @return bool|int
     */
    public function save()
    {
        return $this->save_handler->save();
    }

    /**
     * Determinate the mime type in the $_FILES based on the index
     * @param $key
     * @return mixed
     */
    public function getFileMimeType($key)
    {
        if (isset($_FILES[$key]['tmp_name'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES[$key]['tmp_name']);
            finfo_close($finfo);
            return $mime;
        }
    }

    public function isBlob()
    {
        $content_type = $this->content_type;

        switch ($content_type) {
            case 'application/json':
            case 'application/xml':
            case 'text/plain':
                return false;
            default:
                return true;
        }
    }
}
