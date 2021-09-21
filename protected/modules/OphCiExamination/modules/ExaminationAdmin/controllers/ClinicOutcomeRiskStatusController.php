<?php
use \OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Risk_Status;
class ClinicOutcomeRiskStatusController extends \ModuleAdminController
{
    public function actionEdit()
    {
        $this->group = 'Examination';
        $model_name = 'OEModule_OphCiExamination_models_OphCiExamination_ClinicOutcome_Risk_Status';
        $risk_status_entries = OphCiExamination_ClinicOutcome_Risk_Status::model()->findAll();
        $post_data = Yii::app()->request->getParam($model_name, array());
        $error_msg = array();
        $is_list_changed = false;
        if ($post_data) {
            $transaction = Yii::app()->db->beginTransaction();
            foreach ($risk_status_entries as $risk_entry) {
                if (!$post_data[$risk_entry->id]) {
                    continue;
                }
                $risk_entry->attributes = $post_data[$risk_entry->id]['attributes'];
                if ($risk_entry->isModelDirty()) {
                    if (!$risk_entry->save()) {
                        $error_msg = $risk_entry->getErrors();
                    } else {
                        $is_list_changed = true;
                    }
                }
            }
            if (!$error_msg) {
                if ($is_list_changed) {
                    $transaction->commit();
                    Yii::app()->user->setFlash('success', 'List updated');
                } else {
                    Yii::app()->user->setFlash('info', 'No change detected');
                }
            } else {
                $transaction->rollback();
            }
        }
        $this->render(
            '/ClinicOutcomeRiskStatus/edit',
            array(
                'risk_status_entries' => $risk_status_entries,
                'model_name' => $model_name,
                'title' => 'Edit Clinical Outcome Risk Status',
                'error_msg' => $error_msg,
            )
        );
    }
}
