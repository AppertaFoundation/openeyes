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

    /**
     * @param $id
     * @throws CException
     * @throws CHttpException
     */
    public function actionDownload($id)
    {
        if (!$file = ProtectedFile::model()->findByPk($id)) {
            throw new CHttpException(404, 'File not found');
        }
        if (!$file->file_content) {
            throw new CException('File not found in database');
        }
        header('Content-Description: File Transfer');
        header('Content-Type: '.$file->mimetype);
        header('Content-Disposition: attachment; filename="'.$file->name.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Content-Length: '.$file->size);
        ob_clean();
        flush();
        echo $file->file_content;
    }

    /**
     * @param $id
     * @param $name
     * @param null $rotate
     * @throws CException
     * @throws CHttpException
     * @throws Exception
     */
    public function actionView($id, $name, $rotate = null)
    {
        if (!$file = ProtectedFile::model()->findByPk($id)) {
            throw new CHttpException(404, 'File not found');
        }

        if (!$file->file_content) {
            throw new CException('File not found in database');
        }
        header('Content-Type: '.$file->mimetype);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        if (!file_exists($file->getFilePath())) {
            if (!@mkdir($file->getFilePath(), 0774, true)) {
                throw new Exception("{$file->getPath()} could not be created: permission denied");
            }
        }
        file_put_contents($file->getPath(), $file->file_content);

        $image_size = getimagesize($file->getPath());
        $mime = $image_size['mime'] ?? null;
        if ($mime && $mime === 'image/jpeg' && $rotate) {
            $original = imagecreatefromjpeg($file->getPath());
            $rotated = imagerotate($original, $rotate, imageColorAllocateAlpha($original, 255, 255, 255, 127));
            ob_start();
            imagejpeg($rotated);
            $size = ob_get_length();
            header('Content-length: ' . $size);
            ob_flush();
        }
        unlink($file->getPath());

        ob_clean();
        flush();
        echo $file->file_content;
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
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Content-Length: '.$thumbnail->size);
        ob_clean();
        flush();
        echo $thumbnail->thumbnail_content;
    }

    /**
     * @throws CException
     * @throws Exception
     */
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
