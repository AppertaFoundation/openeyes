<?php
class ReportController extends BaseReportController
{
    public function accessRules()
    {
        return [
            [
                'allow',
                'actions' => ['daReport', 'runreport', 'downloadreport'],
                'expression' => [static::class, 'checkSurgeonOrReportRole']
            ],
        ];
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
