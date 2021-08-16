<?php

namespace OEModule\OphCiExamination\controllers;

use Yii;
use OEModule\OphCiExamination\modules;
use OEModule\OphCiExamination\components;
use Audit;

//class OptomFeedbackController extends \BaseController
class OptomFeedbackController extends \BaseEventTypeController
{
    //const ACTION_TYPE_LIST = 'List';
    protected static $FILTER_LIST_KEY = 'OptomFeedbackManager_list_filter';
    private $is_list_filtered = false;
    protected static $action_types = array(
        'list'              => self::ACTION_TYPE_FORM,
        'optomAjaxEdit'     => self::ACTION_TYPE_FORM,
        'getAuditEventLog'  => self::ACTION_TYPE_FORM,
    );

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('list', 'optomAjaxEdit', 'getAuditEventLog'),
                'roles' => array('Optom co-ordinator')
            ),
        );
    }

    protected function beforeAction($action)
    {
        $this->jsVars['OE_MODEL_PREFIX'] = 'OEModule_OphCiExamination_models_';
        return parent::beforeAction($action);
    }

    /**
     *  Optom Feedback manager list screen
     */
    public function actionList()
    {
        $this->layout = '//layouts/main';
        $this->renderPatientPanel = false;

        $filter = $this->getListFilter();
        $examinationLog = new \AutomaticExaminationEventLog();
        $dp = $examinationLog->optomSearch($filter);

        $this->render(
            '/optom/list',
            array(
                'list_filter'   => $filter,
                'model'         => $examinationLog,
                'dp'            => $dp,
                'institution_id'=> \Institution::model()->getCurrent()->id,
                'site_id'       => $this->selectedSiteId
            )
        );
    }

    /**
     *  Optom Feedback manager row save
     */
    public function actionOptomAjaxEdit($id)
    {
        if ($this->request->isPostRequest) {
            $model = \AutomaticExaminationEventLog::model()->findByPk($id);
            if (!$model) {
                $result = json_encode(array(
                    's'     => 0,
                    'msg'   => 'Something went wrong!'
                ));
            } else {
                $model->invoice_status_id = $this->request->getPost('invoice_status_id');
                $model->comment = $this->request->getPost('comment');

                $model->save();

                $result = json_encode(array(
                    's'     => 1,
                    'msg'   => 'Update successful'
                ));
            }

            echo $result;
        }

    }

    /**
     *  Get and set post data value to search fields
     */
    protected function getListFilter()
    {
        $filter = array();

        // if POST, then a new filter is to be applied, otherwise retrieve from the session
        if ($this->request->isPostRequest) {
            foreach (array('date_from', 'date_to', 'status_id', 'patient_number','optometrist', 'goc_number' ) as $key) {
                $val = $this->request->getPost($key, null);
                $filter[$key] = $val;
            }
        } else {
            if ($session_filter = $this->getApp()->session[static::$FILTER_LIST_KEY]) {
                $filter = $session_filter;
            }
        }

        // set the is filtered flag for the controller
        foreach ($filter as $val) {
            if ($val) {
                $this->is_list_filtered = true;
                break;
            }
        }

        // store filter for later use
        $this->getApp()->session[static::$FILTER_LIST_KEY] = $filter;

        return $filter;
    }

    public function isListFiltered()
    {
        return $this->is_list_filtered;
    }

    public function actionGetAuditEventLog($id)
    {
        if ($this->request->isPostRequest) {
            $model = \AutomaticExaminationEventLog::model()->findByPk($id);
            $previousVersions = $model->getPreviousVersions();
            array_unshift($previousVersions, $model);
            $result = $this->renderPartial('/optom/audit_list', array( 'data' => $previousVersions));
        }
        echo $result;
    }
}
