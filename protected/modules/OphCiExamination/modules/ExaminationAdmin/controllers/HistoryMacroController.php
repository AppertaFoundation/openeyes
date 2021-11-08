<?php

use OEModule\OphCiExamination\models\HistoryMacro;
use OEModule\OphCiExamination\models\HistoryMacro_Subspecialty;

class HistoryMacroController extends \ModuleAdminController
{
    public $group = 'Examination';

    public function actions()
    {
        return [
            'sortHistoryMacros' => [
                'class' => 'SaveDisplayOrderAction',
                'model' => HistoryMacro::model(),
                'modelName' => 'HistoryMacro',
            ],
        ];
    }

    public function actionList()
    {
        $assetManager = Yii::app()->getAssetManager();
        $assetManager->registerScriptFile('/js/oeadmin/OpenEyes.admin.js');
        $assetManager->registerScriptFile('/js/oeadmin/list.js');

        $this->render('/historymacros/index', [
            'model_list' => HistoryMacro::model()->findAll(['order' => 'display_order asc']),
        ]);
    }

    public function actionEdit($id)
    {
        $model = HistoryMacro::model()->findByPk($id);
        $errors = [];

        $request = Yii::app()->getRequest();
        if ($post = $request->getPost('OEModule_OphCiExamination_models_HistoryMacro')) {
            $model->attributes = $post;
            $subspecialties = (array_key_exists('subspecialties', $post) && is_array($post['subspecialties'])) ? $post['subspecialties'] : [];
            if ($model->save()) {
                HistoryMacro_Subspecialty::model()->deleteAll('history_macro_id = :macro_id', [':macro_id' => $id]);
                $saved = $model->createMappings(ReferenceData::LEVEL_SUBSPECIALTY, $subspecialties);
                if ($saved) {
                    Audit::add('admin', 'edit', serialize($model->attributes), false, ['model' => 'OEModule_OphCiExamination_models_HistoryMacro']);
                    Yii::app()->user->setFlash('success', 'History Macro saved');
                    $this->redirect(['list']);
                } else {
                    $errors = $model->getErrors();
                }
            } else {
                $errors = $model->getErrors();
            }
        }

        $this->render('/historymacros/edit', [
            'model' => $model,
            'errors' => $errors,
        ]);
    }

    public function actionCreate()
    {
        $model = new HistoryMacro();
        $errors = [];

        $request = Yii::app()->getRequest();
        if ($post = $request->getPost('OEModule_OphCiExamination_models_HistoryMacro')) {
            $model->attributes = $post;
            $subspecialties = is_array($post['subspecialties']) ? $post['subspecialties'] : [];

            // Set display order
            $criteria=new CDbCriteria;
            $criteria->select = 'max(display_order) AS display_order';
            $order = HistoryMacro::model()->find($criteria);
            $model->display_order = (int)$order['display_order'] + 1;

            if ($model->save()) {
                $saved = $model->createMappings(ReferenceData::LEVEL_SUBSPECIALTY, $subspecialties);
                if ($saved) {
                    Audit::add('admin', 'create', serialize($model->attributes), false, ['model' => 'OEModule_OphCiExamination_models_HistoryMacro']);
                    Yii::app()->user->setFlash('success', 'History Macro saved');
                    $this->redirect(['list']);
                } else {
                    $errors = $model->getErrors();
                }
            } else {
                $errors = $model->getErrors();
            }
        }

        $this->render('/historymacros/edit', [
            'model' => $model,
            'errors' => $errors,
        ]);
    }

    public function actionDelete()
    {
        $delete_ids = isset($_POST['select']) ? $_POST['select'] : [];
        $transaction = Yii::app()->db->beginTransaction();
        $success = true;
        try {
            foreach ($delete_ids as $macro_id) {
                $macro = HistoryMacro::model()->findByPk($macro_id);
                if ($macro) {
                    HistoryMacro_Subspecialty::model()->deleteAll('history_macro_id = :macro_id', [':macro_id' => $macro_id]);
                    if (!$macro->delete()) {
                        $success = false;
                        break;
                    } else {
                        Audit::add('admin', 'delete', serialize($macro), false, ['model' => 'OEModule_OphCiExamination_models_HistoryMacro']);
                    }
                }
            }
        } catch (Exception $e) {
            \OELog::log($e->getMessage());
            $success = false;
        }

        if ($success) {
            $transaction->commit();
            echo '1';
        } else {
            $transaction->rollback();
            echo '0';
        }
    }
}
