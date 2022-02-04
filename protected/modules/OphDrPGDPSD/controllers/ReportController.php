<?php
class ReportController extends BaseReportController
{
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('psdReport', 'runreport', 'downloadreport'),
            ),
        );
    }

    public function actionIndex()
    {
        $this->redirect(array('psdReport'));
    }

    public function actionPSDReport()
    {
        $this->pageTitle = 'PSD Report';
        $this->render('psd');
    }
}
