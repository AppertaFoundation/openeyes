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
class FormDataHandler extends BaseRequestHandler
{
    /**
     * @inheritdoc
     */
    public function addPayload()
    {
        if (!$_FILES) {
            $this->save_handler->addError('AttachmentData', 'File not found sent by client.');
        } else {
            foreach ($_FILES as $name => $file) {
                if ($_FILES[$name]['error'] === 0) {
                    $attachment_data = new AttachmentData();
                    // model needs to be informed that we need blob data here
                    $attachment_data->scenario = "FileRequest";

                    $attachment_data->attachment_mnemonic = $name; // e.g.: "REQUEST_BLOB";
                    $attachment_data->body_site_snomed_type = null;
                    $attachment_data->attachment_type = "NONE";
                    $attachment_data->mime_type = isset($file['type']) ? $file['type'] : $this->getFileMimeType($name);
                    $attachment_data->blob_data = null;
                    $attachment_data->text_data = null;
                    $content = isset($file['tmp_name']) ? file_get_contents($file['tmp_name']) : null;

                    if ($this->isBlob()) {
                        $attachment_data->blob_data = $content;
                    } else {
                        $attachment_data->text_data = $content;
                    }

                    $attachment_data->upload_file_name = isset($file['name']) ? $file['name'] : null;
                    $attachment_data->system_only_managed = 0;

                    $this->save_handler->addAttachmentData($attachment_data);
                } else {
                    $this->save_handler->addError("AttachmentData: $name", $this->php_file_upload_errors[$_FILES[$name]['error']]);
                }
            }
        }

    }
}
