<?php
class ReportController extends BaseReportController
{
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('daReport', 'runreport', 'downloadreport'),
            ),
        );
    }

    public function actionIndex()
    {
        $this->redirect(array('daReport'));
    }

    public function actionDAReport()
    {
        $this->pageTitle = 'PSD Report';
        $this->render('da');
    }
}
