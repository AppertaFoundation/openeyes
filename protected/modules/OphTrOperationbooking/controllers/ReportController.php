<?php
class ReportController extends BaseReportController
{
    public function accessRules()
    {
        return array(
        array('allow',
        'actions' => array('EUR', 'runreport', 'downloadreport'),
        ),
        );
    }

    public function actionIndex()
    {
        $this->redirect(array('eur'));
    }

    public function actionEUR()
    {

        $this->pageTitle = 'Effective use of resources (EUR)';
        if ( !Yii::app()->getAuthManager()->checkAccess('Report', Yii::app()->user->id) ) {
          //if the user has no Report role than he/she must be a consultant
            if ($firm->consultant_id !== Yii::app()->user->id) {
                throw new CException("Not authorised: Only for consultant");
            }
        }
        if (@$_GET['date_from'] && date('Y-m-d', strtotime($_GET['date_from']))) {
            $date_from = date('Y-m-d', strtotime($_GET['date_from']));
        }
        if (@$_GET['date_to'] && date('Y-m-d', strtotime($_GET['date_to']))) {
            $date_to = date('Y-m-d', strtotime($_GET['date_to']));
        }

        $this->render('eur', array(
        'consultant' => CHtml::listData(User::model()->findAll(array('condition' => 'is_consultant = 1', 'order' => 'first_name,last_name')), 'id', 'fullname')
        ));
    }
}
