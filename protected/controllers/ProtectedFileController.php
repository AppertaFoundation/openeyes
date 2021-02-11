<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class ProtectedFileController extends BaseController
{
    public function accessRules()
    {
        return array(
                array('allow',
                    'actions' => array('download', 'view', 'thumbnail'),
                    'roles' => array('OprnViewProtectedFile'),
                ),
                array('allow',
                    'actions' => array('import'),
                    'roles' => array('admin'),
                ),
        );
    }

    public function actionDownload($id)
    {
        if (!$file = ProtectedFile::model()->findByPk($id)) {
            throw new CHttpException(404, 'File not found');
        }
        if (!file_exists($file->getPath())) {
            throw new CException('File not found on filesystem: '.$file->getPath());
        }
        header('Content-Description: File Transfer');
        header('Content-Type: '.$file->mimetype);
        header('Content-Disposition: attachment; filename="'.$file->name.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Content-Length: '.$file->size);
        ob_clean();
        flush();
        readfile($file->getPath());
    }

    public function actionView($id, $name, $rotate = null)
    {
        if (!$file = ProtectedFile::model()->findByPk($id)) {
            throw new CHttpException(404, 'File not found');
        }
        $filepath = $file->getPath();

        if (!file_exists($file->getPath())) {
            throw new CException('File not found on filesystem: '.$file->getPath());
        }
        header('Content-Type: '.$file->mimetype);

        $image_size = getimagesize($filepath);
        $mime = isset($image_size['mime']) ? $image_size['mime'] : null;
        if ($mime && $mime == 'image/jpeg') {
            if ($rotate) {
                $original = imagecreatefromjpeg($filepath);
                $rotated = imagerotate($original, $rotate, imageColorAllocateAlpha($original, 255, 255, 255, 127));
                ob_start();
                imagejpeg($rotated);
                $size = ob_get_length();
                header("Content-length: " . $size);
                ob_flush();
            }
        }

        ob_clean();
        flush();
        readfile($filepath);
    }

    public function actionThumbnail($id, $dimensions, $name)
    {
        if (!$file = ProtectedFile::model()->findByPk($id)) {
            throw new CHttpException(404, 'File not found');
        }
        if (!$thumbnail = $file->getThumbnail($dimensions, true)) {
            throw new CHttpException(404, 'Thumbnail not available');
        }
        header('Content-Type: '.$file->mimetype);
        header('Content-Length: '.$thumbnail['size']);
        ob_clean();
        flush();
        readfile($thumbnail['path']);
    }

    public function actionImport()
    {
        echo '<h1>Importing files:</h1>';
        foreach (glob(Yii::app()->basePath.'/data/test/*') as $src_file) {
            $file = ProtectedFile::createFromFile($src_file);
            if (!$file->save()) {
                throw new CException('Cannot save file'.print_r($file->getErrors(), true));
            }
            unlink($src_file);
            echo '<p>Imported '.$file->uid.' - '.$file->name.'</p>';
        }
        echo '<p>Done!</p>';
    }
}
