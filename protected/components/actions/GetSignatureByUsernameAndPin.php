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

class getSignatureByUsernameAndPin extends \CAction
{
    use RenderJsonTrait;
    public $user;
    public $pin;

    public function run()
    {
        $code = 0;
        $error = '';
        $full_username = '';

        $this->pin = Yii::app()->request->getPost('pin');
        $user_id = Yii::app()->request->getPost('user_id');
        $this->user = User::model()->findByPk($user_id);

        $thumbnail1_base64 = '';
        $thumbnail2_base64 = '';

        try {
            if ($this->user && strlen($this->pin)>=4) {
                $full_username = $this->user->getFullName();
                if ($this->user->signature_file_id) {
                    if(!$this->user->checkPin($this->pin,$this->user->id)){
                        throw new Exception('Incorrect PIN.');
                    }

                    $file = ProtectedFile::model()->findByPk($this->user->signature_file_id);
                    if ($file) {
                        $thumbnail1 = $file->getThumbnail("72x24", true);
                        $thumbnail2 = $file->getThumbnail("150x50", true);

                        $thumbnail1_source = file_get_contents($thumbnail1['path']);
                        $thumbnail1_base64 = 'data:' . $file->mimetype . ';base64,' . base64_encode($thumbnail1_source);

                        $thumbnail2_source = file_get_contents($thumbnail2['path']);
                        $thumbnail2_base64 = 'data:' . $file->mimetype . ';base64,' . base64_encode($thumbnail2_source);
                    } else {
                        throw new Exception('Signature file not found');
                    }
                } else {
                    throw new Exception('Signature not assigned.');
                }
            } else {
                throw new Exception('Missing user, or malformed pin.');
            }
        } catch (Exception $e) {
            $code = 1;
            $error = $e->getMessage();
        }

        $response = array(
            'code' => $code,
            'error' => $error,
            'singature_image1_base64' => $thumbnail1_base64,
            'singature_image2_base64' => $thumbnail2_base64,
            'signatory_name' => $full_username,
            'date' => date('Y.m.d'),
            'time' => date('H:i'),
        );

        $this->renderJSON($response);
    }
}