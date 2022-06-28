<?php

class InvestigationCommentController extends \BaseController
{
    public function accessRules()
    {
        return array(
            array('allow', 'users' => array('@')),
        );
    }

    public function actionList()
    {
        if (!empty($_POST['investigation'])) {
            $criteria = new \CDbCriteria();

            if (sizeof($_POST['investigation']) > 1) {
                $list = array();

                foreach ($_POST['investigation'] as $investigation) {
                    $criteria->select = 'comments';
                    $criteria->addCondition('(investigation_code = :investigation_code)');
                    $criteria->params[':investigation_code'] = $investigation;
                    $investigationComments = \OEModule\OphCiExamination\models\InvestigationComments::model()->findAll($criteria);

                    $return = array_map(function ($investigationComment) {
                        return $investigationComment->comments;
                    }, $investigationComments);

                    $list[] = $return;
                }

                $intersect = call_user_func_array('array_intersect', $list);

                $this->renderPartial('/investigationcomment/_investigationcommentDialogOptions', array('investigationComments' => $intersect), false, false);
            } else {
                $criteria->addCondition('(investigation_code = :investigation_code)');
                $criteria->params[':investigation_code'] = $_POST['investigation'][0];
                $investigationComments = \OEModule\OphCiExamination\models\InvestigationComments::model()->findAll($criteria);

                $return = array_map(function ($investigationComment) {
                    return $investigationComment->comments;
                }, $investigationComments);

                $this->renderPartial('/investigationcomment/_investigationcommentDialogOptions', array('investigationComments' => $return), false, false);
            }
        }
    }
}
