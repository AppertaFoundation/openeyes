<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class FreehandDrawController extends \ModuleAdminController
{
    public $group = 'Examination';

    private function errorCodeToMessage($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

    public function actions()
    {
        return [
            'sort' => [
                'class' => 'SaveDisplayOrderAction',
                'model' => DrawingTemplate::model(),
                'modelName' => 'DrawingTemplate'
            ],
        ];
    }

    public function actionIndex()
    {
        $asset_manager = Yii::app()->getAssetManager();
        $asset_manager->registerScriptFile('/js/oeadmin/OpenEyes.admin.js');
        $asset_manager->registerScriptFile('/js/oeadmin/list.js');
        $model = DrawingTemplate::model();

        $this->render(
            '/FreehandDraw/index',
            [
                'templates' => $model->findAll(['order' => 'display_order']),
                'pagination' => $this->initPagination($model),
            ]
        );
    }

    /**
     * Renders the create page
     */
    public function actionCreate()
    {
        $model = new DrawingTemplate;

        if (\Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST['DrawingTemplate'];

            // remove 'protected_file_id' before validate, we didn't save file yet
            $attributes = $model->attributeNames();
            $attributes[] = 'image';

            $attributes = array_values(array_filter($attributes, fn ($attr) => $attr !== 'protected_file_id'));

            if ($model->validate($attributes)) {
                $image_upload_file = \CUploadedFile::getInstance($model, 'image');

                if ($image_upload_file) {
                    if (!$image_upload_file->hasError) {
                        if ($id = $this->uploadFile($image_upload_file->tempName, $image_upload_file->name)) {
                            $model->protected_file_id = $id;

                            // no error save the model and redirect
                            if ($model->save(false) && !$model->hasErrors()) {
                                $this->redirect(array('/OphCiExamination/admin/FreehandDraw/index'));
                            }
                        } else {
                            // ProtectedFile save() failed
                            $model->addError('protected_file_id', 'Could not save the file.');
                        }
                    } else {
                        $model->addError('protected_file_id', $this->errorCodeToMessage($image_upload_file->error));
                    }
                } else {
                    // CUploadedFile::getInstance failed to create a CUploadedFile instance
                    $model->addError('protected_file_id', 'Something went wrong. Please try again.');
                }
            }
        }


        $this->render('/FreehandDraw/create', array(
            'model' => $model,
        ));
    }

    /**
     * @param $id
     * @throws CDbException
     */
    public function actionEdit($id)
    {
        $model = DrawingTemplate::model()->findByPk($id);

        if (!$model) {
            $this->redirect(array('/OphCiExamination/admin/FreehandDraw/index'));
        }

        if (\Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST['DrawingTemplate'];

            if ($model->validate()) {
                // delete the existing file first, before trying to upload a new file.
                $original_protected_file_id = $model->protected_file_id;

                $image_upload_file = \CUploadedFile::getInstance($model, 'image');

                if ($image_upload_file) {
                    if (!$image_upload_file->hasError) {
                        if ($id = $this->uploadFile($image_upload_file->tempName, $image_upload_file->name)) {
                            $model->protected_file_id = $id;
                            // no error save the model and redirect
                            if ($model->save(false) && !$model->hasErrors()) {
                                ProtectedFile::model()->deleteByPk($original_protected_file_id);

                                $this->redirect(array('/OphCiExamination/admin/FreehandDraw/index'));
                            }
                        } else {
                            // ProtectedFile save() failed
                            $model->addError('protected_file_id', 'Could not save the file.');
                        }
                    } else {
                        $model->addError('protected_file_id', $this->errorCodeToMessage($image_upload_file->error));
                    }
                } else {
                    // probably no file was selected/uploaded
                    // we just keep the original one
                    //$model->protected_file_id = $original_protected_file_id;
                    if ($model->save()) {
                        $this->redirect(array('/OphCiExamination/admin/FreehandDraw/index'));
                    }
                }
            }
        }

        $this->render('/FreehandDraw/edit', ['model' => $model]);
    }

    public function actionDelete()
    {
        $pks = Yii::app()->request->getPost('delete_templates');

        if (!$pks) {
            echo 0;
        }

        $transaction = Yii::app()->db->beginTransaction();
        try {
            foreach (DrawingTemplate::model()->findAllByPk($pks) as $template) {
                $protected_file = ProtectedFile::model()->findByPk($template->protected_file_id);
                if (!$template->delete()) {
                    $transaction->rollback();
                    echo '0';
                    return;
                }

                $protected_file->delete();
            }
            $transaction->commit();
        } catch (Exception $exception) {
            OELog::logException($exception);
            $transaction->rollback();
            echo '0';
            return;
        }

        echo '1';
    }

    /**
     * @param $tmp_name
     * @param $original_name
     * @return false|int
     * @throws Exception
     */
    private function uploadFile($tmp_name, $original_name)
    {
        $file = ProtectedFile::createFromFile($tmp_name);
        $file->name = $original_name;
        $file->title = $original_name;
        if ($file->save()) {
            unlink($tmp_name);
            return $file->id;
        } else {
            \OELog::log(print_r($file->getErrors(), true));
        }

        unlink($tmp_name);
        return false;
    }
}
